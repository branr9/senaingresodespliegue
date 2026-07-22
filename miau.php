<?php

// 1. FASE DE DIVISIÓN (Divide y vencerás)
function mergeSort($array) {
    // Si la lista tiene 1 o 0 elementos, ya se considera ordenada. ¡Es nuestra condición de parada!
    if (count($array) <= 1) {
        return $array;
    }

    // Buscamos la mitad exacta para cortar la lista
    $mitad = floor(count($array) / 2);

    // Cortamos el array en dos pedazos (Izquierda y Derecha)
    // array_slice extrae una parte del array
    $izquierda = array_slice($array, 0, $mitad);
    $derecha = array_slice($array, $mitad);

    // Nos llamamos a nosotros mismos (recursividad) para seguir rompiendo las mitades
    // hasta que queden elementos individuales
    $izquierda = mergeSort($izquierda);
    $derecha = mergeSort($derecha);

    // Pasamos a la fase de mezcla con las partes ya divididas
    return mezclar($izquierda, $derecha);
}

// 2. FASE DE MEZCLA (El Merge)
function mezclar($izquierda, $derecha) {
    $resultado = [];

    // Mientras haya competidores en ambas filas...
    while (count($izquierda) > 0 && count($derecha) > 0) {
        
        // "Competencia de los primeros de la fila"
        if ($izquierda[0] <= $derecha[0]) {
            // Si el de la izquierda es menor, lo sacamos de su fila (array_shift) 
            // y lo guardamos en el resultado final
            $resultado[] = array_shift($izquierda);
        } else {
            // Si el de la derecha es menor, sale de su fila al resultado final
            $resultado[] = array_shift($derecha);
        }
    }

    // 3. LIMPIEZA
    // Si una fila se vació primero, simplemente tomamos los que sobraron en la otra 
    // y los pegamos al final del resultado
    while (count($izquierda) > 0) {
        $resultado[] = array_shift($izquierda);
    }
    while (count($derecha) > 0) {
        $resultado[] = array_shift($derecha);
    }

    return $resultado;
}

// --- EJEMPLO DE USO PARA TU EXPOSICIÓN ---
$miArray = [6, 4, 8, 1];
echo "Array original: " . implode(", ", $miArray) . "\n";

$arrayOrdenado = mergeSort($miArray);
echo "Array ordenado: " . implode(", ", $arrayOrdenado) . "\n";

?>