<?php
$config = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Agregando columna nombre_receptor...\n";
    
    $sql = "ALTER TABLE prestamos_llaves 
            ADD COLUMN nombre_receptor VARCHAR(150) NOT NULL AFTER usuario_id,
            ADD INDEX idx_nombre_receptor (nombre_receptor)";
    
    $pdo->exec($sql);
    
    echo "✓ Columna 'nombre_receptor' agregada exitosamente\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✓ La columna 'nombre_receptor' ya existe\n";
    } else {
        echo "✗ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
