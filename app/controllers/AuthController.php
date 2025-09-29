<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/User.php';

class AuthController extends Controller {
  public function login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->checkCsrf();
      $u = new User();
      $user = $u->findByUsername(trim($_POST['username'] ?? ''));
      if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        $_SESSION['user'] = ['id' => $user['id'], 'username' => $user['username'], 'nombre' => $user['nombre']];
        $this->redirect('/?controller=inventory&action=index');
      } else {
        $this->render('auth/login', ['error' => 'Credenciales inválidas', 'csrf' => $this->csrfToken()]);
      }
      return;
    }
    $this->render('auth/login', ['csrf' => $this->csrfToken()]);
  }

  public function logout() {
    session_destroy();
    $this->redirect('/?controller=auth&action=login');
  }
}
