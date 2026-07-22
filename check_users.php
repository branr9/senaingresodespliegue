<?php
$config = require __DIR__ . '/config/database.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "=== USUARIOS EXISTENTES EN LA BASE DE DATOS ===\n\n";
    
    // Primero verificar si la tabla usuarios_sistema existe
    $tables = $pdo->query("SHOW TABLES LIKE 'usuarios_sistema'")->fetchAll();
    
    if (empty($tables)) {
        echo "La tabla 'usuarios_sistema' NO existe.\n";
        echo "Verificando tabla 'usuarios'...\n\n";
        
        $stmt = $pdo->query("SELECT id, nombre, email, username, rol_id, estado FROM usuarios");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Tabla 'usuarios_sistema' encontrada.\n\n";
        $stmt = $pdo->query("
            SELECT us.*, cr.nombre as rol_nombre 
            FROM usuarios_sistema us 
            LEFT JOIN cat_roles cr ON us.rol_id = cr.id
        ");
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    if (empty($usuarios)) {
        echo "❌ NO HAY USUARIOS EN LA BASE DE DATOS\n\n";
        echo "Necesitas ejecutar el script de seeds o crear usuarios manualmente.\n";
    } else {
        foreach ($usuarios as $usuario) {
            echo "Usuario: {$usuario['username']}\n";
            echo "Nombre: {$usuario['nombre']}\n";
            echo "Email: {$usuario['email']}\n";
            echo "Rol: " . ($usuario['rol_nombre'] ?? $usuario['rol_id']) . "\n";
            echo "Estado: {$usuario['estado']}\n";
            echo "---\n";
        }
    }
    
    echo "\n=== ROLES DISPONIBLES ===\n\n";
    $roles = $pdo->query("SELECT * FROM cat_roles")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($roles as $rol) {
        echo "ID: {$rol['id']} - Código: {$rol['codigo']} - Nombre: {$rol['nombre']}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
