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

}
