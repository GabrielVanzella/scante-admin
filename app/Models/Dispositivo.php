<?php
namespace App\Models;

use App\Core\Model;

class Dispositivo extends Model {
    protected string $table = 'dispositivos';

    public function ping(string $deviceId, string $deviceNome, string $appVersion, ?string $chave): void {
        $statusLicenca  = 'sem_licenca';
        $licencaId      = null;
        $empresaId      = null;
        $empresaNome    = null;
        $chaveNorm      = null;

        if ($chave) {
            $lic = $this->db->queryOne(
                "SELECT l.id, l.status, l.empresa_id, e.nome AS empresa_nome, l.chave
                 FROM licencas l
                 LEFT JOIN empresas e ON e.id = l.empresa_id
                 WHERE l.chave = ? AND l.device_id = ?",
                [$chave, $deviceId]
            );
            if ($lic) {
                $licencaId   = (int)$lic['id'];
                $empresaId   = $lic['empresa_id'] ? (int)$lic['empresa_id'] : null;
                $empresaNome = $lic['empresa_nome'];
                $statusLicenca = $lic['status'];
                $chaveNorm   = $lic['chave'];
            }
        }

        $this->db->execute("
            INSERT INTO dispositivos
                (device_id, device_nome, app_version, licenca_id, empresa_id, empresa_nome, status_licenca, chave_licenca, primeiro_acesso, ultimo_acesso)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE
                device_nome    = VALUES(device_nome),
                app_version    = VALUES(app_version),
                licenca_id     = VALUES(licenca_id),
                empresa_id     = VALUES(empresa_id),
                empresa_nome   = VALUES(empresa_nome),
                status_licenca = VALUES(status_licenca),
                chave_licenca  = VALUES(chave_licenca),
                ultimo_acesso  = NOW()
        ", [$deviceId, $deviceNome, $appVersion, $licencaId, $empresaId, $empresaNome, $statusLicenca, $chaveNorm]);
    }

    public function listar(): array {
        return $this->db->query(
            "SELECT * FROM dispositivos ORDER BY ultimo_acesso DESC"
        );
    }

    public function estatisticas(): array {
        $total      = (int)($this->db->queryOne("SELECT COUNT(*) AS n FROM dispositivos")['n'] ?? 0);
        $online     = (int)($this->db->queryOne("SELECT COUNT(*) AS n FROM dispositivos WHERE ultimo_acesso >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)")['n'] ?? 0);
        $comLicenca = (int)($this->db->queryOne("SELECT COUNT(*) AS n FROM dispositivos WHERE status_licenca = 'ativa'")['n'] ?? 0);
        $semLicenca = (int)($this->db->queryOne("SELECT COUNT(*) AS n FROM dispositivos WHERE status_licenca = 'sem_licenca'")['n'] ?? 0);
        return compact('total', 'online', 'comLicenca', 'semLicenca');
    }
}
