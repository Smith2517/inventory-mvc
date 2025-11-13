<?php
require_once __DIR__ . '/../core/Database.php';
class User {
  private $db;
  public function __construct() {
    $this->db = Database::getInstance()->pdo();
  }
  public function all() {
    $st = $this->db->query('SELECT * FROM users WHERE estado = 1 ORDER BY nombre, username');
    return $st->fetchAll();
  }
  public function find($id) {
    $st = $this->db->prepare('SELECT * FROM users WHERE id = ? AND estado = 1 LIMIT 1');
    $st->execute([intval($id)]);
    return $st->fetch();
  }
  public function findByUsername($username) {
    $st = $this->db->prepare('SELECT * FROM users WHERE username = ? AND estado = 1 LIMIT 1');
    $st->execute([$username]);
    return $st->fetch();
  }
}
