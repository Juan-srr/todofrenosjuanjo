// Cambia la cantidad en el input de cantidad
function cambiarCantidad(btn, cambio) {
    const input = btn.parentNode.querySelector('.cantidad');
    let valor = parseInt(input.value) + cambio;
    if (valor < 0) valor = 0;
    input.value = valor;
}

// MEJORAS DEL MENÚ HAMBURGUESA
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.querySelectorAll('.nav-links li a');
    const menuOverlay = document.querySelector('.menu-overlay');
    const menuIcon = document.querySelector('.menu-icon');
    const body = document.body;

    // Función para cerrar el menú
    function closeMenu() {
        if (menuToggle.checked) {
            menuToggle.checked = false;
            body.style.overflow = '';
        }
    }

    // Función para abrir el menú
    function openMenu() {
        if (!menuToggle.checked) {
            menuToggle.checked = true;
            body.style.overflow = 'hidden'; // Previene scroll del body
        }
    }

    // Cierra el menú cuando se hace clic en un enlace
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            // Agregar efecto de clic
            link.style.transform = 'scale(0.95)';
            setTimeout(() => {
                link.style.transform = '';
            }, 150);
            
            // Cerrar menú después de un pequeño delay para ver la animación
            setTimeout(closeMenu, 200);
        });
    });

    // Cierra el menú si se hace clic en el overlay
    menuOverlay.addEventListener('click', closeMenu);

    // Navegación por teclado
    menuIcon.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            if (menuToggle.checked) {
                closeMenu();
            } else {
                openMenu();
            }
        }
    });

    // Cerrar menú con la tecla Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && menuToggle.checked) {
            closeMenu();
        }
    });

    // Detectar cambio de orientación en móviles
    window.addEventListener('orientationchange', () => {
        setTimeout(closeMenu, 500);
    });

    // Cerrar menú al redimensionar la ventana (si pasa de móvil a desktop)
    window.addEventListener('resize', () => {
        if (window.innerWidth > 700 && menuToggle.checked) {
            closeMenu();
        }
    });

    // Agregar indicador de página activa
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.classList.add('active');
        }
    });

    // Efecto de hover mejorado para el icono
    menuIcon.addEventListener('mouseenter', () => {
        if (!menuToggle.checked) {
            menuIcon.style.transform = 'scale(1.1) rotate(5deg)';
        }
    });

    menuIcon.addEventListener('mouseleave', () => {
        if (!menuToggle.checked) {
            menuIcon.style.transform = 'scale(1) rotate(0deg)';
        }
    });

    // Agregar feedback táctil en móviles
    navLinks.forEach(link => {
        link.addEventListener('touchstart', () => {
            link.style.transform = 'scale(0.95)';
        });
        
        link.addEventListener('touchend', () => {
            setTimeout(() => {
                link.style.transform = '';
            }, 150);
        });
    });

    // Prevenir scroll del body cuando el menú está abierto
    menuToggle.addEventListener('change', () => {
        if (menuToggle.checked) {
            body.style.overflow = 'hidden';
        } else {
            body.style.overflow = '';
        }
    });
});

function confirmarEliminacion() {
    return confirm('¿Estás seguro que quieres eliminar este producto?');
}

// FUNCIONALIDAD PARA LIMPIAR CAMPOS DE FORMULARIOS DE AUTENTICACIÓN
document.addEventListener('DOMContentLoaded', function() {
    // Limpiar campos de formularios de autenticación al cargar la página
    limpiarCamposAutenticacion();
    
    // Limpiar campos cuando el usuario sale de la página
    window.addEventListener('beforeunload', function() {
        limpiarCamposAutenticacion();
    });
    
    // Limpiar campos cuando se oculta la página (cambio de pestaña, minimizar, etc.)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            limpiarCamposAutenticacion();
        }
    });
    
    // Limpiar campos al hacer clic en enlaces de navegación
    const navLinks = document.querySelectorAll('a[href]');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Solo limpiar si no es un enlace de autenticación
            const href = this.getAttribute('href');
            if (!href.includes('auth') && !href.includes('login') && !href.includes('registro')) {
                setTimeout(limpiarCamposAutenticacion, 100);
            }
        });
    });
});

function limpiarCamposAutenticacion() {
    // Limpiar campos del formulario de login
    const loginForm = document.querySelector('form[action*="authenticate"]');
    if (loginForm) {
        const inputs = loginForm.querySelectorAll('input[type="text"], input[type="password"], input[type="email"]');
        inputs.forEach(input => {
            input.value = '';
            // Desactivar autocompletado adicional
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('autocorrect', 'off');
            input.setAttribute('autocapitalize', 'off');
            input.setAttribute('spellcheck', 'false');
        });
    }
    
    // Limpiar campos del formulario de registro
    const registroForm = document.querySelector('form[action*="store"]');
    if (registroForm) {
        const inputs = registroForm.querySelectorAll('input[type="text"], input[type="password"], input[type="email"]');
        inputs.forEach(input => {
            input.value = '';
            // Desactivar autocompletado adicional
            input.setAttribute('autocomplete', 'off');
            input.setAttribute('autocorrect', 'off');
            input.setAttribute('autocapitalize', 'off');
            input.setAttribute('spellcheck', 'false');
        });
    }
    
    // Limpiar cualquier campo de verificación de código
    const codigoInputs = document.querySelectorAll('input[name*="codigo"], input[name*="verificacion"]');
    codigoInputs.forEach(input => {
        input.value = '';
        input.setAttribute('autocomplete', 'off');
    });
}