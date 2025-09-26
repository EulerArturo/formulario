document.addEventListener('DOMContentLoaded', function() {
    // Navigation functionality
    const navButtons = document.querySelectorAll('.nav-btn');
    const formSections = document.querySelectorAll('.form-section');
    
    // Show first section by default
    if (formSections.length > 0) {
        formSections[0].classList.add('active');
        navButtons[0].classList.add('active');
    }
    
    navButtons.forEach((btn, index) => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons and sections
            navButtons.forEach(b => b.classList.remove('active'));
            formSections.forEach(s => s.classList.remove('active'));
            
            // Add active class to clicked button and corresponding section
            this.classList.add('active');
            if (formSections[index]) {
                formSections[index].classList.add('active');
            }
        });
    });
    
    // Form validation
    const form = document.getElementById('personalForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Date validation
    function validateDate(dateString) {
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(dateString)) return false;
        
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }
    
    // Number validation
    function validateNumber(value) {
        return !isNaN(value) && isFinite(value);
    }
    
    // Email validation
    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    // Form validation function
    function validateForm() {
        let isValid = true;
        const errors = [];
        
        // Validate required fields
        const requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = '#dc3545';
                errors.push(`El campo ${field.name} es requerido`);
            } else {
                field.style.borderColor = '#e0e6ed';
            }
        });
        
        // Validate dates
        const dateFields = document.querySelectorAll('input[type="date"]');
        dateFields.forEach(field => {
            if (field.value && !validateDate(field.value)) {
                isValid = false;
                field.style.borderColor = '#dc3545';
                errors.push(`Formato de fecha inválido en ${field.name}`);
            }
        });
        
        // Validate numbers
        const numberFields = document.querySelectorAll('input[type="number"]');
        numberFields.forEach(field => {
            if (field.value && !validateNumber(field.value)) {
                isValid = false;
                field.style.borderColor = '#dc3545';
                errors.push(`Valor numérico inválido en ${field.name}`);
            }
        });
        
        // Show errors if any
        if (errors.length > 0) {
            showErrors(errors);
        }
        
        return isValid;
    }
    
    // Show error messages
    function showErrors(errors) {
        const errorContainer = document.getElementById('errorContainer');
        if (errorContainer) {
            errorContainer.innerHTML = '<div class="error-message">' + 
                '<strong>Por favor corrija los siguientes errores:</strong><ul>' +
                errors.map(error => `<li>${error}</li>`).join('') +
                '</ul></div>';
            errorContainer.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    // Real-time calculations
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const diasTrabajados = document.getElementById('dias_trabajados');
    
    if (fechaInicio && fechaFin && diasTrabajados) {
        [fechaInicio, fechaFin].forEach(field => {
            field.addEventListener('change', function() {
                if (fechaInicio.value && fechaFin.value) {
                    const inicio = new Date(fechaInicio.value);
                    const fin = new Date(fechaFin.value);
                    const diferencia = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
                    diasTrabajados.value = diferencia > 0 ? diferencia : 0;
                }
            });
        });
    }
    
    // Calculate auxiliary transport
    const tipoContrato = document.getElementById('tipo_contrato');
    const mesada = document.getElementById('mesada');
    const auxTransporte = document.getElementById('aux_transporte');
    
    if (tipoContrato && mesada && auxTransporte) {
        [tipoContrato, mesada].forEach(field => {
            field.addEventListener('change', function() {
                const smlv = 1423500;
                if (tipoContrato.value === 'LAB' && 
                    parseFloat(mesada.value) < (2 * smlv) && 
                    mesada.value) {
                    auxTransporte.value = 200000;
                } else {
                    auxTransporte.value = 0;
                }
            });
        });
    }
    
    // Smooth scrolling for navigation
    navButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelector('.main-container').scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    // Auto-save functionality (optional)
    let autoSaveTimer;
    const formInputs = document.querySelectorAll('input, select, textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Auto-save logic here if needed
                console.log('Auto-saving form data...');
            }, 2000);
        });
    });
});