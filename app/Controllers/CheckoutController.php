<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Licenca;
use App\Models\Empresa;

class CheckoutController extends Controller {

    private array $precos = [
        'mensal'    => PRECO_MENSAL,
        'anual'     => PRECO_ANUAL,
        'vitalicia' => PRECO_VITALICIA,
    ];

    private array $labels = [
        'mensal'    => 'Mensal',
        'anual'     => 'Anual',
        'vitalicia' => 'Vitalícia',
    ];

    private array $dias = [
        'mensal'    => 30,
        'anual'     => 365,
        'vitalicia' => null,
    ];

    public function index(): void {
        $deviceId   = trim($_GET['device_id']   ?? '');
        $deviceNome = trim($_GET['device_nome'] ?? '');
        $empresaId  = (int)($_GET['empresa_id'] ?? 0) ?: null;
        $email      = trim($_GET['email'] ?? '');

        $empresas = (new Empresa())->findAll('nome ASC');

        $this->view('checkout.index', [
            'deviceId'        => $deviceId,
            'deviceNome'      => $deviceNome,
            'empresaId'       => $empresaId,
            'empresas'        => $empresas,
            'dados'           => ['email' => $email],
            'erro'            => null,
            'novaEmpresaNome' => null,
        ], 'checkout');
    }

    public function processar(): void {
        $deviceId   = trim($this->input('device_id', ''));
        $deviceNome = trim($this->input('device_nome', ''));
        $email      = trim($this->input('email', ''));
        $telefone   = trim($this->input('telefone', ''));
        $tipo       = $this->input('tipo', 'mensal');
        $empresaId  = (int)$this->input('empresa_id') ?: null;

        $novaEmpresaNome     = trim($this->input('nova_empresa_nome', ''));
        $novaEmpresaCnpj     = trim($this->input('nova_empresa_cnpj', ''));
        $novaEmpresaTelefone = trim($this->input('nova_empresa_telefone', ''));
        $novaEmpresaContato  = trim($this->input('nova_empresa_contato', ''));

        $empresas = (new Empresa())->findAll('nome ASC');

        if (!$email) {
            $this->view('checkout.index', [
                'deviceId' => $deviceId, 'deviceNome' => $deviceNome,
                'empresaId' => $empresaId, 'empresas' => $empresas,
                'dados' => compact('email', 'telefone', 'tipo'),
                'erro' => 'O e-mail é obrigatório.', 'novaEmpresaNome' => null,
            ], 'checkout');
            return;
        }

        if (!isset($this->precos[$tipo])) {
            $tipo = 'mensal';
        }

        if ($novaEmpresaNome && !$empresaId) {
            $empresaId = (new Empresa())->create([
                'nome'     => $novaEmpresaNome,
                'cnpj'     => $novaEmpresaCnpj ?: null,
                'email'    => $email,
                'telefone' => $novaEmpresaTelefone ?: $telefone ?: null,
                'contato'  => $novaEmpresaContato ?: null,
            ]);
        }

        $licencaId = (new Licenca())->criarPendente($empresaId, $tipo, $deviceId, $deviceNome, $email, $telefone);
        $token     = $this->gerarToken($licencaId);

        $this->redirectTo(APP_URL . '/checkout/pagamento?id=' . $licencaId . '&h=' . $token);
    }

    public function pagamento(): void {
        $licencaId = (int)($_GET['id'] ?? 0);
        $token     = $_GET['h'] ?? '';

        if (!$licencaId || !hash_equals($this->gerarToken($licencaId), $token)) {
            $this->redirectTo(APP_URL . '/checkout');
            return;
        }

        $licenca = (new Licenca())->findById($licencaId);
        if (!$licenca || $licenca['status'] !== 'pendente') {
            $this->redirectTo(APP_URL . '/checkout/sucesso');
            return;
        }

        $tipo   = $licenca['tipo'];
        $valor  = $this->precos[$tipo]  ?? PRECO_MENSAL;
        $label  = $this->labels[$tipo]  ?? ucfirst($tipo);
        $diasV  = $this->dias[$tipo];

        $this->view('checkout.pagamento', [
            'licenca'    => $licenca,
            'licencaId'  => $licencaId,
            'token'      => $token,
            'tipo'       => $tipo,
            'label'      => $label,
            'valor'      => $valor,
            'dias'       => $diasV,
            'mpToken'    => MP_ACCESS_TOKEN,
        ], 'checkout');
    }

    public function pagar(): void {
        $licencaId = (int)$this->input('licenca_id');
        $token     = $this->input('token', '');

        if (!$licencaId || !hash_equals($this->gerarToken($licencaId), $token)) {
            $this->redirectTo(APP_URL . '/checkout');
            return;
        }

        $licenca = (new Licenca())->findById($licencaId);
        if (!$licenca || $licenca['status'] !== 'pendente') {
            $this->redirectTo(APP_URL . '/checkout/sucesso');
            return;
        }

        $tipo  = $licenca['tipo'];
        $valor = $this->precos[$tipo] ?? PRECO_MENSAL;
        $model = new Licenca();

        if (!MP_ACCESS_TOKEN) {
            // Modo desenvolvimento: ativa direto
            $model->ativarAposPagamento($licencaId, $tipo, 'DEV-' . $licencaId);
            $this->redirectTo(APP_URL . '/checkout/sucesso');
            return;
        }

        // Cria preferência no Mercado Pago e redireciona
        $url = $this->criarPreferenciaMp(
            $licencaId, $tipo, $valor,
            $licenca['device_nome'] ?? 'ScanTE'
        );

        if ($url) {
            $this->redirectTo($url);
        } else {
            $this->redirectTo(APP_URL . '/checkout/pagamento?id=' . $licencaId . '&h=' . $token . '&erro=1');
        }
    }

    public function sucesso(): void {
        $this->view('checkout.sucesso', [], 'checkout');
    }

    public function cancelado(): void {
        $this->view('checkout.cancelado', [], 'checkout');
    }

    private function gerarToken(int $licencaId): string {
        return substr(hash_hmac('sha256', (string)$licencaId, API_SECRET), 0, 20);
    }

    private function criarPreferenciaMp(int $licencaId, string $tipo, float $valor, string $deviceNome): ?string {
        $licenca = (new Licenca())->findById($licencaId);
        $email   = $licenca['email'] ?? 'comprador@email.com';

        $body = [
            'items' => [[
                'title'       => 'ScanTE — Licença ' . ucfirst($tipo),
                'quantity'    => 1,
                'unit_price'  => $valor,
                'currency_id' => 'BRL',
            ]],
            'payer'              => ['email' => $email],
            'external_reference' => (string)$licencaId,
            'metadata'           => ['tipo' => $tipo, 'device_nome' => $deviceNome],
            'back_urls'          => [
                'success' => APP_URL . '/checkout/sucesso',
                'failure' => APP_URL . '/checkout/cancelado',
                'pending' => APP_URL . '/checkout/sucesso',
            ],
            'auto_return'      => 'approved',
            'notification_url' => APP_URL . '/api/webhook/mercadopago',
        ];

        $ch = curl_init('https://api.mercadopago.com/checkout/preferences');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($body),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . MP_ACCESS_TOKEN,
            ],
        ]);
        $resp = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $resp['init_point'] ?? null;
    }
}
