<?php
require_once __DIR__ . '/../core/Database.php';
class User {
  private $db;
  public function __construct() {
    $this->db = Database::getInstance()->pdo();
  }
  public function findByUsername($username) {
    $st = $this->db->prepare('SELECT * FROM users WHERE username = ? AND estado = 1 LIMIT 1');
    $st->execute([$username]);
    return $st->fetch();
  }
}
