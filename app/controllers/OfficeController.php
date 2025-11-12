<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Office.php';

class OfficeController extends Controller {
  public function index() {
    $this->requireLogin();
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
        if ($isAjax) {
            http_response_code(422);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false, 'errors' => $errors]);
            return;
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
            return;
        }

        // --- Flujo clásico (no-AJAX) ---
        $this->redirect('/?controller=office&action=index');
        return;

    } catch (Throwable $e) {
        if ($isAjax) {
            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'ok'    => false,
                'error' => 'Error al registrar la oficina.',
                'msg'   => $e->getMessage(),
            ]);
            return;
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
        // Mostrar contenido para modal
        extract(['oficina' => $oficina], EXTR_SKIP);
        require __DIR__ . '/../views/office/show.php';
        return;
    }
    
    $this->render('office/show', ['oficina' => $oficina]);
  }
  
  public function edit() {
    $this->requireLogin();
    $id = (int)($_GET['id'] ?? 0);
    $isPartial = !empty($_GET['partial']); // para modal/iframe
    
    if ($id <= 0) {
        if ($isPartial) {
            http_response_code(404);
            echo "Oficina no encontrada";
            return;
        }
        $this->redirect('/?controller=office&action=index');
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
        $this->redirect('/?controller=office&action=index');
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // --- CSRF ---
        $this->checkCsrf();

        // --- Inputs ---
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado      = isset($_POST['estado']) ? (int)$_POST['estado'] : 0;

        // --- Validación simple ---
        $errors = [];
        if ($nombre === '') {
            $errors['nombre'] = 'El nombre es obligatorio.';
        }
        if ($estado !== 0 && $estado !== 1) {
            $estado = 1; // normalizamos
        }

        if (!empty($errors)) {
            // No-AJAX: podrías guardar errores en sesión y redirigir
            if ($isPartial) {
                // Devolver errores para modal
                http_response_code(422);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'errors' => $errors]);
                return;
            }
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

            if ($updated) {
                if ($isPartial) {
                    // Devolver éxito para modal
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => true, 'message' => 'Oficina actualizada correctamente']);
                    return;
                }
                $this->redirect('/?controller=office&action=index');
            } else {
                if ($isPartial) {
                    http_response_code(500);
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode(['ok' => false, 'error' => 'Error al actualizar la oficina']);
                    return;
                }
                $_SESSION['flash_errors'] = ['general' => 'Error al actualizar la oficina'];
                $this->redirect('/?controller=office&action=edit&id=' . $id);
            }
            return;

        } catch (Throwable $e) {
            // flash + redirect
            if ($isPartial) {
                http_response_code(500);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['ok' => false, 'error' => 'Error al actualizar la oficina: ' . $e->getMessage()]);
                return;
            }
            $_SESSION['flash_errors'] = ['general' => 'Error al actualizar la oficina: ' . $e->getMessage()];
            $this->redirect('/?controller=office&action=edit&id=' . $id);
            return;
        }
    }
    
    if ($isPartial) {
        // Mostrar formulario para modal
        extract(['oficina' => $oficina, 'csrf' => $this->csrfToken()], EXTR_SKIP);
        require __DIR__ . '/../views/office/edit.php';
        return;
    }
    
    $this->render('office/edit', ['oficina' => $oficina, 'csrf' => $this->csrfToken()]);
  }
}
