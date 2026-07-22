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
    
    echo "Creando usuario instructor...\n";
    
    // Primero obtener el ID de la persona asociada o crear una
    $stmt = $pdo->prepare("SELECT id FROM personas WHERE documento = '1234567890' LIMIT 1");
    $stmt->execute();
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$persona) {
        echo "Creando persona para instructor...\n";
        $pdo->exec("
            INSERT INTO personas (documento, tipo_documento, nombres, apellidos, tipo_persona_id)
            VALUES ('1234567890', 'CC', 'Juan Carlos', 'Rodríguez', 
                    (SELECT id FROM cat_persona_tipo WHERE codigo = 'INSTRUCTOR' LIMIT 1))
        ");
        $personaId = $pdo->lastInsertId();
    } else {
        $personaId = $persona['id'];
    }
    
    // Obtener el rol de instructor
    $stmt = $pdo->prepare("SELECT id FROM cat_roles WHERE codigo = 'INSTRUCTOR' LIMIT 1");
    $stmt->execute();
    $rol = $stmt->fetch(PDO::FETCH_ASSOC);
    $rolId = $rol['id'];
    
    // Verificar si ya existe el usuario
    $stmt = $pdo->prepare("SELECT id FROM usuarios_sistema WHERE username = 'instructor'");
    $stmt->execute();
    $exists = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($exists) {
        echo "Usuario instructor ya existe. Actualizando contraseña...\n";
        $stmt = $pdo->prepare("
            UPDATE usuarios_sistema 
            SET password_hash = :password, estado = 'ACTIVO'
            WHERE username = 'instructor'
        ");
        $stmt->execute(['password' => $passwordHash]);
        echo "✓ Contraseña actualizada\n";
    } else {
        echo "Creando nuevo usuario instructor...\n";
        $stmt = $pdo->prepare("
            INSERT INTO usuarios_sistema (persona_id, username, email, password_hash, rol_id, estado)
            VALUES (:persona_id, 'instructor', 'instructor@sena.edu.co', :password, :rol_id, 'ACTIVO')
        ");
        $stmt->execute([
            'persona_id' => $personaId,
            'password' => $passwordHash,
            'rol_id' => $rolId
        ]);
        echo "✓ Usuario instructor creado\n";
    }
    
    echo "\n=== CREDENCIALES ===\n";
    echo "Usuario: instructor\n";
    echo "Contraseña: Admin123!\n";
    echo "Email: instructor@sena.edu.co\n";
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
