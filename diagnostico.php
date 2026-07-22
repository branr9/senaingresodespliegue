<?php

echo "=== DIAGNÓSTICO COMPLETO ===\n\n";

// 1. Verificar carga de .env
echo "1. Variables de entorno:\n";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (trim($line) === '') continue;
        
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
    
    echo "   DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NO DEFINIDO') . "\n";
    echo "   DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NO DEFINIDO') . "\n";
    echo "   DB_USER: " . ($_ENV['DB_USER'] ?? 'NO DEFINIDO') . "\n\n";
} else {
    echo "   ❌ Archivo .env no encontrado\n\n";
}

// 2. Verificar config/database.php
echo "2. Configuración de database.php:\n";
define('CONFIG_PATH', __DIR__ . '/config');
$dbConfig = require CONFIG_PATH . '/database.php';
echo "   host: " . $dbConfig['host'] . "\n";
echo "   database: " . $dbConfig['database'] . "\n";
echo "   username: " . $dbConfig['username'] . "\n\n";

// 3. Probar conexión directa
echo "3. Conexión directa con PDO:\n";
try {
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    $currentDb = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "   ✅ Conectado a: $currentDb\n\n";
    
    // 4. Verificar tabla usuarios_sistema
    echo "4. Verificación de tabla usuarios_sistema:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios_sistema'");
    if ($stmt->rowCount() > 0) {
        echo "   ✅ Tabla existe\n\n";
        
        // 5. Verificar usuario admin
        echo "5. Verificación de usuario admin:\n";
        $sql = "SELECT us.username, us.estado, p.email, p.nombres, p.estado as persona_estado
                FROM usuarios_sistema us
                INNER JOIN personas p ON us.persona_id = p.id
                WHERE us.username = 'admin'";
        $stmt = $pdo->query($sql);
        $admin = $stmt->fetch();
        
        if ($admin) {
            echo "   ✅ Usuario admin encontrado:\n";
            echo "      Username: {$admin['username']}\n";
            echo "      Email: {$admin['email']}\n";
            echo "      Nombres: {$admin['nombres']}\n";
            echo "      Estado US: {$admin['estado']}\n";
            echo "      Estado Persona: {$admin['persona_estado']}\n\n";
            
            // 6. Verificar password
            echo "6. Verificación de password:\n";
            $stmt = $pdo->query("SELECT password_hash FROM usuarios_sistema WHERE username = 'admin'");
            $hash = $stmt->fetchColumn();
            $verify = password_verify('Admin123!', $hash);
            echo "   Password 'Admin123!': " . ($verify ? '✅ CORRECTO' : '❌ INCORRECTO') . "\n";
        } else {
            echo "   ❌ Usuario admin NO encontrado\n";
        }
    } else {
        echo "   ❌ Tabla NO existe\n";
    }
    
} catch (PDOException $e) {
    echo "   ❌ Error de conexión: " . $e->getMessage() . "\n";
}
