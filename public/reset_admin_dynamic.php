<?php
// public/reset_admin_dynamic.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../app/core/Database.php';

try {
  $pdo = Database::getInstance()->pdo();

  // 1) Asegura que la columna pueda guardar hashes completos
  $pdo->exec("ALTER TABLE users MODIFY password_hash VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL");

  // 2) Genera un hash NUEVO en ESTE servidor (evita problemas de copiado/encoding)
  $hash = password_hash('admin123', PASSWORD_BCRYPT);

  // 3) Quita posibles espacios invisibles previos
  $pdo->prepare("UPDATE users SET password_hash = TRIM(password_hash) WHERE username='admin'")->execute();

  // 4) Inserta/actualiza admin
  $exists = $pdo->query("SELECT id FROM users WHERE username='admin' LIMIT 1")->fetchColumn();
  if ($exists) {
    $upd = $pdo->prepare("UPDATE users SET password_hash=?, estado=1 WHERE username='admin'");
    $upd->execute([$hash]);
    echo "<p>🔁 Admin reseteado con hash local. Usuario: <strong>admin</strong> | Clave: <strong>admin123</strong></p>";
  } else {
    $ins = $pdo->prepare("INSERT INTO users (username, password_hash, nombre, estado) VALUES ('admin', ?, 'Administrador', 1)");
    $ins->execute([$hash]);
    echo "<p>🆕 Admin creado con hash local. Usuario: <strong>admin</strong> | Clave: <strong>admin123</strong></p>";
  }

  echo '<p>Ahora abre <code>/inventory-mvc/public/test_db.php</code> y verifica que diga "Verificar password admin123: ✅ OK".</p>';

} catch (Throwable $e) {
  echo "<p>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
  echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
