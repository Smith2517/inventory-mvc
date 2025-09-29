<?php
class Database {
  private static $instance = null;
  private $pdo;

  private function __construct($config) {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset={$config['charset']}";
    $options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $this->pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
  }

  public static function getInstance() {
    if (self::$instance === null) {
      $cfg = require __DIR__ . '/../config/config.php';
      self::$instance = new Database($cfg['db']);
    }
    return self::$instance;
  }

  public function pdo() {
    return $this->pdo;
  }
}
