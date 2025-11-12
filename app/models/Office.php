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
  public function find($id) {
    $st = $this->db->prepare('SELECT * FROM oficinas WHERE id = ?');
    $st->execute([intval($id)]);
    return $st->fetch();
  }
  public function create($data) {
    $st = $this->db->prepare('INSERT INTO oficinas (nombre, descripcion, estado) VALUES (?,?,?)');
    $st->execute([$data['nombre'], $data['descripcion'] ?? null, $data['estado'] ?? 1]);
    return $this->db->lastInsertId();
  }
  public function update($id, $data) {
    $st = $this->db->prepare('UPDATE oficinas SET nombre = ?, descripcion = ?, estado = ? WHERE id = ?');
    return $st->execute([$data['nombre'], $data['descripcion'], $data['estado'], intval($id)]);
  }
  public function delete($id) {
    $st = $this->db->prepare('DELETE FROM oficinas WHERE id = ?');
    return $st->execute([intval($id)]);
  }
}
