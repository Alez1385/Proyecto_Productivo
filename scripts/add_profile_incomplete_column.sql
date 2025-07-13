-- Script para agregar la columna perfil_incompleto a la tabla usuario
-- Ejecutar este script en la base de datos para habilitar la funcionalidad de notificación

ALTER TABLE usuario 
ADD COLUMN perfil_incompleto TINYINT(1) DEFAULT 0 
COMMENT 'Indica si el usuario necesita completar su perfil';

-- Actualizar usuarios existentes que tengan datos incompletos
UPDATE usuario 
SET perfil_incompleto = 1 
WHERE nombre IS NULL 
   OR apellido IS NULL 
   OR telefono IS NULL 
   OR direccion IS NULL 
   OR fecha_nac IS NULL; 