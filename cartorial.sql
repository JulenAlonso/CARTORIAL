-- Creación de la base de datos y uso
CREATE DATABASE IF NOT EXISTS `cartorial`
  CHARACTER SET = 'utf8mb4'
  COLLATE = 'utf8mb4_unicode_ci';
USE `cartorial`;

----------------------------------------------------------------------
-- Tabla Usuarios
-- Incluye user_avatar y admin (por defecto 0)
----------------------------------------------------------------------
CREATE TABLE `usuarios` (
  `id_usuario` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(150) DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `telefono` VARCHAR(50) DEFAULT NULL,
  `user_name` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `user_avatar` VARCHAR(255) DEFAULT NULL,
  `admin` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = usuario normal, 1 = administrador',
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

----------------------------------------------------------------------
-- Tabla Vehiculos
----------------------------------------------------------------------
CREATE TABLE `vehiculos` (
  `id_vehiculo` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `matricula` VARCHAR(20) NOT NULL UNIQUE,
  `marca` VARCHAR(100) DEFAULT NULL,
  `modelo` VARCHAR(100) DEFAULT NULL,

  `anio_fabricacion` SMALLINT UNSIGNED DEFAULT NULL,
  `anio_matriculacion` SMALLINT UNSIGNED DEFAULT NULL,
  `anio` SMALLINT UNSIGNED DEFAULT NULL,

  `fecha_compra` DATE DEFAULT NULL,
  `km` INT UNSIGNED DEFAULT 0,
  `cv` SMALLINT UNSIGNED DEFAULT NULL,
  `combustible` VARCHAR(30) DEFAULT NULL,
  `etiqueta` VARCHAR(10) DEFAULT NULL, 
  `precio` DECIMAL(10,2) DEFAULT NULL,
  `precio_segunda_mano` DECIMAL(10,2) DEFAULT NULL,
  `car_avatar` VARCHAR(255) DEFAULT NULL,

  PRIMARY KEY (`id_vehiculo`),
  KEY `idx_vehiculos_id_usuario` (`id_usuario`),
  CONSTRAINT `fk_vehiculos_usuario` FOREIGN KEY (`id_usuario`)
    REFERENCES `usuarios`(`id_usuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

----------------------------------------------------------------------
-- Tabla Notas_calendario
----------------------------------------------------------------------
CREATE TABLE `notas_calendario` (
  `id_nota` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `id_vehiculo` INT UNSIGNED DEFAULT NULL,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `fecha_evento` DATE DEFAULT NULL,
  `hora_evento` TIME DEFAULT NULL,
  `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_nota`),
  KEY `idx_notas_usuario` (`id_usuario`),
  KEY `idx_notas_vehiculo` (`id_vehiculo`),
  CONSTRAINT `fk_notas_usuario` FOREIGN KEY (`id_usuario`)
    REFERENCES `usuarios`(`id_usuario`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_notas_vehiculo` FOREIGN KEY (`id_vehiculo`)
    REFERENCES `vehiculos`(`id_vehiculo`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

----------------------------------------------------------------------
-- Tabla Registros_km
----------------------------------------------------------------------
CREATE TABLE `registros_km` (
  `id_registro_km` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_vehiculo` INT UNSIGNED NOT NULL,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `km_actual` INT UNSIGNED NOT NULL,
  `comentario` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id_registro_km`),
  KEY `idx_regkm_vehiculo` (`id_vehiculo`),
  CONSTRAINT `fk_regkm_vehiculo` FOREIGN KEY (`id_vehiculo`)
    REFERENCES `vehiculos`(`id_vehiculo`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

----------------------------------------------------------------------
-- Tabla Gastos
----------------------------------------------------------------------
CREATE TABLE `gastos` (
  `id_gasto` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_vehiculo` INT UNSIGNED NOT NULL,
  `id_usuario` INT UNSIGNED DEFAULT NULL,
  `fecha_gasto` DATE NOT NULL,
  `tipo_gasto` VARCHAR(50) NOT NULL,
  `importe` DECIMAL(10,2) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  PRIMARY KEY (`id_gasto`),
  KEY `idx_gastos_vehiculo` (`id_vehiculo`),
  KEY `idx_gastos_usuario` (`id_usuario`),
  CONSTRAINT `fk_gastos_vehiculo` FOREIGN KEY (`id_vehiculo`)
    REFERENCES `vehiculos`(`id_vehiculo`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_gastos_usuario` FOREIGN KEY (`id_usuario`)
    REFERENCES `usuarios`(`id_usuario`)
    ON DELETE SET NULL
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

----------------------------------------------------------------------
-- Índices
----------------------------------------------------------------------
CREATE INDEX `ix_vehiculo_marca_modelo` ON `vehiculos` (`marca`, `modelo`);
CREATE INDEX `ix_registroskm_fecha` ON `registros_km` (`fecha_registro`);
CREATE INDEX `ix_gastos_tipo_fecha` ON `gastos` (`tipo_gasto`, `fecha_gasto`);
