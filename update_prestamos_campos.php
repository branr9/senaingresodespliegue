<?php
$config = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Agregando campos adicionales...\n";
    
    $sql = "ALTER TABLE prestamos_llaves 
            ADD COLUMN documento_receptor VARCHAR(20) NOT NULL AFTER nombre_receptor,
            ADD COLUMN departamento VARCHAR(100) NULL AFTER documento_receptor,
            ADD COLUMN telefono VARCHAR(20) NULL AFTER departamento,
            ADD INDEX idx_documento_receptor (documento_receptor)";
    
    $pdo->exec($sql);
    
    echo "✓ Campos 'documento_receptor', 'departamento' y 'telefono' agregados exitosamente\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✓ Los campos adicionales ya existen\n";
    } else {
        echo "✗ Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}
