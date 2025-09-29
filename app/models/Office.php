<?php
require_once __DIR__ . '/../core/Database.php';
class Office {
  private $db;
  public function __construct() {
    $this->db = Database::getInstance()->pdo();
  }
  public function all() {
    $st = $this->db->query('SELECT * FROM oficinas ORDER BY nombre');
    return $st->fetchAll();
  }
  public function create($data) {
    $st = $this->db->prepare('INSERT INTO oficinas (nombre, descripcion, estado) VALUES (?,?,?)');
    $st->execute([$data['nombre'], $data['descripcion'] ?? null, $data['estado'] ?? 1]);
  }
}
