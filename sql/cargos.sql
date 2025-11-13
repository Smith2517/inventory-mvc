CREATE TABLE cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventario_id INT NOT NULL,
    oficina_id INT,
    usuario_id INT NOT NULL,
    cantidad INT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    motivo TEXT,
    estado ENUM('ACTIVO', 'DEVUELTO') DEFAULT 'ACTIVO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (inventario_id) REFERENCES inventario(id) ON DELETE CASCADE,
    FOREIGN KEY (oficina_id) REFERENCES oficinas(id) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);