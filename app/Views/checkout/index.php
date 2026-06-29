<div class="checkout-card">
  <div class="checkout-card-header">
    <h5><i class="bi bi-shield-check me-2"></i>Ativar Licença ScanTE</h5>
    <p>Preencha seus dados para continuar com o pagamento</p>
  </div>
  <div class="checkout-card-body">

    <?php if ($deviceNome): ?>
    <div class="badge-device d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-phone-fill text-success"></i>
      <div>
        <div class="fw-semibold"><?= htmlspecialchars($deviceNome) ?></div>
        <div style="font-size:.75rem;opacity:.6">Dispositivo identificado</div>
      </div>
    </div>
    <?php endif; ?>

    <?php if ($erro): ?>
    <div class="alert alert-danger alert-sm py-2 mb-3" style="font-size:.85rem">
      <i class="bi bi-exclamation-triangle me-1"></i><?= htmlspecialchars($erro) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= APP_URL ?>/checkout" id="formCheckout">
      <input type="hidden" name="device_id"   value="<?= htmlspecialchars($deviceId) ?>">
      <input type="hidden" name="device_nome" value="<?= htmlspecialchars($deviceNome) ?>">
      <input type="hidden" name="empresa_id"  id="hiddenEmpresaId" value="<?= htmlspecialchars($empresaId ?? '') ?>">

      <div class="mb-3">
        <label class="form-label">E-mail <span class="text-danger">*</span></label>
        <input type="email" name="email" class="form-control" placeholder="seu@email.com"
               value="<?= htmlspecialchars($dados['email'] ?? '') ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Telefone / WhatsApp</label>
        <input type="tel" name="telefone" class="form-control" placeholder="(11) 99999-9999"
               value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Empresa</label>
        <?php if ($empresas): ?>
        <select name="empresa_select" class="form-select mb-2" id="selectEmpresa">
          <option value="">— sem empresa / selecionar depois —</option>
          <?php foreach ($empresas as $e): ?>
            <option value="<?= $e['id'] ?>" <?= ($empresaId == $e['id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($e['nome']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php endif; ?>

        <button type="button" class="btn btn-outline-secondary btn-sm w-100"
          data-bs-toggle="modal" data-bs-target="#modalNovaEmpresa">
          <i class="bi bi-plus-circle me-1"></i>Cadastrar nova empresa
        </button>
        <?php if (!empty($novaEmpresaNome)): ?>
        <div class="mt-2 badge-device">
          <i class="bi bi-building text-success me-1"></i>
          <strong><?= htmlspecialchars($novaEmpresaNome) ?></strong> será cadastrada ao confirmar
        </div>
        <?php endif; ?>
      </div>

      <div class="mb-4">
        <label class="form-label">Tipo de licença</label>
        <select name="tipo" class="form-select" id="selectTipo" required>
          <option value="mensal" <?= ($dados['tipo'] ?? '') === 'mensal' ? 'selected' : '' ?>>
            Mensal — R$ <?= number_format(PRECO_MENSAL, 2, ',', '.') ?>/mês
          </option>
          <option value="anual" <?= ($dados['tipo'] ?? '') === 'anual' ? 'selected' : '' ?>>
            Anual — R$ <?= number_format(PRECO_ANUAL, 2, ',', '.') ?>/ano
          </option>
          <option value="vitalicia" <?= ($dados['tipo'] ?? '') === 'vitalicia' ? 'selected' : '' ?>>
            Vitalícia — R$ <?= number_format(PRECO_VITALICIA, 2, ',', '.') ?> (pagamento único)
          </option>
        </select>
      </div>

      <div class="mb-4 p-3 rounded-3 text-center" style="background:#f0fdf9;border:1px solid #a7f3d0">
        <div class="text-muted small">Valor a pagar</div>
        <div class="fw-bold fs-4 text-success" id="valorExibido">—</div>
      </div>

      <button type="submit" class="btn-pay">
        <i class="bi bi-lock-fill me-2"></i>Continuar para o pagamento
      </button>
    </form>

    <div class="divider">pagamento seguro</div>
    <div class="security-note text-center">
      <i class="bi bi-shield-fill-check text-success me-1"></i>
      Pagamento processado pelo <strong>Mercado Pago</strong>. Seus dados estão protegidos.
    </div>
  </div>
</div>

<!-- Modal nova empresa -->
<div class="modal fade" id="modalNovaEmpresa" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cadastrar empresa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label">Nome da empresa <span class="text-danger">*</span></label>
            <input type="text" id="novaEmpresaNome" class="form-control" placeholder="Ex: Empresa ABC Ltda">
          </div>
          <div class="col-md-6">
            <label class="form-label">CNPJ</label>
            <input type="text" id="novaEmpresaCnpj" class="form-control" placeholder="00.000.000/0000-00">
          </div>
          <div class="col-md-6">
            <label class="form-label">Telefone</label>
            <input type="text" id="novaEmpresaTelefone" class="form-control" placeholder="(11) 99999-9999">
          </div>
          <div class="col-12">
            <label class="form-label">Responsável</label>
            <input type="text" id="novaEmpresaContato" class="form-control" placeholder="Nome do responsável">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-accent" id="btnConfirmarEmpresa" style="background:var(--accent);color:#0F2A3D;font-weight:700;border:none;padding:8px 20px;border-radius:8px">
          Confirmar empresa
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Campos ocultos para nova empresa -->
<input type="hidden" name="nova_empresa_nome"     form="formCheckout" id="hNome">
<input type="hidden" name="nova_empresa_cnpj"     form="formCheckout" id="hCnpj">
<input type="hidden" name="nova_empresa_telefone" form="formCheckout" id="hTelefone">
<input type="hidden" name="nova_empresa_contato"  form="formCheckout" id="hContato">

<script>
const precos = {
  mensal:   <?= PRECO_MENSAL ?>,
  anual:    <?= PRECO_ANUAL ?>,
  vitalicia:<?= PRECO_VITALICIA ?>
};

function formatBRL(v) {
  return 'R$ ' + v.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function atualizarValor() {
  const tipo = document.getElementById('selectTipo').value;
  document.getElementById('valorExibido').textContent = formatBRL(precos[tipo] || 0);
}

document.getElementById('selectTipo').addEventListener('change', atualizarValor);
atualizarValor();

// Empresa select → atualiza hidden
document.getElementById('selectEmpresa')?.addEventListener('change', function () {
  document.getElementById('hiddenEmpresaId').value = this.value;
  // Se selecionou empresa existente, limpa nova empresa
  if (this.value) {
    ['hNome','hCnpj','hTelefone','hContato'].forEach(id => document.getElementById(id).value = '');
  }
});

// Confirmar nova empresa no modal
document.getElementById('btnConfirmarEmpresa').addEventListener('click', function () {
  const nome = document.getElementById('novaEmpresaNome').value.trim();
  if (!nome) { alert('Informe o nome da empresa.'); return; }

  document.getElementById('hNome').value     = nome;
  document.getElementById('hCnpj').value     = document.getElementById('novaEmpresaCnpj').value;
  document.getElementById('hTelefone').value = document.getElementById('novaEmpresaTelefone').value;
  document.getElementById('hContato').value  = document.getElementById('novaEmpresaContato').value;

  // Limpa seleção de empresa existente
  document.getElementById('hiddenEmpresaId').value = '';
  const sel = document.getElementById('selectEmpresa');
  if (sel) sel.value = '';

  // Fecha modal e exibe confirmação
  bootstrap.Modal.getInstance(document.getElementById('modalNovaEmpresa')).hide();

  // Atualiza badge
  let badge = document.querySelector('.badge-nova-empresa');
  if (!badge) {
    badge = document.createElement('div');
    badge.className = 'mt-2 badge-device badge-nova-empresa';
    document.getElementById('btnConfirmarEmpresa').closest('.modal').previousElementSibling && null;
    document.querySelector('[name="empresa_select"]')?.closest('.mb-3')
      ?.querySelector('button')?.insertAdjacentElement('afterend', badge);
  }
  badge.innerHTML = '<i class="bi bi-building text-success me-1"></i><strong>' + nome + '</strong> será cadastrada ao confirmar';
});
</script>
