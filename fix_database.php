<?php
/**
 * Script para arreglar tablas faltantes en la base de datos
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

$sql = <<<'SQL'
-- Tabla para registros de acceso de personal externo
CREATE TABLE IF NOT EXISTS registros_acceso_externo (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento VARCHAR(20) NOT NULL,
    tipo_documento ENUM('CC', 'CE', 'TI', 'PAS', 'NIT') DEFAULT 'CC',
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100),
    empresa VARCHAR(150),
    telefono VARCHAR(20),
    email VARCHAR(150),
    motivo_visita VARCHAR(255) NOT NULL,
    persona_visitada VARCHAR(150),
    area_destino VARCHAR(100),
    fecha_entrada DATETIME NOT NULL,
    fecha_salida DATETIME NULL,
    tiempo_permanencia INT NULL COMMENT 'Minutos de permanencia',
    vigilante_entrada_id BIGINT UNSIGNED,
    vigilante_salida_id BIGINT UNSIGNED NULL,
    observaciones TEXT,
    estado ENUM('DENTRO', 'SALIO') DEFAULT 'DENTRO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_documento (documento),
    INDEX idx_fecha_entrada (fecha_entrada),
    INDEX idx_estado (estado),
    INDEX idx_empresa (empresa),
    FOREIGN KEY (vigilante_entrada_id) REFERENCES usuarios_sistema(id) ON DELETE SET NULL,
    FOREIGN KEY (vigilante_salida_id) REFERENCES usuarios_sistema(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

try {
    $config = require CONFIG_PATH . '/database.php';
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=%s",
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );
    
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    
    // Ejecutar SQL
    $pdo->exec($sql);
    echo "✓ Tabla registros_acceso_externo creada exitosamente\n";
    
    // Crear vista
    $pdo->exec("DROP VIEW IF EXISTS vista_acceso_externo");
    $sqlView = <<<'SQL'
CREATE VIEW vista_acceso_externo AS
SELECT 
    rae.*,
    CONCAT(rae.nombres, ' ', COALESCE(rae.apellidos, '')) as nombre_completo,
    CONCAT(pe.nombres, ' ', COALESCE(pe.apellidos, '')) as vigilante_entrada_nombre,
    CONCAT(ps.nombres, ' ', COALESCE(ps.apellidos, '')) as vigilante_salida_nombre,
    TIMESTAMPDIFF(MINUTE, rae.fecha_entrada, COALESCE(rae.fecha_salida, NOW())) as minutos_transcurridos
FROM registros_acceso_externo rae
LEFT JOIN usuarios_sistema use1 ON rae.vigilante_entrada_id = use1.id
LEFT JOIN personas pe ON use1.persona_id = pe.id
LEFT JOIN usuarios_sistema use2 ON rae.vigilante_salida_id = use2.id
LEFT JOIN personas ps ON use2.persona_id = ps.id
ORDER BY rae.fecha_entrada DESC
SQL;
    
    $pdo->exec($sqlView);
    echo "✓ Vista vista_acceso_externo creada exitosamente\n";
    
    echo "\n✓✓✓ Base de datos actualizada correctamente\n";
    
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
