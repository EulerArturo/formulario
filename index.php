<?php
require_once 'includes/security.php';
require_once 'includes/dropdown_data.php';

$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Personal - Información Laboral y Pagos</title>
    <link rel="stylesheet" href="assets/style.css">
    <meta name="description" content="Sistema seguro para la gestión de información laboral y de pagos">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <h1>Sistema de Gestión de Personal</h1>
            <nav class="nav-menu">
                <button type="button" class="nav-btn" data-section="info-laboral">INFORMACIÓN LABORAL</button>
                <button type="button" class="nav-btn" data-section="info-pagos">INFORMACIÓN PAGOS</button>
                <button type="button" class="nav-btn" data-section="info-aportes">INFORMACIÓN APORTES</button>
                <button type="button" class="nav-btn" data-section="vigencia-cursos">VIGENCIA DE CURSOS</button>
                <button type="button" class="nav-btn" data-section="extension-1">EXTENSIÓN CONTRATO - 1 TRIMESTRE</button>
                <button type="button" class="nav-btn" data-section="extension-2">EXTENSIÓN CONTRATO - 2 TRIMESTRE</button>
                <button type="button" class="nav-btn" data-section="extension-3">EXTENSIÓN CONTRATO - 3 TRIMESTRE</button>
                <button type="button" class="nav-btn" data-section="extension-4">EXTENSIÓN CONTRATO - 4 TRIMESTRE</button>
            </nav>
        </div>
    </header>

    <main class="main-container">
        <div id="errorContainer"></div>
        
        <form id="personalForm" method="POST" action="process_form.php">
            <input type="hidden" name="csrf_token" value="<?php echo Security::escapeHtml($csrfToken); ?>">
            
            <!-- INFORMACIÓN LABORAL -->
            <section class="form-section active" id="info-laboral">
                <h2 class="section-title">INFORMACIÓN LABORAL</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="sede">Sede *</label>
                        <input type="text" id="sede" name="sede" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cargo">Cargo *</label>
                        <select id="cargo" name="cargo" required>
                            <option value="">Seleccione un cargo</option>
                            <?php foreach (DropdownData::getCargos() as $cargo): ?>
                                <option value="<?php echo Security::escapeHtml($cargo); ?>"><?php echo Security::escapeHtml($cargo); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nivel">Nivel *</label>
                        <select id="nivel" name="nivel" required>
                            <option value="">Seleccione un nivel</option>
                            <?php foreach (DropdownData::getNiveles() as $nivel): ?>
                                <option value="<?php echo Security::escapeHtml($nivel); ?>"><?php echo Security::escapeHtml($nivel); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="calidad">Calidad *</label>
                        <select id="calidad" name="calidad" required>
                            <option value="">Seleccione una calidad</option>
                            <?php foreach (DropdownData::getCalidades() as $calidad): ?>
                                <option value="<?php echo Security::escapeHtml($calidad); ?>"><?php echo Security::escapeHtml($calidad); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="programa">Programa</label>
                        <select id="programa" name="programa">
                            <option value="">Seleccione un programa</option>
                            <?php foreach (DropdownData::getProgramas() as $programa): ?>
                                <option value="<?php echo Security::escapeHtml($programa); ?>"><?php echo Security::escapeHtml($programa); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="area">Área</label>
                        <select id="area" name="area">
                            <option value="">Seleccione un área</option>
                            <?php foreach (DropdownData::getAreas() as $area): ?>
                                <option value="<?php echo Security::escapeHtml($area); ?>"><?php echo Security::escapeHtml($area); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="intramural">Intramural</label>
                        <input type="text" id="intramural" name="intramural">
                    </div>
                    
                    <div class="form-group">
                        <label for="departamento">Departamento</label>
                        <select id="departamento" name="departamento">
                            <option value="">Seleccione un departamento</option>
                            <?php foreach (DropdownData::getDepartamentos() as $departamento): ?>
                                <option value="<?php echo Security::escapeHtml($departamento); ?>"><?php echo Security::escapeHtml($departamento); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="municipio">Municipio</label>
                        <select id="municipio" name="municipio">
                            <option value="">Seleccione un municipio</option>
                            <?php foreach (DropdownData::getMunicipios() as $municipio): ?>
                                <option value="<?php echo Security::escapeHtml($municipio); ?>"><?php echo Security::escapeHtml($municipio); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="servicio">Servicio</label>
                        <select id="servicio" name="servicio">
                            <option value="">Seleccione un servicio</option>
                            <?php foreach (DropdownData::getTiposServicio() as $servicio): ?>
                                <option value="<?php echo Security::escapeHtml($servicio); ?>"><?php echo Security::escapeHtml($servicio); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio">
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin">
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_fin_contrato">Fecha Fin de Contrato</label>
                        <input type="date" id="fecha_fin_contrato" name="fecha_fin_contrato">
                    </div>
                    
                    <div class="form-group">
                        <label for="nivel_riesgo">Nivel de Riesgo</label>
                        <select id="nivel_riesgo" name="nivel_riesgo">
                            <option value="">Seleccione un nivel</option>
                            <?php foreach (DropdownData::getNivelesRiesgo() as $nivel): ?>
                                <option value="<?php echo Security::escapeHtml($nivel); ?>"><?php echo Security::escapeHtml($nivel); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="eps">EPS</label>
                        <input type="text" id="eps" name="eps">
                    </div>
                    
                    <div class="form-group">
                        <label for="arl">ARL</label>
                        <input type="text" id="arl" name="arl">
                    </div>
                    
                    <div class="form-group">
                        <label for="afp">AFP</label>
                        <input type="text" id="afp" name="afp">
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_vencimiento_registro">Fecha Vencimiento de Registro</label>
                        <input type="date" id="fecha_vencimiento_registro" name="fecha_vencimiento_registro">
                    </div>
                    
                    <div class="form-group">
                        <label for="dias_trabajados">Días Trabajados (Calculado)</label>
                        <input type="number" id="dias_trabajados" name="dias_trabajados" class="calculated-field" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero">
                            <option value="">Seleccione</option>
                            <?php foreach (DropdownData::getGeneros() as $genero): ?>
                                <option value="<?php echo Security::escapeHtml($genero); ?>"><?php echo Security::escapeHtml($genero); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="grupo_sanguineo">Grupo Sanguíneo</label>
                        <select id="grupo_sanguineo" name="grupo_sanguineo">
                            <option value="">Seleccione</option>
                            <?php foreach (DropdownData::getGruposSanguineos() as $grupo): ?>
                                <option value="<?php echo Security::escapeHtml($grupo); ?>"><?php echo Security::escapeHtml($grupo); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_contrato">Tipo de Contrato</label>
                        <select id="tipo_contrato" name="tipo_contrato">
                            <option value="">Seleccione</option>
                            <?php foreach (DropdownData::getTiposContrato() as $tipo): ?>
                                <option value="<?php echo Security::escapeHtml($tipo); ?>"><?php echo Security::escapeHtml($tipo); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="">Seleccione</option>
                            <?php foreach (DropdownData::getEstados() as $estado): ?>
                                <option value="<?php echo Security::escapeHtml($estado); ?>"><?php echo Security::escapeHtml($estado); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </section>

            <!-- INFORMACIÓN PAGOS -->
            <section class="form-section" id="info-pagos">
                <h2 class="section-title">INFORMACIÓN PAGOS</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="valor_por_evento">Valor por Evento</label>
                        <input type="number" id="valor_por_evento" name="valor_por_evento" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="mesada">Mesada</label>
                        <input type="number" id="mesada" name="mesada" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="pres_mensual">Pres Mensual (Calculado)</label>
                        <input type="number" id="pres_mensual" name="pres_mensual" class="calculated-field" readonly step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="pres_anual">Pres Anual (Calculado)</label>
                        <input type="number" id="pres_anual" name="pres_anual" class="calculated-field" readonly step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="extras_legales">Extras Legales</label>
                        <input type="number" id="extras_legales" name="extras_legales" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="aux_transporte">Aux. Transporte (Calculado)</label>
                        <input type="number" id="aux_transporte" name="aux_transporte" class="calculated-field" readonly step="0.01">
                    </div>
                    
                    <div class="form-group">
                        <label for="num_cuenta">Núm. Cuenta</label>
                        <input type="text" id="num_cuenta" name="num_cuenta">
                    </div>
                    
                    <div class="form-group">
                        <label for="entidad_bancaria">Entidad Bancaria</label>
                        <input type="text" id="entidad_bancaria" name="entidad_bancaria">
                    </div>
                </div>
            </section>

            <!-- INFORMACIÓN APORTES -->
            <section class="form-section" id="info-aportes">
                <h2 class="section-title">INFORMACIÓN APORTES</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tasa_arl">Tasa ARL (Calculado)</label>
                        <input type="number" id="tasa_arl" name="tasa_arl" class="calculated-field" readonly step="0.0001">
                    </div>
                    
                    <div class="form-group">
                        <label for="ap_salud_mes">Ap. Salud Mes</label>
                        <input type="number" id="ap_salud_mes" name="ap_salud_mes" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="ap_pension_mes">Ap. Pensión Mes</label>
                        <input type="number" id="ap_pension_mes" name="ap_pension_mes" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="ap_arl_mes_ap_caja_mes">Ap. ARL Mes Ap. Caja Mes</label>
                        <input type="number" id="ap_arl_mes_ap_caja_mes" name="ap_arl_mes_ap_caja_mes" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="ap_sena_mes">Ap. SENA Mes</label>
                        <input type="number" id="ap_sena_mes" name="ap_sena_mes" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="ap_icbf_mes">Ap. ICBF Mes</label>
                        <input type="number" id="ap_icbf_mes" name="ap_icbf_mes" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="ap_cesantia_anual">Ap. Cesantía Anual</label>
                        <input type="number" id="ap_cesantia_anual" name="ap_cesantia_anual" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="ap_interes_cesantias_anual">Ap. Interés Cesantías Anual</label>
                        <input type="number" id="ap_interes_cesantias_anual" name="ap_interes_cesantias_anual" step="0.01" min="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="ap_prima_anual">Ap. Prima Anual</label>
                        <input type="number" id="ap_prima_anual" name="ap_prima_anual" step="0.01" min="0">
                    </div>
                </div>
            </section>

            <!-- VIGENCIA DE CURSOS -->
            <section class="form-section" id="vigencia-cursos">
                <h2 class="section-title">VIGENCIA DE CURSOS</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="vigencia_soporte_vital_avanzado">Soporte Vital Avanzado</label>
                        <input type="date" id="vigencia_soporte_vital_avanzado" name="vigencia_soporte_vital_avanzado">
                    </div>
                    
                    <div class="form-group">
                        <label for="vigencia_victimas_violencia_sexual">Atención Víctimas de Violencia Sexual</label>
                        <input type="date" id="vigencia_victimas_violencia_sexual" name="vigencia_victimas_violencia_sexual">
                    </div>
                    
                    <div class="form-group">
                        <label for="vigencia_soporte_vital_basico">Curso Soporte Vital Básico</label>
                        <input type="date" id="vigencia_soporte_vital_basico" name="vigencia_soporte_vital_basico">
                    </div>
                    
                    <div class="form-group">
                        <label for="vigencia_manejo_dolor_cuidados_paliativos">Curso Manejo del Dolor y Cuidados Paliativos</label>
                        <input type="date" id="vigencia_manejo_dolor_cuidados_paliativos" name="vigencia_manejo_dolor_cuidados_paliativos">
                    </div>
                    
                    <div class="form-group">
                        <label for="vigencia_humanizacion_toma_muestras">Curso Humanización Toma de Muestras de Laboratorio</label>
                        <input type="date" id="vigencia_humanizacion_toma_muestras" name="vigencia_humanizacion_toma_muestras">
                    </div>
                    
                    <div class="form-group">
                        <label for="vigencia_manejo_duelo">Manejo del Duelo</label>
                        <input type="date" id="vigencia_manejo_duelo" name="vigencia_manejo_duelo">
                    </div>
                    
                    <div class="form-group">
                        <label for="vigencia_manejo_residuos">Manejo Residuos</label>
                        <input type="date" id="vigencia_manejo_residuos" name="vigencia_manejo_residuos">
                    </div>
                    
                    <div class="form-group">
                        <label for="vigencia_seguridad_vial">Seguridad Vial</label>
                        <input type="date" id="vigencia_seguridad_vial" name="vigencia_seguridad_vial">
                    </div>
                    
                    <div class="form-group">
                        <label for="vigencia_vigiflow">Vigiflow</label>
                        <input type="date" id="vigencia_vigiflow" name="vigencia_vigiflow">
                    </div>
                </div>
            </section>

            <!-- EXTENSIÓN CONTRATO - 1 TRIMESTRE -->
            <section class="form-section" id="extension-1">
                <h2 class="section-title">EXTENSIÓN CONTRATO - 1 TRIMESTRE</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="extension_contrato_1_trimestre">Extensión Contrato</label>
                        <input type="text" id="extension_contrato_1_trimestre" name="extension_contrato_1_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_area_cargo_1_trimestre">Área Cargo</label>
                        <input type="text" id="extension_area_cargo_1_trimestre" name="extension_area_cargo_1_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_fecha_inicio_1_trimestre">Fecha Inicio</label>
                        <input type="date" id="extension_fecha_inicio_1_trimestre" name="extension_fecha_inicio_1_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_fecha_fin_1_trimestre">Fecha Fin</label>
                        <input type="date" id="extension_fecha_fin_1_trimestre" name="extension_fecha_fin_1_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_observaciones_1_trimestre">Observaciones</label>
                        <textarea id="extension_observaciones_1_trimestre" name="extension_observaciones_1_trimestre" rows="4"></textarea>
                    </div>
                </div>
            </section>

            <!-- EXTENSIÓN CONTRATO - 2 TRIMESTRE -->
            <section class="form-section" id="extension-2">
                <h2 class="section-title">EXTENSIÓN CONTRATO - 2 TRIMESTRE</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="extension_contrato_2_trimestre">Extensión Contrato</label>
                        <input type="text" id="extension_contrato_2_trimestre" name="extension_contrato_2_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_area_cargo_2_trimestre">Área Cargo</label>
                        <input type="text" id="extension_area_cargo_2_trimestre" name="extension_area_cargo_2_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_fecha_inicio_2_trimestre">Fecha Inicio</label>
                        <input type="date" id="extension_fecha_inicio_2_trimestre" name="extension_fecha_inicio_2_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_fecha_fin_2_trimestre">Fecha Fin</label>
                        <input type="date" id="extension_fecha_fin_2_trimestre" name="extension_fecha_fin_2_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_observaciones_2_trimestre">Observaciones</label>
                        <textarea id="extension_observaciones_2_trimestre" name="extension_observaciones_2_trimestre" rows="4"></textarea>
                    </div>
                </div>
            </section>

            <!-- EXTENSIÓN CONTRATO - 3 TRIMESTRE -->
            <section class="form-section" id="extension-3">
                <h2 class="section-title">EXTENSIÓN CONTRATO - 3 TRIMESTRE</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="extension_contrato_3_trimestre">Extensión Contrato</label>
                        <input type="text" id="extension_contrato_3_trimestre" name="extension_contrato_3_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_area_cargo_3_trimestre">Área Cargo</label>
                        <input type="text" id="extension_area_cargo_3_trimestre" name="extension_area_cargo_3_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_fecha_inicio_3_trimestre">Fecha Inicio</label>
                        <input type="date" id="extension_fecha_inicio_3_trimestre" name="extension_fecha_inicio_3_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_fecha_fin_3_trimestre">Fecha Fin</label>
                        <input type="date" id="extension_fecha_fin_3_trimestre" name="extension_fecha_fin_3_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_observaciones_3_trimestre">Observaciones</label>
                        <textarea id="extension_observaciones_3_trimestre" name="extension_observaciones_3_trimestre" rows="4"></textarea>
                    </div>
                </div>
            </section>

            <!-- EXTENSIÓN CONTRATO - 4 TRIMESTRE -->
            <section class="form-section" id="extension-4">
                <h2 class="section-title">EXTENSIÓN CONTRATO - 4 TRIMESTRE</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="extension_contrato_4_trimestre">Extensión Contrato</label>
                        <input type="text" id="extension_contrato_4_trimestre" name="extension_contrato_4_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_area_cargo_4_trimestre">Área Cargo</label>
                        <input type="text" id="extension_area_cargo_4_trimestre" name="extension_area_cargo_4_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_fecha_inicio_4_trimestre">Fecha Inicio</label>
                        <input type="date" id="extension_fecha_inicio_4_trimestre" name="extension_fecha_inicio_4_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_fecha_fin_4_trimestre">Fecha Fin</label>
                        <input type="date" id="extension_fecha_fin_4_trimestre" name="extension_fecha_fin_4_trimestre">
                    </div>
                    
                    <div class="form-group">
                        <label for="extension_observaciones_4_trimestre">Observaciones</label>
                        <textarea id="extension_observaciones_4_trimestre" name="extension_observaciones_4_trimestre" rows="4"></textarea>
                    </div>
                </div>
            </section>

            <div class="submit-section">
                <button type="submit" class="submit-btn">
                    <span class="btn-text">Guardar Información</span>
                    <span class="loading" style="display: none;"></span>
                </button>
            </div>
        </form>
    </main>

    <script src="assets/script.js"></script>
    <script>
        // Enhanced form submission with AJAX
        document.getElementById('personalForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('.submit-btn');
            const btnText = submitBtn.querySelector('.btn-text');
            const loading = submitBtn.querySelector('.loading');
            
            // Show loading state
            btnText.style.display = 'none';
            loading.style.display = 'inline-block';
            submitBtn.disabled = true;
            
            const formData = new FormData(this);
            
            fetch('process_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('errorContainer').innerHTML = 
                        '<div class="success-message">✓ ' + data.message + '</div>';
                    this.reset();
                } else {
                    document.getElementById('errorContainer').innerHTML = 
                        '<div class="error-message">✗ ' + data.error + 
                        (data.details ? '<ul>' + data.details.map(d => '<li>' + d + '</li>').join('') + '</ul>' : '') +
                        '</div>';
                }
                document.getElementById('errorContainer').scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                document.getElementById('errorContainer').innerHTML = 
                    '<div class="error-message">✗ Error de conexión. Por favor intente nuevamente.</div>';
                document.getElementById('errorContainer').scrollIntoView({ behavior: 'smooth' });
            })
            .finally(() => {
                // Reset button state
                btnText.style.display = 'inline';
                loading.style.display = 'none';
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>