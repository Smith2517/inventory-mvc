<?php
require_once __DIR__ . '/../core/Database.php';

class Inventory
{
  private $db;
  public function __construct()
  {
    $this->db = Database::getInstance()->pdo();
  }

  public function paginate($q = '', $limit = 50, $offset = 0)
  {
    $limit  = max(1, (int)$limit);
    $offset = max(0, (int)$offset);

    $sql = 'SELECT i.*, o.nombre AS oficina
            FROM inventario i
            LEFT JOIN oficinas o ON i.oficina_id = o.id';
    $params = [];

    if ($q) {
      $sql .= ' WHERE i.codigo LIKE ? OR i.nombre LIKE ?';
      $params = ["%$q%", "%$q%"];
    }

    $sql .= " ORDER BY i.created_at DESC LIMIT $limit OFFSET $offset";
    $st = $this->db->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
  }

  public function count($q = '')
  {
    $sql = 'SELECT COUNT(*) c FROM inventario i';
    $params = [];
    if ($q) {
      $sql .= ' WHERE i.codigo LIKE ? OR i.nombre LIKE ?';
      $params = ["%$q%", "%$q%"];
    }
    $st = $this->db->prepare($sql);
    $st->execute($params);
    return (int)$st->fetch()['c'];
  }

  public function find($id)
  {
    $st = $this->db->prepare('SELECT i.id, i.codigo, i.nombre, i.descripcion,
          i.cantidad, o.nombre AS nameOficina, i.estado, i.estante, i.estado_2, i.serie
          FROM inventario i 
          INNER JOIN oficinas o ON i.oficina_id=o.id WHERE i.id = ?');
    $st->execute([$id]);
    return $st->fetch();
  }

  public function findByCodigo($codigo)
  {
    $st = $this->db->prepare('SELECT * FROM inventario WHERE codigo = ?');
    $st->execute([$codigo]);
    return $st->fetch();
  }

  public function create($data)
  {
    $estado = ((int)$data['cantidad'] > 0) ? 'DISPONIBLE' : 'AGOTADO';
    $st = $this->db->prepare('INSERT INTO inventario (codigo, nombre, serie, descripcion, cantidad, oficina_id, estado, estado_2, estante, updated_at)
                              VALUES (?,?,?,?,?,?,?,?,?, NOW())');
    $st->execute([
      $data['codigo'],
      $data['nombre'],
      $data['serie'] ?? null,
      $data['descripcion'] ?? null,
      (int)$data['cantidad'],
      $data['oficina_id'] ?: null,
      $estado,
      $data['estado_2'] ?? 'BUENO',
      $data['estante'] ?? null
    ]);
    return (int)$this->db->lastInsertId();
  }

  public function updateCantidad($id, $cantidad)
  {
    $estado = ($cantidad > 0) ? 'DISPONIBLE' : 'AGOTADO';
    $st = $this->db->prepare('UPDATE inventario SET cantidad = ?, estado = ?, updated_at = NOW() WHERE id = ?');
    $st->execute([(int)$cantidad, $estado, (int)$id]);
  }

  /** Devuelve p.ej. SOP-INF-0001, SOP-INF-0002, ... para el prefijo dado */
  public function nextCodeForPrefix(string $prefix): string
  {
    // OBLIGATORIO: índice único en inventario.codigo para evitar duplicados por concurrencia:
    // ALTER TABLE inventario ADD UNIQUE KEY uniq_codigo (codigo);

    $table = 'inventario';

    // Tomamos el trozo numérico inmediatamente después de "PREFIX-"
    // Ej: PREFIX-0007  -> "0007"
    $sql = "SELECT COALESCE(MAX(CAST(SUBSTRING(codigo, CHAR_LENGTH(:pref) + 2) AS UNSIGNED)), 0) AS maxn
            FROM {$table}
            WHERE codigo LIKE CONCAT(:pref, '-%')";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([':pref' => $prefix]);
    $max = (int)$stmt->fetchColumn();
    $next = $max + 1;

    return sprintf('%s-%04d', $prefix, $next);
  }
}
