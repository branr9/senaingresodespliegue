<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/models/Database.php';

$db = Database::getInstance()->getConnection();

echo "=== Estructura de cat_roles ===\n";
$stmt = $db->query("DESCRIBE cat_roles");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")\n";
}

echo "\n=== Roles disponibles ===\n";
$stmt = $db->query("SELECT * FROM cat_roles");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($roles);

echo "\n=== Usuario admin ===\n";
$stmt = $db->query("SELECT * FROM usuarios_sistema WHERE email = 'admin@sena.edu.co'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Email: " . $user['email'] . "\n";
echo "Rol ID: " . $user['rol_id'] . "\n";
