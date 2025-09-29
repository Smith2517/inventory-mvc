<?php
class App {
  public function run() {
    session_start();
    $controller = $_GET['controller'] ?? 'inventory';
    $action = $_GET['action'] ?? 'index';

    $controllerClass = ucfirst($controller) . 'Controller';
    $controllerFile = __DIR__ . '/../controllers/' . $controllerClass . '.php';

    if (!file_exists($controllerFile)) {
      http_response_code(404);
      echo 'Controlador no encontrado';
      exit;
    }
    require_once $controllerFile;
    $obj = new $controllerClass();
    if (!method_exists($obj, $action)) {
      http_response_code(404);
      echo 'Acción no encontrada';
      exit;
    }
    $obj->$action();
  }
}
