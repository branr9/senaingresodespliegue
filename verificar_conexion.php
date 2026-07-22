<?php
define('APP_PATH', __DIR__ . '/app');
define('CONFIG_PATH', __DIR__ . '/config');
define('STORAGE_PATH', __DIR__ . '/storage');
define('APP_DEBUG', true);

require_once APP_PATH . '/models/Database.php';

echo "=== VERIFICACIÓN DE CONEXIÓN ===\n\n";

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    echo "✅ Base de datos conectada: $dbName\n\n";
    
    // Verificar usuario admin
    $sql = "SELECT us.id,
                   us.persona_id,
                   us.username,
                   us.estado,
                   us.intentos_fallidos,
                   us.bloqueado_hasta,
                   p.documento,
                   p.nombres,
                   p.apellidos,
                   p.email,
                   p.estado as persona_estado,
                   crs.codigo as rol
            FROM usuarios_sistema us
            INNER JOIN personas p ON us.persona_id = p.id
            INNER JOIN cat_rol_sistema crs ON us.rol_id = crs.id
            WHERE us.username = :username
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => 'admin']);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ Usuario admin encontrado:\n";
        echo "   ID: {$user['id']}\n";
        echo "   Username: {$user['username']}\n";
        echo "   Email: {$user['email']}\n";
        echo "   Rol: {$user['rol']}\n";
        echo "   Estado usuario_sistema: {$user['estado']}\n";
        echo "   Estado persona: {$user['persona_estado']}\n";
        echo "   Intentos fallidos: {$user['intentos_fallidos']}\n";
        echo "   Bloqueado hasta: " . ($user['bloqueado_hasta'] ?? 'NO') . "\n\n";
        
        // Verificar password
        $sqlPass = "SELECT password_hash FROM usuarios_sistema WHERE username = :username";
        $stmtPass = $pdo->prepare($sqlPass);
        $stmtPass->execute(['username' => 'admin']);
        $hash = $stmtPass->fetchColumn();
        
        echo "Hash almacenado: " . substr($hash, 0, 30) . "...\n";
        
        $password = 'Admin123!';
        $verify = password_verify($password, $hash);
        echo "Verificación de password 'Admin123!': " . ($verify ? '✅ CORRECTO' : '❌ INCORRECTO') . "\n";
        
    } else {
        echo "❌ Usuario admin NO encontrado\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
