<?php
// Generar hash para Admin123!
$password = 'Admin123!';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\n";
echo "SQL para actualizar:\n";
echo "UPDATE usuarios_sistema SET password_hash = '$hash' WHERE email = 'admin@sena.edu.co';\n";
?>
