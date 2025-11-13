<?php
require_once __DIR__ . '/../core/Database.php';

class Cargo {
  private $db;
  
  public function __construct() {
    $this->db = Database::getInstance()->pdo();
  }
  
  public function all() {
    $sql = "SELECT c.*, i.codigo, i.nombre, i.cantidad as inventario_cantidad
            FROM cargos c
            LEFT JOIN inventario i ON c.inventario_id = i.id
            ORDER BY c.fecha_inicio DESC";
    $st = $this->db->query($sql);
    return $st->fetchAll();
  }
  
  public function find($id) {
    $sql = "SELECT c.*, i.codigo, i.nombre, i.cantidad as inventario_cantidad
            FROM cargos c
            LEFT JOIN inventario i ON c.inventario_id = i.id
            WHERE c.id = ?";
    $st = $this->db->prepare($sql);
    $st->execute([intval($id)]);
    return $st->fetch();
  }
  
  public function create($data) {
    $this->db->beginTransaction();
    
    try {
      // Insertar el cargo
      $sql = "INSERT INTO cargos (inventario_id, oficina_destino, usuario_destino, cantidad, fecha_inicio, motivo, estado) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
      $st = $this->db->prepare($sql);
      $result = $st->execute([
        $data['inventario_id'],
        $data['oficina_destino'] ?? null,
        $data['usuario_destino'],
        $data['cantidad'],
        $data['fecha_inicio'],
        $data['motivo'] ?? null,
        'ACTIVO'
      ]);
      
      $cargoId = $this->db->lastInsertId();
      
      // Descontar del inventario
      $updateSql = "UPDATE inventario SET cantidad = cantidad - ? WHERE id = ?";
      $updateSt = $this->db->prepare($updateSql);
      $updateResult = $updateSt->execute([$data['cantidad'], $data['inventario_id']]);
      
      if (!$result || !$updateResult) {
        throw new Exception("Error al crear el cargo o actualizar inventario");
      }
      
      $this->db->commit();
      return $cargoId;
      
    } catch (Throwable $e) {
      $this->db->rollback();
      throw $e;
    }
  }
  
  public function update($id, $data) {
    $st = $this->db->prepare("UPDATE cargos SET inventario_id = ?, oficina_destino = ?, usuario_destino = ?, cantidad = ?, fecha_inicio = ?, fecha_fin = ?, motivo = ?, estado = ? WHERE id = ?");
    return $st->execute([
      $data['inventario_id'],
      $data['oficina_destino'] ?? null,
      $data['usuario_destino'],
      $data['cantidad'],
      $data['fecha_inicio'],
      $data['fecha_fin'] ?? null,
      $data['motivo'] ?? null,
      $data['estado'],
      intval($id)
    ]);
  }
  
  public function returnCargo($id) {
    $cargo = $this->find($id);
    if (!$cargo) {
      return false;
    }
    
    $this->db->beginTransaction();
    
    try {
      // Actualizar estado del cargo
      $updateCargoSql = "UPDATE cargos SET estado = 'DEVUELTO', fecha_fin = CURRENT_DATE WHERE id = ?";
      $updateCargoSt = $this->db->prepare($updateCargoSql);
      $updateCargoResult = $updateCargoSt->execute([intval($id)]);
      
      // Añadir al inventario
      $updateInventarioSql = "UPDATE inventario SET cantidad = cantidad + ? WHERE id = ?";
      $updateInventarioSt = $this->db->prepare($updateInventarioSql);
      $updateInventarioResult = $updateInventarioSt->execute([$cargo['cantidad'], $cargo['inventario_id']]);
      
      if (!$updateCargoResult || !$updateInventarioResult) {
        throw new Exception("Error al devolver el cargo o actualizar inventario");
      }
      
      $this->db->commit();
      return true;
      
    } catch (Throwable $e) {
      $this->db->rollback();
      throw $e;
    }
  }
  
  public function getActiveCargos() {
    $sql = "SELECT c.*, i.codigo, i.nombre, i.cantidad as inventario_cantidad
            FROM cargos c
            LEFT JOIN inventario i ON c.inventario_id = i.id
            WHERE c.estado = 'ACTIVO'
            ORDER BY c.fecha_inicio DESC";
    $st = $this->db->query($sql);
    return $st->fetchAll();
  }
  
  // Método para buscar items de inventario
  public function searchInventory($searchTerm) {
    $sql = "SELECT i.id, i.codigo, i.nombre, i.cantidad, i.descripcion, i.estado_2, o.nombre as oficina_nombre
            FROM inventario i
            LEFT JOIN oficinas o ON i.oficina_id = o.id
            WHERE i.codigo LIKE ? OR i.nombre LIKE ?
            ORDER BY i.nombre
            LIMIT 10";
    $st = $this->db->prepare($sql);
    $st->execute(["%$searchTerm%", "%$searchTerm%"]);
    return $st->fetchAll();
  }
}