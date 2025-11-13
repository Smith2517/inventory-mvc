<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Office.php';

class OfficeController extends Controller {
  public function index() {
    $this->requireLogin();
    
    // Verificar si se eliminó una oficina y mostrar mensaje flash
    if (isset($_GET['deleted']) && $_GET['deleted'] == '1') {
      $_SESSION['flash_success'] = 'Oficina eliminada correctamente.';
    }
    
    $of = new Office();
    $oficinas = $of->all();
    $this->render('office/index', ['oficinas' => $oficinas, 'csrf' => $this->csrfToken()]);
  }

public function create()
{
    // Si no es POST, volvemos al índice (evitamos cargar otra vista)
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect('/?controller=office&action=index');
        return;
    }

    // --- CSRF ---
    $this->checkCsrf();

    // --- Inputs ---
    $nombre      = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado      = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

    // Iniciar buffer de salida para prevenir cualquier salida no deseada
    ob_start();
    
    // --- Detección de petición AJAX ---
    $isAjax = (isset($_GET['ajax']) && $_GET['ajax'] == '1')
           || (isset($_SERVER['HTTP_ACCEPT']) && stripos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
           || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');

    // --- Validación simple ---
    $errors = [];
    if ($nombre === '') {
        $errors['nombre'] = 'El nombre es obligatorio.';
    }
    if ($estado !== 0 && $estado !== 1) {
        $estado = 1; // normalizamos
    }

    if (!empty($errors)) {
        // Limpiar buffer antes de enviar JSON
        ob_clean();
        
        if ($isAjax) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'errors' => $errors]);
            exit;
        }
        // No-AJAX: podrías guardar errores en sesión y redirigir
        $_SESSION['flash_errors'] = $errors;
        $this->redirect('/?controller=office&action=index');
        return;
    }

    // --- Crear en BD ---
    try {
        $of = new Office();
        // Se asume que create(...) hace INSERT y devuelve el ID o bool
        $insertedId = $of->create([
            'nombre'      => $nombre,
            'descripcion' => $descripcion,
            'estado'      => $estado,
        ]);

        // Limpiar buffer antes de enviar respuesta
        ob_clean();
        
        // Si el modelo no devuelve ID, lo recuperamos por PDO
        if (!$insertedId) {
            $pdo = Database::getInstance()->pdo();
            $insertedId = (int)$pdo->lastInsertId();
        } else {
            $insertedId = (int)$insertedId;
        }

        // --- Respuesta AJAX JSON ---
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok'     => true,
                'office' => [
                    'id'          => $insertedId,
                    'nombre'      => $nombre,
                    'descripcion' => $descripcion,
                    'estado'      => (int)$estado,
                ],
            ]);
            exit;
        }

        // --- Flujo clásico (no-AJAX) ---
        $_SESSION['flash_success'] = 'Oficina creada exitosamente.';
        $this->redirect('/?controller=office&action=index');
        return;

    } catch (Throwable $e) {
        // Limpiar buffer antes de enviar error
        ob_clean();
        
        if ($isAjax) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok'    => false,
                'error' => 'Error al registrar la oficina.',
                'msg'   => $e->getMessage(),
            ]);
            exit;
        }
        // No-AJAX: flash + redirect
        $_SESSION['flash_errors'] = ['general' => 'Error al registrar la oficina: ' . $e->getMessage()];
        $this->redirect('/?controller=office&action=index');
        return;
    }
}

  public function show() {
    $this->requireLogin();
    $id = (int)($_GET['id'] ?? 0);
    $isPartial = !empty($_GET['partial']); // para modal/iframe
    
    if ($id <= 0) {
        if ($isPartial) {
            http_response_code(404);
            echo "Oficina no encontrada";
            return;
        }
        http_response_code(404);
        echo "Oficina no encontrada";
        return;
    }
    
    $of = new Office();
    $oficina = $of->find($id);
    
    if (!$oficina) {
        if ($isPartial) {
            http_response_code(404);
            echo "Oficina no encontrada";
            return;
        }
        http_response_code(404);
        echo "Oficina no encontrada";
        return;
    }
    
    if ($isPartial) {
        // Mostrar contenido para modal de forma limpia
        extract(['oficina' => $oficina], EXTR_SKIP);
        ob_start(); // Iniciar buffer de salida
        require __DIR__ . '/../views/office/show.php';
        $content = ob_get_clean(); // Limpiar buffer y capturar contenido
        echo $content; // Mostrar solo contenido limpio
        exit; // Salir inmediatamente para evitar más salida
    }
    
    $this->render('office/show', ['oficina' => $oficina]);
  }
  
public function edit() {
    $this->requireLogin();
    $id = (int)($_GET['id'] ?? 0);
    $isPartial = !empty($_GET['partial']); // para modal/iframe

    // Detectar si es petición AJAX (por fetch del modal)
    $isAjax = $isPartial 
        || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');

    if ($id <= 0) {
        if ($isAjax) {
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Oficina no encontrada']);
            exit;
        }
        $this->redirect('/?controller=office&action=index');
        return;
    }
    
    $of = new Office();
    $oficina = $of->find($id);
    
    if (!$oficina) {
        if ($isAjax) {
            http_response_code(404);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'error' => 'Oficina no encontrada']);
            exit;
        }
        $this->redirect('/?controller=office&action=index');
        return;
    }
    
    // ===== POST: actualizar =====
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // --- CSRF ---
        $this->checkCsrf();

        // --- Inputs ---
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado      = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

        // Iniciar buffer de salida para prevenir cualquier salida no deseada
        ob_start();

        // --- Validación simple ---
        $errors = [];
        if ($nombre === '') {
            $errors['nombre'] = 'El nombre es obligatorio.';
        }
        if ($estado !== 0 && $estado !== 1) {
            $estado = 1; // normalizamos
        }

        if (!empty($errors)) {
            // Limpiar buffer antes de enviar respuesta
            ob_clean();
            
            if ($isAjax) {
                http_response_code(422);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'errors' => $errors]);
                exit;
            }

            // No-AJAX: guardar errores y redirigir
            $_SESSION['flash_errors'] = $errors;
            $this->redirect('/?controller=office&action=edit&id=' . $id);
            return;
        }

        // --- Actualizar en BD ---
        try {
            $updated = $of->update($id, [
                'nombre'      => $nombre,
                'descripcion' => $descripcion,
                'estado'      => $estado,
            ]);

            // Limpiar buffer antes de enviar respuesta
            ob_clean();

            if ($updated) {
                if ($isAjax) {
                    // Respuesta JSON para el modal
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'ok'      => true,
                        'message' => 'Oficina actualizada correctamente',
                        'office'  => [
                            'id'          => $id,
                            'nombre'      => $nombre,
                            'descripcion' => $descripcion,
                            'estado'      => (int)$estado,
                        ]
                    ]);
                    exit;
                }

                // Flujo clásico (no-AJAX): redirigir
                $_SESSION['flash_success'] = 'Oficina actualizada correctamente.';
                $this->redirect('/?controller=office&action=index');
            } else {
                // Limpiar buffer antes de enviar error
                ob_clean();
                
                if ($isAjax) {
                    http_response_code(500);
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'error' => 'Error al actualizar la oficina']);
                    exit;
                }

                $_SESSION['flash_errors'] = ['general' => 'Error al actualizar la oficina'];
                $this->redirect('/?controller=office&action=edit&id=' . $id);
            }
            return;

        } catch (Throwable $e) {
            // Limpiar buffer antes de enviar error
            ob_clean();
            
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode([
                    'ok'    => false,
                    'error' => 'Error al actualizar la oficina: ' . $e->getMessage()
                ]);
                exit;
            }

            $_SESSION['flash_errors'] = ['general' => 'Error al actualizar la oficina: ' . $e->getMessage()];
            $this->redirect('/?controller=office&action=edit&id=' . $id);
            return;
        }
    }
    
    // ===== GET: mostrar formulario =====
    if ($isPartial) {
        // Mostrar formulario para modal de forma limpia
        extract(['oficina' => $oficina, 'csrf' => $this->csrfToken()], EXTR_SKIP);
        ob_start(); // Iniciar buffer de salida
        require __DIR__ . '/../views/office/edit.php';
        $content = ob_get_clean(); // Limpiar buffer y capturar contenido
        echo $content; // Mostrar solo contenido limpio
        exit; // Salir inmediatamente para evitar más salida
    }
    
    // Vista completa (no modal)
    $this->render('office/edit', ['oficina' => $oficina, 'csrf' => $this->csrfToken()]);
}

  
  public function delete() {
    $this->requireLogin();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      http_response_code(405);
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
      return;
    }
    
    $this->checkCsrf();
    
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['ok' => false, 'error' => 'ID de oficina no válido']);
      return;
    }
    
    $of = new Office();
    $oficina = $of->find($id);
    
    if (!$oficina) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['ok' => false, 'error' => 'Oficina no encontrada']);
      return;
    }
    
    // Verificar si la oficina tiene referencias en el inventario
    if ($of->hasReferences($id)) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['ok' => false, 'error' => 'No se puede eliminar la oficina porque tiene ítems de inventario asociados']);
      return;
    }
    
    // Proceder con la eliminación
    $deleted = $of->delete($id);
    
    if ($deleted) {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['ok' => true, 'message' => 'Oficina eliminada correctamente']);
      exit;
    } else {
      header('Content-Type: application/json; charset=utf-8');
      echo json_encode(['ok' => false, 'error' => 'No se pudo eliminar la oficina']);
      exit;
    }
  }
}
