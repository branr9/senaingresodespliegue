<?php
/**
 * Script temporal para generar hash de contraseña
 */

$password = 'Admin123!';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Contraseña: {$password}\n";
echo "Hash generado: {$hash}\n\n";

// Verificar que funciona
if (password_verify($password, $hash)) {
    echo "✅ Verificación exitosa!\n";
} else {
    echo "❌ Verificación fallida!\n";
}

// Verificar el hash que está en la BD
$oldHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
echo "\nProbando hash de la BD:\n";
if (password_verify($password, $oldHash)) {
    echo "✅ El hash de la BD funciona!\n";
} else {
    echo "❌ El hash de la BD NO funciona con 'Admin123!'\n";
    echo "Probando con 'password'...\n";
    if (password_verify('password', $oldHash)) {
        echo "✅ El hash corresponde a 'password'\n";
    }
}
?>
