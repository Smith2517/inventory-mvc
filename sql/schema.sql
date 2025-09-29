-- Schema for inventory-mvc (MariaDB)
CREATE DATABASE IF NOT EXISTS inventory_mvc CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE inventory_mvc;

-- Users
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  nombre VARCHAR(100) NOT NULL,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Oficinas
DROP TABLE IF EXISTS oficinas;
CREATE TABLE oficinas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  estado TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Inventario
DROP TABLE IF EXISTS inventario;
CREATE TABLE inventario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(50) NOT NULL UNIQUE,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT,
  cantidad INT NOT NULL DEFAULT 0,
  oficina_id INT,
  estado ENUM('DISPONIBLE','AGOTADO') NOT NULL DEFAULT 'DISPONIBLE',
  estante VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT fk_inv_oficina FOREIGN KEY (oficina_id) REFERENCES oficinas(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Movimientos
DROP TABLE IF EXISTS movimientos;
CREATE TABLE movimientos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  inventario_id INT NOT NULL,
  tipo ENUM('ENTRADA','SALIDA') NOT NULL,
  cantidad INT NOT NULL,
  motivo TEXT,
  oficina_id INT NULL,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mov_inv FOREIGN KEY (inventario_id) REFERENCES inventario(id) ON DELETE CASCADE,
  CONSTRAINT fk_mov_ofi FOREIGN KEY (oficina_id) REFERENCES oficinas(id) ON DELETE SET NULL,
  CONSTRAINT fk_mov_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Seed
INSERT INTO users (username, password_hash, nombre, estado) VALUES
('admin', '$2y$10$8Qk7OAzq4w6eQEN2I/bt7uY0dWqI3f7kYk3w1E9wVql9uWPOlV6Bm', 'Administrador', 1);
-- Password: admin123 (bcrypt)

INSERT INTO oficinas (nombre, descripcion, estado) VALUES
('Almacén Central', 'Oficina principal de almacenamiento', 1),
('Oficina de Sistemas', 'Área de TI', 1);

INSERT INTO inventario (codigo, nombre, descripcion, cantidad, oficina_id, estado, estante) VALUES
('HD-001', 'Disco Duro 1TB', 'WD Blue', 20, 1, 'DISPONIBLE', 'E1'),
('SSD-256', 'SSD 256GB', 'SATA 2.5"', 5, 2, 'DISPONIBLE', 'E2');
