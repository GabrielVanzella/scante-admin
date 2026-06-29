<?php
$erroMp = isset($_GET['erro']) && $_GET['erro'] === '1';

$icones = [
    'mensal'    => 'bi-calendar-month',
    'anual'     => 'bi-calendar-check',
    'vitalicia' => 'bi-infinity',
];
$icone = $icones[$tipo] ?? 'bi-key';

$descricoes = [
    'mensal'    => '30 dias de acesso',
    'anual'     => '365 dias de acesso',
    'vitalicia' => 'Acesso permanente, sem renovação',
];
$descricao = $descricoes[$tipo] ?? '';

$beneficios = [
    'mensal'    => ['Acesso por 30 dias', 'Suporte por e-mail', 'Atualizações incluídas'],
    'anual'     => ['Acesso por 1 ano', 'Suporte prioritário', 'Atualizações incluídas', 'Economia de 44% vs mensal'],
    'vitalicia' => ['Acesso vitalício', 'Suporte prioritário', 'Todas as atualizações futuras', 'Pague uma vez, use para sempre'],
];
$listaBeneficios = $beneficios[$tipo] ?? [];

$valorFormatado = 'R$ ' . number_format($valor, 2, ',', '.');
?>

<?php if ($erroMp): ?>
<div class="alert alert-danger alert-dismissible fade show mb-0 rounded-0" role="alert">
  <i class="bi bi-exclamation-circle me-2"></i>
  Não foi possível conectar com o Mercado Pago. Tente novamente ou entre em contato.
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="checkout-page">
  <div class="container" style="max-width:960px; padding: 2rem 1rem;">

    <!-- Progress bar -->
    <div class="progress-steps mb-4">
      <div class="step done">
        <div class="step-circle"><i class="bi bi-check-lg"></i></div>
        <span>Dados</span>
      </div>
      <div class="step-line done"></div>
      <div class="step active">
        <div class="step-circle">2</div>
        <span>Pagamento</span>
      </div>
      <div class="step-line"></div>
      <div class="step">
        <div class="step-circle">3</div>
        <span>Confirmação</span>
      </div>
    </div>

    <div class="row g-4 align-items-start">

      <!-- Coluna esquerda: Resumo do pedido -->
      <div class="col-lg-5">
        <div class="card order-card h-100">
          <div class="card-body p-4">
            <h6 class="text-uppercase fw-bold text-muted mb-4" style="letter-spacing:.08em; font-size:.75rem;">
              Resumo do pedido
            </h6>

            <!-- Produto -->
            <div class="product-box mb-4">
              <div class="product-icon">
                <i class="bi <?= $icone ?>"></i>
              </div>
              <div>
                <div class="fw-bold">ScanTE — Licença <?= htmlspecialchars($label) ?></div>
                <div class="text-muted small"><?= htmlspecialchars($descricao) ?></div>
              </div>
            </div>

            <!-- Benefícios -->
            <ul class="benefit-list mb-4">
              <?php foreach ($listaBeneficios as $b): ?>
              <li><i class="bi bi-check-circle-fill text-success me-2"></i><?= htmlspecialchars($b) ?></li>
              <?php endforeach; ?>
            </ul>

            <!-- Dispositivo -->
            <?php if (!empty($licenca['device_nome']) || !empty($licenca['device_id'])): ?>
            <div class="device-box mb-4">
              <i class="bi bi-phone-fill me-2 text-muted"></i>
              <div>
                <div class="small fw-semibold"><?= htmlspecialchars($licenca['device_nome'] ?: 'Dispositivo') ?></div>
                <div class="text-muted" style="font-size:.8rem; font-family:monospace"><?= htmlspecialchars($licenca['device_id'] ?? '') ?></div>
              </div>
            </div>
            <?php endif; ?>

            <hr class="my-3">

            <!-- Total -->
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">Total</span>
              <div class="text-end">
                <div class="price-total"><?= $valorFormatado ?></div>
                <div class="text-muted" style="font-size:.78rem;">pagamento único</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna direita: Pagamento -->
      <div class="col-lg-7">
        <div class="card payment-card">
          <div class="card-body p-4">

            <?php if (!$mpToken): ?>
            <!-- MODO DESENVOLVIMENTO: botão simples de simulação -->
            <div class="alert alert-warning d-flex gap-2 align-items-start mb-4" style="font-size:.85rem;">
              <i class="bi bi-tools mt-1"></i>
              <div>
                <strong>Modo desenvolvimento</strong><br>
                O Mercado Pago não está configurado. O clique em "Pagar" ativa a licença diretamente.
              </div>
            </div>

            <h5 class="fw-bold mb-4">Confirmar pagamento</h5>
            <div class="review-box mb-4">
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span class="text-muted">E-mail</span>
                <span class="fw-semibold"><?= htmlspecialchars($licenca['email'] ?? '') ?></span>
              </div>
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span class="text-muted">Plano</span>
                <span class="fw-semibold">Licença <?= htmlspecialchars($label) ?></span>
              </div>
              <div class="d-flex justify-content-between align-items-center py-2">
                <span class="text-muted fw-bold">Total</span>
                <span class="fw-bold fs-5"><?= $valorFormatado ?></span>
              </div>
            </div>

            <form method="POST" action="<?= APP_URL ?>/checkout/pagar">
              <input type="hidden" name="licenca_id" value="<?= $licencaId ?>">
              <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
              <button type="submit" class="btn btn-pay w-100">
                <i class="bi bi-lock-fill me-2"></i>Ativar licença — <?= $valorFormatado ?>
              </button>
            </form>

            <?php else: ?>
            <!-- PRODUÇÃO: redireciona para Mercado Pago -->
            <div class="d-flex align-items-center gap-3 mb-4">
              <div class="mp-logo-wrap">
                <img src="https://http2.mlstatic.com/frontend-assets/mp-web-navigation/ui-navigation/5.21.22/mercadopago/logo__large@2x.png"
                     alt="Mercado Pago" style="height:28px; object-fit:contain;">
              </div>
              <div class="text-muted small">Pagamento seguro via Mercado Pago</div>
            </div>

            <h5 class="fw-bold mb-2">Prosseguir para pagamento</h5>
            <p class="text-muted small mb-4">
              Você será redirecionado para o ambiente seguro do Mercado Pago para concluir
              o pagamento de <strong><?= $valorFormatado ?></strong>.
            </p>

            <div class="pay-methods mb-4">
              <span class="badge bg-light text-dark border"><i class="bi bi-credit-card me-1"></i>Cartão</span>
              <span class="badge bg-light text-dark border"><i class="bi bi-qr-code me-1"></i>Pix</span>
              <span class="badge bg-light text-dark border"><i class="bi bi-receipt me-1"></i>Boleto</span>
            </div>

            <form method="POST" action="<?= APP_URL ?>/checkout/pagar" id="formPagar">
              <input type="hidden" name="licenca_id" value="<?= $licencaId ?>">
              <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
              <button type="submit" class="btn btn-pay w-100" id="btnPagar">
                <i class="bi bi-lock-fill me-2"></i>Pagar <?= $valorFormatado ?> com segurança
              </button>
            </form>
            <?php endif; ?>

            <!-- Segurança -->
            <div class="security-note mt-4">
              <i class="bi bi-shield-check me-1"></i>
              Compra 100% segura · Seus dados são protegidos
            </div>
          </div>
        </div>

        <!-- Voltar -->
        <div class="text-center mt-3">
          <a href="<?= APP_URL ?>/checkout" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i>Alterar dados
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

<style>
/* Progress steps */
.progress-steps {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0;
}
.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  font-size: .78rem;
  color: rgba(255,255,255,.45);
  font-weight: 500;
}
.step-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: rgba(255,255,255,.12);
  border: 2px solid rgba(255,255,255,.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: .85rem;
  font-weight: 700;
}
.step.active .step-circle {
  background: #3b82f6;
  border-color: #3b82f6;
  color: #fff;
  box-shadow: 0 0 0 4px rgba(59,130,246,.25);
}
.step.active { color: #fff; }
.step.done .step-circle {
  background: #10b981;
  border-color: #10b981;
  color: #fff;
}
.step.done { color: rgba(255,255,255,.7); }
.step-line {
  flex: 1;
  max-width: 80px;
  height: 2px;
  background: rgba(255,255,255,.15);
  margin: 0 8px;
  margin-bottom: 22px;
}
.step-line.done { background: #10b981; }

/* Cards */
.order-card {
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 16px;
  color: #fff;
  backdrop-filter: blur(8px);
}
.payment-card {
  background: #fff;
  border-radius: 16px;
  border: none;
  box-shadow: 0 8px 40px rgba(0,0,0,.35);
  color: #111;
}

/* Product box */
.product-box {
  display: flex;
  align-items: center;
  gap: 14px;
  background: rgba(255,255,255,.06);
  border-radius: 12px;
  padding: 14px;
  border: 1px solid rgba(255,255,255,.1);
}
.product-icon {
  width: 46px;
  height: 46px;
  border-radius: 12px;
  background: linear-gradient(135deg, #3b82f6, #7c3aed);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
  color: #fff;
  flex-shrink: 0;
}

/* Benefit list */
.benefit-list {
  list-style: none;
  padding: 0;
  margin: 0;
}
.benefit-list li {
  padding: 4px 0;
  font-size: .875rem;
  color: rgba(255,255,255,.8);
}

/* Device box */
.device-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(255,255,255,.05);
  border-radius: 10px;
  padding: 10px 14px;
  border: 1px solid rgba(255,255,255,.08);
  font-size: .85rem;
}

/* Price */
.price-total {
  font-size: 1.5rem;
  font-weight: 800;
  color: #fff;
}

/* Payment form side */
.review-box {
  background: #f8fafc;
  border-radius: 10px;
  padding: 4px 14px;
  border: 1px solid #e5e7eb;
}

/* Pay methods */
.pay-methods {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}
.pay-methods .badge {
  font-size: .8rem;
  padding: 6px 10px;
  font-weight: 500;
}

/* Pay button */
.btn-pay {
  background: linear-gradient(135deg, #3b82f6, #7c3aed);
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 14px;
  font-size: 1rem;
  font-weight: 700;
  letter-spacing: .02em;
  transition: opacity .2s, transform .1s;
}
.btn-pay:hover { opacity: .9; color: #fff; }
.btn-pay:active { transform: scale(.98); }
.btn-pay:disabled { opacity: .6; }

/* Security note */
.security-note {
  text-align: center;
  color: #6b7280;
  font-size: .8rem;
}

/* MP logo */
.mp-logo-wrap {
  background: #f3f4f6;
  border-radius: 8px;
  padding: 6px 10px;
  display: flex;
  align-items: center;
}
</style>

<?php if ($mpToken): ?>
<script>
document.getElementById('formPagar').addEventListener('submit', function() {
  const btn = document.getElementById('btnPagar');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Redirecionando...';
});
</script>
<?php endif; ?>
