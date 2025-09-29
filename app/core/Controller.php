<?php
class Controller {
  protected $viewData = [];

  protected function render($view, $data = []) {
    $this->viewData = $data;
    extract($data);
    $base = (require __DIR__.'/../config/config.php')['app']['base_url'];
    include __DIR__ . '/../views/layout/header.php';
    include __DIR__ . '/../views/' . $view . '.php';
    include __DIR__ . '/../views/layout/footer.php';
  }

  protected function renderRaw($view, $data = []) {
    // For printable pages (no layout)
    extract($data);
    include __DIR__ . '/../views/' . $view . '.php';
  }

  protected function redirect($path) {
    $base = (require __DIR__.'/../config/config.php')['app']['base_url'];
    header('Location: ' . $base . $path);
    exit;
  }

  protected function isLogged() {
    return isset($_SESSION['user']);
  }

  protected function requireLogin() {
    if (!$this->isLogged()) {
      $this->redirect('/?controller=auth&action=login');
    }
  }

  protected function csrfToken() {
    if (empty($_SESSION['csrf'])) {
      $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
  }

  protected function checkCsrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (empty($_POST['csrf']) || $_POST['csrf'] !== ($_SESSION['csrf'] ?? '')) {
        http_response_code(403);
        die('CSRF token inválido.');
      }
    }
  }
}
