<?php
$config = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    // Password: Admin123!
    $passwordHash = password_hash('Admin123!', PASSWORD_DEFAULT);
    
    echo "Actualizando contraseña del vigilante...\n";
    
    $stmt = $pdo->prepare("
        UPDATE usuarios_sistema 
        SET password_hash = :password, estado = 'ACTIVO'
        WHERE username = 'vigilante'
    ");
    $stmt->execute(['password' => $passwordHash]);
    
    echo "✓ Contraseña del vigilante actualizada\n\n";
    echo "=== CREDENCIALES VIGILANTE ===\n";
    echo "Usuario: vigilante\n";
    echo "Contraseña: Admin123!\n";
    echo "Email: carlos.martinez@sena.edu.co\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
