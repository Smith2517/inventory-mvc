<?php
require_once __DIR__ . '/../core/Database.php';
class Movement {
  private $db;
  public function __construct() {
    $this->db = Database::getInstance()->pdo();
  }
  public function create($data) {
    $st = $this->db->prepare('INSERT INTO movimientos (inventario_id, tipo, cantidad, motivo, oficina_id, user_id) VALUES (?,?,?,?,?,?)');
    $st->execute([
      $data['inventario_id'], $data['tipo'], (int)$data['cantidad'], $data['motivo'] ?? null,
      $data['oficina_id'] ?: null, $data['user_id']
    ]);
  }
  public function byInventory($inventario_id) {
    $st = $this->db->prepare('SELECT m.*, o.nombre AS oficina, u.username AS usuario
                              FROM movimientos m
                              LEFT JOIN oficinas o ON m.oficina_id = o.id
                              LEFT JOIN users u ON m.user_id = u.id
                              WHERE inventario_id = ?
                              ORDER BY m.created_at DESC');
    $st->execute([$inventario_id]);
    return $st->fetchAll();
  }
  public function betweenDates($from, $to) {
    $st = $this->db->prepare('SELECT m.*, i.codigo, i.nombre AS item, o.nombre AS oficina, u.username AS usuario
                              FROM movimientos m
                              INNER JOIN inventario i ON m.inventario_id = i.id
                              LEFT JOIN oficinas o ON m.oficina_id = o.id
                              LEFT JOIN users u ON m.user_id = u.id
                              WHERE m.created_at BETWEEN ? AND ?
                              ORDER BY m.created_at DESC');
    $st->execute([$from, $to]);
    return $st->fetchAll();
  }
}
