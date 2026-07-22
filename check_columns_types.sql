-- Verificar tipos de datos
USE sena_acceso;

SELECT 
    'usuarios_sistema' as tabla,
    COLUMN_NAME, 
    COLUMN_TYPE, 
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'sena_acceso'
  AND TABLE_NAME = 'usuarios_sistema'
  AND COLUMN_NAME = 'id';

SELECT 
    'permisos_salida' as tabla,
    COLUMN_NAME, 
    COLUMN_TYPE, 
    IS_NULLABLE
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'sena_acceso'
  AND TABLE_NAME = 'permisos_salida'
  AND COLUMN_NAME IN ('instructor_id', 'usado_por');
