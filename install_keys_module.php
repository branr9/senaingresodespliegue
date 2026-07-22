<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/models/Database.php';

$db = Database::getInstance()->getConnection();

// Leer el archivo SQL
$sql = file_get_contents(__DIR__ . '/database/control_llaves.sql');

// Dividir por sentencias
$statements = array_filter(array_map('trim', explode(';', $sql)));

try {
    echo "Ejecutando script de Control de Llaves...\n\n";
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $db->exec($statement);
            echo "✓ Sentencia ejecutada\n";
        }
    }
    
    echo "\n✅ Tablas de control de llaves creadas exitosamente!\n";
    
} catch (PDOException $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}
