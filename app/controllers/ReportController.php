<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../models/Movement.php';

class ReportController extends Controller {
  public function inventory() {
    $this->requireLogin();
    $inv = new Inventory();
    $q = trim($_GET['q'] ?? '');
    $items = $inv->paginate($q, 1000, 0);
    $this->renderRaw('report/inventory', ['items' => $items, 'q' => $q]);
  }

  public function movements() {
    $this->requireLogin();
    $from = $_GET['from'] ?? date('Y-m-01 00:00:00');
    $to   = $_GET['to']   ?? date('Y-m-d 23:59:59');
    $mov = new Movement();
    $rows = $mov->betweenDates($from, $to);
    $this->renderRaw('report/movements', ['rows' => $rows, 'from' => $from, 'to' => $to]);
  }
}
