<?php
// public/test_db.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/core/Database.php';

try {
  $pdo = Database::getInstance()->pdo();
  echo "<p>✅ Conexión a BD OK</p>";

  // Verifica base de datos y tablas
  $dbName = (require __DIR__ . '/../app/config/config.php')['db']['name'];
  echo "<p>BD: <strong>{$dbName}</strong></p>";

  // ¿Existe users?
  $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
  if (!$stmt->fetch()) {
    echo "<p>❌ La tabla <code>users</code> no existe. Importa <code>sql/schema.sql</code>.</p>";
    exit;
  }

  // ¿Existe admin?
  $stmt = $pdo->prepare("SELECT id, username, nombre, estado, password_hash FROM users WHERE username='admin' LIMIT 1");
  $stmt->execute();
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$admin) {
    echo "<p>❌ No existe usuario <code>admin</code> en <code>users</code>.</p>";
    echo "<p>Usa <code>reset_admin.php</code> (abajo) para crearlo.</p>";
    exit;
  }

  echo "<p>👤 Admin encontrado: id={$admin['id']}, estado={$admin['estado']}</p>";

  // Probar verificación del hash con admin123
  $ok = password_verify('admin123', $admin['password_hash']);
  echo "<p>Verificar password <code>admin123</code>: " . ($ok ? "✅ OK" : "❌ NO COINCIDE") . "</p>";

  if (!$ok) {
    echo "<p>Ejecuta <code>reset_admin.php</code> para restablecer la clave.</p>";
  }

  // Comprobar que la URL del formulario de login es alcanzable
  $base = (require __DIR__ . '/../app/config/config.php')['app']['base_url'];
  $loginUrl = $base . '/?controller=auth&action=login';
  echo "<p>Login POST URL esperado: <code>{$loginUrl}</code></p>";
  echo "<p>Si tu carpeta NO es <code>inventory-mvc</code>, cambia <code>base_url</code> en <code>app/config/config.php</code>.</p>";

} catch (Throwable $e) {
  echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
  echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
