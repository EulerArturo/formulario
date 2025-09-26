<?php
class DropdownData {
    
    public static function getMunicipios() {
        return [
            'PASTO', 'TUMACO', 'IPIALES', 'TUQUERRES', 'BARBACOAS', 'SAMANIEGO',
            'LA UNION', 'SANDONA', 'CONSACA', 'YACUANQUER', 'TANGUA', 'FUNES',
            'GUAITARILLA', 'IMUES', 'POTOSI', 'PUPIALES', 'GUALMATÁN', 'ALDANA',
            'CÓRDOBA', 'CUASPUD', 'CUMBAL', 'RICAURTE', 'MALLAMA', 'PROVIDENCIA',
            'BUESACO', 'CHACHAGÜÍ', 'EL TAMBO', 'LA FLORIDA', 'NARIÑO', 'TANGUA',
            'ANCUYÁ', 'LINARES', 'SANDONÁ', 'CONSACÁ', 'YACUANQUER'
        ];
    }
    
    public static function getCargos() {
        return [
            'MÉDICO GENERAL', 'ENFERMERO', 'AUXILIAR DE ENFERMERÍA', 'ODONTÓLOGO',
            'PSICÓLOGO', 'TRABAJADOR SOCIAL', 'FISIOTERAPEUTA', 'NUTRICIONISTA',
            'BACTERIÓLOGO', 'TÉCNICO EN SISTEMAS', 'AUXILIAR ADMINISTRATIVO',
            'CONDUCTOR', 'VIGILANTE', 'SERVICIOS GENERALES', 'COORDINADOR',
            'DIRECTOR', 'SUBDIRECTOR', 'JEFE DE ÁREA'
        ];
    }
    
    public static function getNiveles() {
        return [
            'OPERATIVO ESPECIALIZADO',
            'SOPORTE',
            'COORDINADOR/MANDO MEDIO',
            'OPERATIVO TÉCNICO/ADMINISTRATIVO',
            'DIRECTIVO'
        ];
    }
    
    public static function getCalidades() {
        return [
            'ASISTENCIAL (EN SALUD)',
            'ADMINISTRATIVO (EN SALUD)'
        ];
    }
    
    public static function getProgramas() {
        return [
            'PROGRAMA DE SALUD PÚBLICA',
            'PROGRAMA DE ATENCIÓN PRIMARIA',
            'PROGRAMA DE URGENCIAS',
            'PROGRAMA DE HOSPITALIZACIÓN',
            'PROGRAMA DE CONSULTA EXTERNA',
            'PROGRAMA ADMINISTRATIVO'
        ];
    }
    
    public static function getAreas() {
        return [
            'URGENCIAS', 'CONSULTA EXTERNA', 'HOSPITALIZACIÓN', 'CIRUGÍA',
            'LABORATORIO', 'RADIOLOGÍA', 'FARMACIA', 'ADMINISTRACIÓN',
            'SISTEMAS', 'MANTENIMIENTO', 'SERVICIOS GENERALES'
        ];
    }
    
    public static function getDepartamentos() {
        return ['NARIÑO', 'CAUCA', 'VALLE'];
    }
    
    public static function getTiposServicio() {
        return ['ATENCIÓN A PACIENTE', 'HORA DE SERVICIO', 'NO APLICA'];
    }
    
    public static function getNivelesRiesgo() {
        return ['I', 'II', 'III', 'IV', 'V'];
    }
    
    public static function getGeneros() {
        return ['MASCULINO', 'FEMENINO'];
    }
    
    public static function getGruposSanguineos() {
        return ['A+', 'A−', 'B+', 'B−', 'AB+', 'AB−', 'O+', 'O−'];
    }
    
    public static function getEstados() {
        return [
            'ACTIVO', 'ENVIADO PARA FIRMA', 'TERMINADO', 
            'PROYECTAR CONTRATO', 'ACTIVO SIN FIRMA', 'LIQUIDADO'
        ];
    }
    
    public static function getTiposContrato() {
        return ['OPS', 'LAB'];
    }
    
    public static function getPolizas() {
        return ['TIENE', 'NO TIENE', 'NO APLICA'];
    }
    
    // Cálculos del servidor
    public static function calcularDiasTrabajados($fechaInicio, $fechaFin) {
        if (empty($fechaInicio) || empty($fechaFin)) {
            return 0;
        }
        
        $inicio = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);
        $diferencia = $inicio->diff($fin);
        
        return $diferencia->days + 1;
    }
    
    public static function calcularAuxTransporte($tipoContrato, $mesada) {
        $smlv = 1423500;
        if ($tipoContrato === 'LAB' && $mesada < (2 * $smlv) && !empty($mesada)) {
            return 200000;
        }
        return 0;
    }
    
    public static function calcularPresMensual($campos) {
        $suma = 0;
        $camposAnuales = ['ap_cesantia_anual', 'ap_interes_cesantias_anual', 'ap_prima_anual'];
        
        foreach ($campos as $campo => $valor) {
            if (in_array($campo, $camposAnuales)) {
                $suma += ($valor / 12);
            } else {
                $suma += $valor;
            }
        }
        
        return $suma;
    }
    
    public static function calcularPresAnual($presMensual, $diasTrabajados) {
        if (empty($presMensual) || empty($diasTrabajados)) {
            return 0;
        }
        
        return $presMensual * ($diasTrabajados / 30);
    }
}
?>