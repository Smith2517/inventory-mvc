<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../models/Office.php';
require_once __DIR__ . '/../models/Movement.php';

class InventoryController extends Controller
{
  public function index()
  {
    $this->requireLogin();
    $inv = new Inventory();
    $q = trim($_GET['q'] ?? '');
    $items = $inv->paginate($q, 200, 0);
    $of = new Office();
    $oficinas = $of->all();
    $this->render('inventory/index', [
      'items' => $items,
      'q' => $q,
      'oficinas' => $oficinas,
      'csrf' => $this->csrfToken()
    ]);
  }

  public function create()
  {
    $this->requireLogin();
    $of = new Office();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->checkCsrf();

      $nombre      = trim($_POST['nombre'] ?? '');
      $serie       = trim($_POST['serie'] ?? '');
      $oficinaId   = (int)($_POST['oficina_id'] ?? 0);
      $oficina     = $this->getOficinaById($of, $oficinaId);
      $nombreOfi   = $oficina['nombre'] ?? 'OFI';
      $estado_2   = trim($_POST['estado_2'] ?? 'BUENO');

      // 1) Prefijo a partir de oficina + nombre de ítem
      $prefix = $this->buildCodePrefix($nombreOfi, $nombre);

      // 2) SIGUIENTE código disponible
      $inv = new Inventory();
      $codigo = $inv->nextCodeForPrefix($prefix); // p.e. SOP-INF-0007

      // 3) Crear
      $id = $inv->create([
        'codigo'      => $codigo,
        'nombre'      => $nombre,
        'serie'       => $serie,
        'descripcion' => trim($_POST['descripcion'] ?? ''),
        'cantidad'    => (int)$_POST['cantidad'],
        'oficina_id'  => $oficinaId,
        'estado_2'   => $estado_2,
        'estante'     => trim($_POST['estante'] ?? '')
      ]);

      // 4) Log de entrada
      if ((int)$_POST['cantidad'] > 0) {
        $mov = new Movement();
        $mov->create([
          'inventario_id' => $id,
          'tipo'          => 'ENTRADA',
          'cantidad'      => (int)$_POST['cantidad'],
          'motivo'        => 'Registro inicial',
          'oficina_id'    => $oficinaId ?: 0,
          'user_id'       => $_SESSION['user']['id']
        ]);
      }

      $this->redirect('/');
      return;
    }

    $this->render('inventory/create', ['oficinas' => $of->all(), 'csrf' => $this->csrfToken()]);
  }

  public function label()
  {
    $this->requireLogin();
    $inv = new Inventory();
    $item = $inv->find((int)($_GET['id'] ?? 0));
    if (!$item) { http_response_code(404); echo 'Item no encontrado'; return; }
    $this->renderRaw('inventory/label', ['item' => $item]);
  }

  public function show()
  {
    $this->requireLogin();
    $inv = new Inventory();
    $mov = new Movement();
    $id = (int)($_GET['id'] ?? 0);
    $item = $inv->find($id);
    if (!$item) { http_response_code(404); echo 'Item no encontrado'; return; }
    $movs = $mov->byInventory($id);
    $csrf = $this->csrfToken();

    if (!empty($_GET['partial'])) {
      extract(compact('item', 'movs', 'csrf'), EXTR_SKIP);
      header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
      require __DIR__ . '/../views/inventory/show.php';
      return;
    }
    $this->render('inventory/show', compact('item', 'movs', 'csrf'));
  }

  public function discount()
  {
    $this->requireLogin();
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Método no permitido'; return; }
    $this->checkCsrf();
    $id = (int)($_POST['inventario_id'] ?? 0);
    $cantidad = max(0, (int)($_POST['cantidad'] ?? 0));
    $motivo = trim($_POST['motivo'] ?? '');
    $oficina_id = (int)($_POST['oficina_id'] ?? 0);

    $inv = new Inventory();
    $item = $inv->find($id);
    if (!$item) { http_response_code(404); echo 'Item no encontrado'; return; }
    if ($cantidad <= 0) { $this->redirect('/?controller=inventory&action=index'); }

    $nueva = max(0, (int)$item['cantidad'] - $cantidad);
    $inv->updateCantidad($id, $nueva);

    $mov = new Movement();
    $mov->create([
      'inventario_id' => $id,
      'tipo' => 'SALIDA',
      'cantidad' => $cantidad,
      'motivo' => $motivo ?: 'Uso/Salida de stock',
      'oficina_id' => $oficina_id ?: null,
      'user_id' => $_SESSION['user']['id']
    ]);

    $this->redirect('/?controller=inventory&action=show&id=' . $id);
  }

  /** === Helpers === */

  /** Abreviatura ASCII (3 letras), sin acentos ni signos */
  private function abbr(string $text, int $max = 3): string
  {
    $stop = ['DE','DEL','LA','LAS','LOS','Y','EL','A','EN','PARA','POR','AL'];
    $text = trim($text);
    if ($text === '') return 'GEN';

    $up = mb_strtoupper($text, 'UTF-8');
    // quitar acentos/ñ → ASCII
    $up = strtr($up, ['Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N']);
    $clean = preg_replace('/[^A-Z\s]/u', ' ', $up);
    $words = preg_split('/\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);

    $letters = '';
    foreach ($words as $w) {
      if (in_array($w, $stop, true)) continue;
      $letters .= mb_substr($w, 0, 1, 'UTF-8');
      if (mb_strlen($letters, 'UTF-8') >= $max) break;
    }
    if (mb_strlen($letters, 'UTF-8') < $max) {
      foreach ($words as $w) {
        if (in_array($w, $stop, true)) continue;
        $cand = mb_substr($w, 0, $max, 'UTF-8');
        if ($cand !== '') { $letters = $cand; break; }
      }
    }
    return $letters !== '' ? $letters : 'GEN';
  }

  private function buildCodePrefix(string $nombreOficina, string $nombreItem): string
  {
    $ofAbbr   = $this->abbr($nombreOficina);
    $itemAbbr = $this->abbr($nombreItem);
    return $ofAbbr . '-' . $itemAbbr;
  }

  private function getOficinaById(Office $of, int $id): ?array
  {
    if (method_exists($of, 'find')) return $of->find($id) ?: null;
    $all = $of->all();
    foreach ($all as $o) if ((int)($o['id'] ?? 0) === $id) return $o;
    return null;
  }

public function nextCode()
{
  $this->requireLogin();
  header('Content-Type: application/json; charset=utf-8');

  $oficinaId = (int)($_GET['oficina_id'] ?? 0);
  $nombre    = trim($_GET['nombre'] ?? '');

  if (!$oficinaId || $nombre === '') {
    echo json_encode(['ok' => false, 'code' => null, 'msg' => 'Falta oficina o nombre']);
    return;
  }

  $of = new Office();
  $oficina = $this->getOficinaById($of, $oficinaId);
  if (!$oficina || empty($oficina['nombre'])) {
    echo json_encode(['ok' => false, 'code' => null, 'msg' => 'Oficina no válida']);
    return;
  }

  $prefix = $this->buildCodePrefix($oficina['nombre'], $nombre);
  $inv = new Inventory();
  $code = $inv->nextCodeForPrefix($prefix);

  echo json_encode(['ok' => true, 'code' => $code, 'prefix' => $prefix]);
}


}
