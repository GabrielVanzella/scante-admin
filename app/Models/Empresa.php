<?php
namespace App\Models;

use App\Core\Model;

class Empresa extends Model {
    protected string $table = 'empresas';

    public function create(array $data): int {
        $this->db->execute(
            "INSERT INTO empresas (nome, cnpj, email, telefone, contato, ativo) VALUES (?,?,?,?,?,1)",
            [$data['nome'], $data['cnpj'] ?? null, $data['email'], $data['telefone'] ?? null, $data['contato'] ?? null]
        );
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $this->db->execute(
            "UPDATE empresas SET nome=?, cnpj=?, email=?, telefone=?, contato=?, ativo=? WHERE id=?",
            [$data['nome'], $data['cnpj'] ?? null, $data['email'], $data['telefone'] ?? null, $data['contato'] ?? null, $data['ativo'] ?? 1, $id]
        );
    }

    public function comEstatisticas(): array {
        return $this->db->query("
            SELECT e.*,
                COUNT(l.id)                                        AS total_licencas,
                SUM(l.status = 'ativa')                           AS licencas_ativas,
                SUM(l.status = 'trial')                           AS licencas_trial,
                SUM(l.status = 'expirada')                        AS licencas_expiradas
            FROM empresas e
            LEFT JOIN licencas l ON l.empresa_id = e.id
            GROUP BY e.id
            ORDER BY e.nome
        ");
    }
}
