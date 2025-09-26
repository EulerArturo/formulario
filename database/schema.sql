-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS `db_gestion_personal` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Usar la base de datos
USE `db_gestion_personal`;

-- Creación de la tabla principal de personal
CREATE TABLE IF NOT EXISTS `personal` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(255) UNIQUE,
    `sede` VARCHAR(255),
    `cargo` VARCHAR(255),
    `nivel` VARCHAR(255),
    `calidad` VARCHAR(255),
    `programa` VARCHAR(255),
    `area` VARCHAR(255),
    `intramural` VARCHAR(255),
    `departamento` VARCHAR(255),
    `municipio` VARCHAR(255),
    `servicio` VARCHAR(255),
    `fecha_inicio` DATE,
    `fecha_fin` DATE,
    `fecha_fin_contrato` DATE,
    `nivel_riesgo` VARCHAR(255),
    `eps` VARCHAR(255),
    `arl` VARCHAR(255),
    `afp` VARCHAR(255),
    `fecha_vencimiento_registro` DATE,
    `dias_trabajados` INT,
    `valor_por_evento` DECIMAL(10, 2),
    `mesada` DECIMAL(10, 2),
    `pres_mensual` DECIMAL(10, 2),
    `pres_anual` DECIMAL(10, 2),
    `extras_legales` DECIMAL(10, 2),
    `aux_transporte` DECIMAL(10, 2),
    `num_cuenta` VARCHAR(255),
    `entidad_bancaria` VARCHAR(255),
    `tasa_arl` DECIMAL(5, 4),
    `ap_salud_mes` DECIMAL(10, 2),
    `ap_pension_mes` DECIMAL(10, 2),
    `ap_arl_mes_ap_caja_mes` DECIMAL(10, 2),
    `ap_sena_mes` DECIMAL(10, 2),
    `ap_icbf_mes` DECIMAL(10, 2),
    `ap_cesantia_anual` DECIMAL(10, 2),
    `ap_interes_cesantias_anual` DECIMAL(10, 2),
    `ap_prima_anual` DECIMAL(10, 2),
    `vigencia_soporte_vital_avanzado` DATE,
    `vigencia_victimas_violencia_sexual` DATE,
    `vigencia_soporte_vital_basico` DATE,
    `vigencia_manejo_dolor_cuidados_paliativos` DATE,
    `vigencia_humanizacion_toma_muestras` DATE,
    `vigencia_manejo_duelo` DATE,
    `vigencia_manejo_residuos` DATE,
    `vigencia_seguridad_vial` DATE,
    `vigencia_vigiflow` DATE,
    -- Campos para Extensiones de Contrato
    `extension_contrato_1_trimestre` VARCHAR(255),
    `extension_area_cargo_1_trimestre` VARCHAR(255),
    `extension_fecha_inicio_1_trimestre` DATE,
    `extension_fecha_fin_1_trimestre` DATE,
    `extension_observaciones_1_trimestre` TEXT,
    `extension_contrato_2_trimestre` VARCHAR(255),
    `extension_area_cargo_2_trimestre` VARCHAR(255),
    `extension_fecha_inicio_2_trimestre` DATE,
    `extension_fecha_fin_2_trimestre` DATE,
    `extension_observaciones_2_trimestre` TEXT,
    `extension_contrato_3_trimestre` VARCHAR(255),
    `extension_area_cargo_3_trimestre` VARCHAR(255),
    `extension_fecha_inicio_3_trimestre` DATE,
    `extension_fecha_fin_3_trimestre` DATE,
    `extension_observaciones_3_trimestre` TEXT,
    `extension_contrato_4_trimestre` VARCHAR(255),
    `extension_area_cargo_4_trimestre` VARCHAR(255),
    `extension_fecha_inicio_4_trimestre` DATE,
    `extension_fecha_fin_4_trimestre` DATE,
    `extension_observaciones_4_trimestre` TEXT,
    -- Campos adicionales
    `genero` VARCHAR(20),
    `grupo_sanguineo` VARCHAR(5),
    `poliza` VARCHAR(20),
    `smlv` INT DEFAULT 1423500,
    `estado` VARCHAR(50),
    `tipo_contrato` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;









-- Crear índices para optimizar la búsqueda
CREATE INDEX idx_cargo ON personal(cargo);
CREATE INDEX idx_municipio ON personal(municipio);
CREATE INDEX idx_contrato ON personal(tipo_contrato);
CREATE INDEX idx_estado ON personal(estado);
CREATE INDEX idx_fecha_inicio ON personal(fecha_inicio);

-- Vista para cálculos automáticos
CREATE VIEW personal_calculated AS
SELECT 
    p.*,
    CASE 
        WHEN p.codigo IS NULL OR p.codigo = '' THEN ''
        ELSE CONCAT(p.codigo, '-', p.id, ' ', YEAR(CURDATE()))
    END AS codigo_calculado,
    
    CASE 
        WHEN p.fecha_inicio IS NULL OR p.fecha_fin IS NULL THEN 0
        ELSE DATEDIFF(p.fecha_fin, p.fecha_inicio) + 1
    END AS dias_trabajados_calc,
    
    CASE
        WHEN p.tipo_contrato = 'LAB' AND p.mesada < (2 * p.smlv) AND p.mesada IS NOT NULL THEN 200000
        ELSE 0
    END AS aux_transporte_calc
    
FROM personal p;







-- Trigger para actualizar campos calculados
DELIMITER //
CREATE TRIGGER update_calculated_fields 
BEFORE INSERT ON personal
FOR EACH ROW
BEGIN
    -- Calcular días trabajados
    IF NEW.fecha_inicio IS NOT NULL AND NEW.fecha_fin IS NOT NULL THEN
        SET NEW.dias_trabajados = DATEDIFF(NEW.fecha_fin, NEW.fecha_inicio) + 1;
    END IF;
    
    -- Calcular auxilio de transporte
    IF NEW.tipo_contrato = 'LAB' AND NEW.mesada < (2 * COALESCE(NEW.smlv, 1423500)) AND NEW.mesada IS NOT NULL THEN
        SET NEW.aux_transporte = 200000;
    ELSE
        SET NEW.aux_transporte = 0;
    END IF;
    
    -- Generar código si no existe
    IF NEW.codigo IS NULL OR NEW.codigo = '' THEN
        SET NEW.codigo = CONCAT('EMP-', YEAR(CURDATE()));
    END IF;
END//










CREATE TRIGGER update_calculated_fields_update
BEFORE UPDATE ON personal
FOR EACH ROW
BEGIN
    -- Calcular días trabajados
    IF NEW.fecha_inicio IS NOT NULL AND NEW.fecha_fin IS NOT NULL THEN
        SET NEW.dias_trabajados = DATEDIFF(NEW.fecha_fin, NEW.fecha_inicio) + 1;
    END IF;
    
    -- Calcular auxilio de transporte
    IF NEW.tipo_contrato = 'LAB' AND NEW.mesada < (2 * COALESCE(NEW.smlv, 1423500)) AND NEW.mesada IS NOT NULL THEN
        SET NEW.aux_transporte = 200000;
    ELSE
        SET NEW.aux_transporte = 0;
    END IF;
END//
DELIMITER ;