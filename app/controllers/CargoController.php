<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Cargo.php';
require_once __DIR__ . '/../models/Inventory.php';

class CargoController extends Controller {
  
  public function index() {
    $this->requireLogin();
    $cargo = new Cargo();
    $cargos = $cargo->all();
    
    $this->render('cargo/index', ['cargos' => $cargos, 'csrf' => $this->csrfToken()]);
  }
  
  public function active() {
    $this->requireLogin();
    $isPartial = !empty($_GET['partial']); // para modal/iframe
    
    $cargo = new Cargo();
    $cargos = $cargo->getActiveCargos();
    
    if ($isPartial) {
      // Mostrar contenido para modal
      extract(['cargos' => $cargos, 'csrf' => $this->csrfToken()], EXTR_SKIP);
      require __DIR__ . '/../views/cargo/active.php';
      return;
    }
    
    $this->render('cargo/active', ['cargos' => $cargos, 'csrf' => $this->csrfToken()]);
  }
  
  public function create() {
    $this->requireLogin();
    $isPartial = !empty($_GET['partial']); // para modal/iframe
    
    $cargo = new Cargo();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->checkCsrf();

      $inventario_id = (int)($_POST['inventario_id'] ?? 0);
      $oficina_destino = trim($_POST['oficina_destino'] ?? '');
      $usuario_destino = trim($_POST['usuario_destino'] ?? '');
      $cantidad = (int)($_POST['cantidad'] ?? 0);
      $fecha_inicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
      $motivo = trim($_POST['motivo'] ?? '');

      // Validaciones
      $errors = [];
      if ($inventario_id <= 0) {
        $errors['inventario_id'] = 'Debe seleccionar un ítem de inventario.';
      }
      if (empty($usuario_destino)) {
        $errors['usuario_destino'] = 'El nombre del usuario que recibe es obligatorio.';
      }
      if ($cantidad <= 0) {
        $errors['cantidad'] = 'La cantidad debe ser mayor a 0.';
      }

      if (empty($errors)) {
        // Verificar disponibilidad usando el modelo de Inventory
        $inv = new Inventory();
        $inventario = $inv->find($inventario_id);
        if (!$inventario || $inventario['cantidad'] < $cantidad) {
          $errors['cantidad'] = 'Cantidad no disponible. Stock actual: ' . ($inventario ? $inventario['cantidad'] : 0);
        }
      }

      if (!empty($errors)) {
        if ($isPartial) {
          // Enviar error para modal
          $_SESSION['flash_errors'] = $errors;
          $this->render('cargo/create', [
            'form_data' => $_POST,
            'csrf' => $this->csrfToken(),
            'isPartial' => true
          ]);
          return;
        }
        $_SESSION['flash_errors'] = $errors;
        $this->render('cargo/create', [
          'form_data' => $_POST,
          'csrf' => $this->csrfToken()
        ]);
        return;
      }

      try {
        $id = $cargo->create([
          'inventario_id' => $inventario_id,
          'oficina_destino' => $oficina_destino,
          'usuario_destino' => $usuario_destino,
          'cantidad' => $cantidad,
          'fecha_inicio' => $fecha_inicio,
          'motivo' => $motivo
        ]);

        if ($isPartial) {
          // Responder con JSON para modal
          header('Content-Type: application/json; charset=utf-8');
          $response = [
            'ok' => true,
            'message' => 'Cargo registrado exitosamente',
            'redirect' => '/?controller=cargo&action=index'
          ];
          echo json_encode($response);
          return;
        }
        
        $_SESSION['flash_success'] = 'Cargo registrado exitosamente.';
        $this->redirect('/?controller=cargo&action=index');
        return;

      } catch (Throwable $e) {
        if ($isPartial) {
          // Responder con JSON para modal
          http_response_code(500);
          header('Content-Type: application/json; charset=utf-8');
          $response = [
            'ok' => false,
            'error' => 'Error al crear el cargo: ' . $e->getMessage()
          ];
          echo json_encode($response);
          return;
        }
        
        $_SESSION['flash_errors'] = ['general' => 'Error al crear el cargo: ' . $e->getMessage()];
        $this->render('cargo/create', [
          'form_data' => $_POST,
          'csrf' => $this->csrfToken()
        ]);
        return;
      }
    }

    if ($isPartial) {
      // Mostrar formulario para modal
      extract([
        'form_data' => [],
        'csrf' => $this->csrfToken(),
        'isPartial' => true
      ], EXTR_SKIP);
      require __DIR__ . '/../views/cargo/create.php';
      return;
    }
    
    $this->render('cargo/create', [
      'form_data' => [],
      'csrf' => $this->csrfToken()
    ]);
  }
  
  // Nuevo método para búsqueda de inventario
  public function searchInventory() {
    $this->requireLogin();
    
    $searchTerm = $_GET['term'] ?? '';
    $cargo = new Cargo();
    $results = $cargo->searchInventory($searchTerm);
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($results);
  }
  
  public function returnItem() {
    $this->requireLogin();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      if (!empty($_GET['partial'])) {
        http_response_code(405);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        return;
      }
      $this->redirect('/?controller=cargo&action=active');
      return;
    }
    
    $this->checkCsrf();
    
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
      if (!empty($_GET['partial'])) {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'ID inválido']);
        return;
      }
      $this->redirect('/?controller=cargo&action=active');
      return;
    }
    
    try {
      $cargo = new Cargo();
      $result = $cargo->returnCargo($id);
      
      if ($result) {
        if (!empty($_GET['partial'])) { // Si es solicitud parcial (modal)
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode(['ok' => true, 'message' => 'Item devuelto exitosamente']);
          return;
        }
        $_SESSION['flash_success'] = 'Item devuelto exitosamente';
      } else {
        if (!empty($_GET['partial'])) {
          http_response_code(500);
          header('Content-Type: application/json; charset=utf-8');
          echo json_encode(['ok' => false, 'error' => 'No se pudo devolver el item']);
          return;
        }
        $_SESSION['flash_errors'] = ['general' => 'No se pudo devolver el item'];
      }
    } catch (Throwable $e) {
      if (!empty($_GET['partial'])) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'Error al devolver el item: ' . $e->getMessage()]);
        return;
      }
      $_SESSION['flash_errors'] = ['general' => 'Error al devolver el item: ' . $e->getMessage()];
    }
    
    $this->redirect('/?controller=cargo&action=active');
  }
}