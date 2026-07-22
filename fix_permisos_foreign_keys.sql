-- =========================================================
-- Corregir Foreign Keys de permisos_salida
-- =========================================================

USE sena_acceso;

-- Eliminar las foreign keys antiguas si existen
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

-- Verificar y eliminar constraints existentes
SET @query = (
    SELECT CONCAT('ALTER TABLE permisos_salida DROP FOREIGN KEY ', CONSTRAINT_NAME, ';')
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = 'sena_acceso'
      AND TABLE_NAME = 'permisos_salida'
      AND REFERENCED_TABLE_NAME = 'usuarios'
      AND CONSTRAINT_NAME LIKE '%instructor_id%'
    LIMIT 1
);

-- Ejecutar si existe
SET @query = IFNULL(@query, 'SELECT "No hay FK de instructor_id" AS mensaje;');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @query = (
    SELECT CONCAT('ALTER TABLE permisos_salida DROP FOREIGN KEY ', CONSTRAINT_NAME, ';')
    FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = 'sena_acceso'
      AND TABLE_NAME = 'permisos_salida'
      AND REFERENCED_TABLE_NAME = 'usuarios'
      AND CONSTRAINT_NAME LIKE '%usado_por%'
    LIMIT 1
);

SET @query = IFNULL(@query, 'SELECT "No hay FK de usado_por" AS mensaje;');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Modificar los tipos de datos para que coincidan con usuarios_sistema
-- usuarios_sistema.id probablemente es BIGINT UNSIGNED
-- permisos_salida tiene INT UNSIGNED, necesitamos cambiarlos a BIGINT UNSIGNED

ALTER TABLE permisos_salida
  MODIFY COLUMN instructor_id BIGINT UNSIGNED NOT NULL,
  MODIFY COLUMN usado_por BIGINT UNSIGNED NULL;

-- Agregar las nuevas foreign keys apuntando a usuarios_sistema
ALTER TABLE permisos_salida
  ADD CONSTRAINT fk_permisos_instructor 
    FOREIGN KEY (instructor_id) REFERENCES usuarios_sistema(id) ON DELETE RESTRICT,
  ADD CONSTRAINT fk_permisos_validado_por 
    FOREIGN KEY (usado_por) REFERENCES usuarios_sistema(id) ON DELETE SET NULL;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

SELECT 'Foreign keys corregidas exitosamente' AS mensaje;
