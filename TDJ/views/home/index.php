<?php include 'views/layouts/header.php'; ?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Todo Frenos Juanjo</h1>
            <p class="hero-subtitle">Tu seguridad es nuestra prioridad</p>
            <div class="hero-buttons">
                <a href="index.php?controller=productos&action=index" class="cta-button">Ver Catálogo</a>
                <a href="#contacto" class="cta-button secondary">Contáctanos</a>
            </div>
        </div>
    </section>

    <!-- Acerca de Nosotros -->
    <section class="about-section">
        <div class="container">
            <h2>Acerca de Nosotros</h2>
            <div class="about-content">
                <div class="about-text">
                    <p>En Todo Frenos Juanjo, ofrecemos repuestos de frenos de alta calidad, como discos, campanas, pastillas, bandas, bombas de freno y más. Nuestra experiencia y compromiso con la seguridad nos hacen la opción número uno en Bogotá. Trabajamos con los mejores productos del mercado para garantizar tu tranquilidad en la carretera.</p>
                    <div class="about-features">
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Productos de alta calidad</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Servicio profesional</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-check-circle"></i>
                            <span>Garantía en todos nuestros productos</span>
                        </div>
                    </div>
                </div>
                <div class="about-image">
                    <img src="public/imagenes/image1.png" alt="Nuestro taller" class="about-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Servicios -->
    <section class="services-section">
        <div class="container">
            <h2>Nuestros Servicios</h2>
            <div class="services-grid">
                <div class="service-card">
                    <i class="fas fa-tools"></i>
                    <h3>Frenos</h3>
                    <p>Venta de frenos garantizados</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-car"></i>
                    <h3>Repuestos</h3>
                    <p>Amplio catálogo de repuestos originales y de alta calidad</p>
                </div>
                <div class="service-card">
                    <i class="fas fa-headset"></i>
                    <h3>Asesoría</h3>
                    <p>Asesoramiento técnico especializado para tu vehículo</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contacto" class="contact-section">
        <div class="container">
            <h2>Contáctanos</h2>
            <div class="contact-grid">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <h3>Ubicación</h3>
                        <p>Carrera 27 # 66 - 92, Bogotá, Colombia</p>
                        <div class="location-details">
                            <p><strong>Barrio:</strong> La Soledad</p>
                            <p><strong>Referencia:</strong> Cerca a la estación de Transmilenio</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <h3>Teléfonos</h3>
                        <p><a href="tel:+573134201207">+57 313 420 1207</a></p>
                        <p><a href="tel:+6016609047">+601 6609047</a></p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <h3>Correo Electrónico</h3>
                        <p><a href="mailto:todofrenosjuanjo@gmail.com">todofrenosjuanjo@gmail.com</a></p>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <h3>Horario de Atención</h3>
                        <p>Lunes a Viernes: 8:00 AM - 6:00 PM</p>
                        <p>Sábados: 8:00 AM - 2:00 PM</p>
                    </div>
                </div>
                <div class="location-image">
                    <img src="public/imagenes/image.png" alt="Ubicación Todo Frenos Juanjo" class="location-img">
                    <div class="location-overlay">
                        <p>¡Visítanos en nuestra tienda!</p>
                        <a href="https://wa.me/573134201207" class="whatsapp-button" target="_blank">
                            <i class="fab fa-whatsapp"></i> Chatea con nosotros
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const navLinks = document.querySelectorAll('.nav-links li a');
    const menuOverlay = document.querySelector('.menu-overlay');

    // Cierra el menú cuando se hace clic en un enlace del menú
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (menuToggle.checked) {
                menuToggle.checked = false;
            }
        });
    });

    // Cierra el menú si se hace clic fuera de él
    menuOverlay.addEventListener('click', () => {
        if (menuToggle.checked) {
            menuToggle.checked = false;
        }
    });
});
</script>

<?php include 'views/layouts/footer.php'; ?>
