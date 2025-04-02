/**
 * 2007-2025 PrestaShop SA y Contribuidores
 *
 * AVISO DE LICENCIA
 *
 * Este código fuente está protegido por derechos de autor.
 * El uso está permitido bajo licencia.
 *
 * @author    PrestaShop SA <contacto@prestashop.com>
 * @copyright 2007-2025 PrestaShop SA y Contribuidores
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar slider
    const sliderResponsivo = new SliderResponsivo();
    sliderResponsivo.init();
});

class SliderResponsivo {
    constructor() {
        // Elementos DOM
        this.slider = document.querySelector('.slider-responsivo');
        if (!this.slider) return;
        
        this.slidesContainer = this.slider.querySelector('.slides-container');
        this.slides = this.slider.querySelectorAll('.slide');
        this.totalSlides = this.slides.length;
        if (this.totalSlides <= 0) return;
        
        this.prevBtn = this.slider.querySelector('.slider-control.prev');
        this.nextBtn = this.slider.querySelector('.slider-control.next');
        this.pagination = this.slider.querySelector('.slider-pagination');
        this.dots = this.slider.querySelectorAll('.slider-dot');
        
        // Estado
        this.currentSlide = 0;
        this.isTransitioning = false;
        this.autoplayInterval = null;
        this.touchStartX = 0;
        this.touchEndX = 0;
        this.slideWidth = 0;
        this.isVisible = true;
        
        // Configuración desde PHP
        this.config = window.sliderResponsivoConfig || {
            effect: 'slide',
            breakpoint: 768,
            autoplay: 1,
            autoplaySpeed: 5000
        };
        
        // Ocultar controles si solo hay una imagen
        if (this.totalSlides <= 1) {
            if (this.prevBtn) this.prevBtn.style.display = 'none';
            if (this.nextBtn) this.nextBtn.style.display = 'none';
            if (this.pagination) this.pagination.style.display = 'none';
        }
        
        // Marcar slider como cargando
        this.slider.classList.add('loading');
    }
    
    init() {
        if (!this.slider || this.totalSlides <= 0) return;
        
        // Precarga de imágenes para mejorar la experiencia
        this.preloadImages().then(() => {
            // Quitar clase de carga
            this.slider.classList.remove('loading');
            
            // Aplicar imágenes responsivas primero
            this.applyResponsiveImages();
            
            // Mostrar primer slide
            this.showSlide(0);
            
            // No configurar controles si solo hay una imagen
            if (this.totalSlides > 1) {
                // Establecer eventos de botones de navegación
                if (this.prevBtn && this.nextBtn) {
                    this.setupControls();
                }
                
                // Establecer eventos de paginación (dots)
                if (this.dots && this.dots.length > 0) {
                    this.setupPagination();
                }
                
                // Establecer eventos táctiles para dispositivos móviles
                this.setupTouchEvents();
                
                // Iniciar autoplay si está habilitado
                if (this.config.autoplay) {
                    this.startAutoplay();
                }
                
                // Manejar pausa al pasar el mouse
                this.handlePauseOnHover();
                
                // Manejar visibilidad de página
                this.handlePageVisibility();
            }
            
            // Manejar cambio de tamaño de pantalla - siempre necesario incluso con 1 sola imagen
            this.handleWindowResize();
            
            // Notificar que el slider está listo (para posible integración con otros scripts)
            this.dispatchEvent('sliderReady');
        });
    }
    
    /**
     * Precarga todas las imágenes del slider
     */
    preloadImages() {
        return new Promise((resolve) => {
            const promises = [];
            const slideImages = this.slider.querySelectorAll('img[data-desktop], img[data-mobile]');
            
            slideImages.forEach(img => {
                const mobileSrc = img.getAttribute('data-mobile');
                const desktopSrc = img.getAttribute('data-desktop');
                
                // Precargar imagen de escritorio
                if (desktopSrc) {
                    promises.push(this.preloadImage(desktopSrc));
                }
                
                // Precargar imagen de móvil
                if (mobileSrc) {
                    promises.push(this.preloadImage(mobileSrc));
                }
            });
            
            // Esperar a que todas las imágenes se carguen o al menos 3 segundos
            Promise.race([
                Promise.all(promises),
                new Promise(resolve => setTimeout(resolve, 3000))
            ]).then(() => {
                resolve();
            });
        });
    }
    
    /**
     * Precarga una imagen individual
     */
    preloadImage(src) {
        return new Promise((resolve) => {
            const img = new Image();
            img.onload = img.onerror = function() {
                resolve();
            };
            img.src = src;
        });
    }
    
    /**
     * Configurar controles de navegación
     */
    setupControls() {
        // Eventos de botones
        this.prevBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.prevSlide();
        });
        
        this.nextBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.nextSlide();
        });
        
        // Accesibilidad: Navegación con teclado
        this.slider.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                this.prevSlide();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                this.nextSlide();
            }
        });
        
        // Agregar tabindex para enfoque con teclado
        this.slider.setAttribute('tabindex', '0');
    }
    
    /**
     * Configurar paginación (dots)
     */
    setupPagination() {
        // Asignar eventos a cada dot
        Array.from(this.dots).forEach((dot, index) => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                this.showSlide(index);
            });
            
            // Accesibilidad: Añadir atributos ARIA
            dot.setAttribute('aria-label', `Ir a la imagen ${index + 1} de ${this.totalSlides}`);
            dot.setAttribute('role', 'button');
            dot.setAttribute('tabindex', '0');
            
            // Permitir activación con Enter o Space
            dot.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.showSlide(index);
                }
            });
        });
    }
    
    /**
     * Mostrar un slide específico
     */
    showSlide(index) {
        if (this.isTransitioning) return;
        
        this.isTransitioning = true;
        
        // Validar índice
        if (index < 0) {
            index = this.totalSlides - 1;
        } else if (index >= this.totalSlides) {
            index = 0;
        }
        
        // Remover clase active de todos los slides
        this.slides.forEach(slide => {
            slide.classList.remove('active');
            slide.setAttribute('aria-hidden', 'true');
            // Limpiar clases de efectos anteriores
            slide.classList.remove('fade-out', 'fade-in', 'zoom-out', 'zoom-in', 
                                  'slide-left', 'slide-right', 'slide-from-left', 'slide-from-right');
        });
        
        // Remover clase active de todos los dots
        if (this.dots && this.dots.length > 0) {
            Array.from(this.dots).forEach(dot => {
                dot.classList.remove('active');
                dot.setAttribute('aria-current', 'false');
            });
            
            // Activar dot actual
            this.dots[index].classList.add('active');
            this.dots[index].setAttribute('aria-current', 'true');
        }
        
        // Aplicar efecto según configuración
        this.applyTransitionEffect(index);
        
        // Actualizar slide actual
        this.currentSlide = index;
        
        // Actualizar anuncio para lectores de pantalla
        this.announceSlideChange(index);
        
        // Después de la transición
        setTimeout(() => {
            this.isTransitioning = false;
            this.dispatchEvent('slideChanged', { index: index });
        }, 800); // 800ms para coincidir con la duración de la transición CSS
    }
    
    /**
     * Aplicar el efecto de transición
     */
    applyTransitionEffect(index) {
        const currentSlide = this.slides[this.currentSlide];
        const nextSlide = this.slides[index];
        
        // Añadir clases según el efecto seleccionado
        switch (this.config.effect) {
            case 'fade':
                currentSlide.classList.add('fade-out');
                nextSlide.classList.add('active', 'fade-in');
                break;
                
            case 'zoom':
                currentSlide.classList.add('zoom-out');
                nextSlide.classList.add('active', 'zoom-in');
                break;
                
            case 'slide':
            default:
                // Determinar dirección
                const direction = index > this.currentSlide ? 'right' : 'left';
                
                if (this.currentSlide !== index) {
                    currentSlide.classList.add(direction === 'right' ? 'slide-left' : 'slide-right');
                }
                
                nextSlide.classList.add('active', direction === 'right' ? 'slide-from-right' : 'slide-from-left');
                break;
        }
        
        // Actualizar ARIA
        nextSlide.setAttribute('aria-hidden', 'false');
    }
    
    /**
     * Actualizar anuncio para lectores de pantalla
     */
    announceSlideChange(index) {
        // Crear o actualizar elemento para anuncios ARIA
        let announce = this.slider.querySelector('.sr-only-announcement');
        if (!announce) {
            announce = document.createElement('div');
            announce.className = 'sr-only-announcement';
            announce.setAttribute('aria-live', 'polite');
            announce.setAttribute('aria-atomic', 'true');
            announce.style.position = 'absolute';
            announce.style.width = '1px';
            announce.style.height = '1px';
            announce.style.margin = '-1px';
            announce.style.padding = '0';
            announce.style.overflow = 'hidden';
            announce.style.clip = 'rect(0, 0, 0, 0)';
            announce.style.border = '0';
            this.slider.appendChild(announce);
        }
        
        // Obtener título si está disponible
        let slideTitle = '';
        const slideImg = this.slides[index].querySelector('img');
        if (slideImg) {
            slideTitle = slideImg.getAttribute('alt') || `Imagen ${index + 1}`;
        }
        
        announce.textContent = `Mostrando imagen ${index + 1} de ${this.totalSlides}: ${slideTitle}`;
    }
    
    /**
     * Ir al slide anterior
     */
    prevSlide() {
        this.showSlide(this.currentSlide - 1);
    }
    
    /**
     * Ir al slide siguiente
     */
    nextSlide() {
        this.showSlide(this.currentSlide + 1);
    }
    
    /**
     * Iniciar autoplay
     */
    startAutoplay() {
        // Solo iniciar si autoplay está activado y hay más de una imagen
        if (this.config.autoplay && this.totalSlides > 1) {
            this.stopAutoplay(); // Asegurarse de que no haya intervalos duplicados
            
            this.autoplayInterval = setInterval(() => {
                if (this.isVisible) {
                    this.nextSlide();
                }
            }, this.config.autoplaySpeed);
        }
    }
    
    /**
     * Detener autoplay
     */
    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
    
    /**
     * Manejar pausas al pasar el mouse
     */
    handlePauseOnHover() {
        // Pausar autoplay al pasar el mouse sobre el slider
        this.slider.addEventListener('mouseenter', () => {
            if (this.config.autoplay) {
                this.stopAutoplay();
            }
        });
        
        // Reanudar autoplay al quitar el mouse
        this.slider.addEventListener('mouseleave', () => {
            if (this.config.autoplay) {
                this.startAutoplay();
            }
        });
        
        // También pausar al enfocar con teclado
        this.slider.addEventListener('focus', () => {
            if (this.config.autoplay) {
                this.stopAutoplay();
            }
        }, true);
        
        this.slider.addEventListener('blur', () => {
            if (this.config.autoplay) {
                this.startAutoplay();
            }
        }, true);
    }
    
    /**
     * Configurar eventos táctiles
     */
    setupTouchEvents() {
        // Eventos táctiles para swipe en móviles
        this.slider.addEventListener('touchstart', (e) => {
            this.touchStartX = e.changedTouches[0].screenX;
            
            // Detener autoplay durante el toque
            if (this.config.autoplay) {
                this.stopAutoplay();
            }
        }, { passive: true });
        
        this.slider.addEventListener('touchend', (e) => {
            this.touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe();
            
            // Reiniciar autoplay después del toque
            if (this.config.autoplay) {
                this.startAutoplay();
            }
        }, { passive: true });
        
        // También añadir soporte para ratón (arrastrar)
        this.slider.addEventListener('mousedown', (e) => {
            this.touchStartX = e.screenX;
            this.isDragging = true;
            this.slider.style.cursor = 'grabbing';
            
            // Detener autoplay durante el arrastre
            if (this.config.autoplay) {
                this.stopAutoplay();
            }
            
            e.preventDefault();
        });
        
        document.addEventListener('mousemove', (e) => {
            if (!this.isDragging) return;
            
            const currentX = e.screenX;
            const diff = this.touchStartX - currentX;
            
            // Opcional: Mostrar desplazamiento visual durante el arrastre
            if (Math.abs(diff) > 20 && this.config.effect === 'slide') {
                const slideWidth = this.slides[0].offsetWidth;
                const percentage = Math.min(Math.abs(diff) / slideWidth, 1) * 30;
                
                // Aplicar transformación temporal durante el arrastre
                this.slides[this.currentSlide].style.transform = `translateX(${diff < 0 ? percentage : -percentage}%)`;
            }
        });
        
        document.addEventListener('mouseup', (e) => {
            if (!this.isDragging) return;
            
            this.touchEndX = e.screenX;
            this.isDragging = false;
            this.slider.style.cursor = '';
            
            // Eliminar transformación temporal
            if (this.config.effect === 'slide') {
                this.slides[this.currentSlide].style.transform = '';
            }
            
            this.handleSwipe();
            
            // Reiniciar autoplay después del arrastre
            if (this.config.autoplay) {
                this.startAutoplay();
            }
        });
        
        // Cancelar arrastre si el ratón sale de la ventana
        document.addEventListener('mouseleave', () => {
            if (this.isDragging) {
                this.isDragging = false;
                this.slider.style.cursor = '';
                
                // Eliminar transformación temporal
                if (this.config.effect === 'slide') {
                    this.slides[this.currentSlide].style.transform = '';
                }
                
                // Reiniciar autoplay
                if (this.config.autoplay) {
                    this.startAutoplay();
                }
            }
        });
    }
    
    /**
     * Manejar gesto de swipe
     */
    handleSwipe() {
        const minSwipeDistance = Math.max(50, this.slideWidth * 0.1); // Al menos 50px o 10% del ancho
        const swipeDistance = this.touchStartX - this.touchEndX;
        
        if (Math.abs(swipeDistance) > minSwipeDistance) {
            if (swipeDistance > 0) {
                // Swipe izquierda -> Siguiente slide
                this.nextSlide();
            } else {
                // Swipe derecha -> Slide anterior
                this.prevSlide();
            }
        }
    }
    
    /**
     * Manejar cambios de visibilidad de la página
     */
    handlePageVisibility() {
        // Usar Page Visibility API para pausar cuando la página no es visible
        document.addEventListener('visibilitychange', () => {
            this.isVisible = document.visibilityState === 'visible';
            
            if (this.config.autoplay) {
                if (this.isVisible) {
                    this.startAutoplay();
                } else {
                    this.stopAutoplay();
                }
            }
        });
    }
    
    /**
     * Aplicar imágenes responsivas
     */
    applyResponsiveImages() {
        // Usar window.matchMedia para mayor precisión
        const isMobile = window.matchMedia(`(max-width: ${this.config.breakpoint}px)`).matches;
        
        // Seleccionar todas las imágenes con atributos data-desktop y data-mobile
        const slideImages = this.slider.querySelectorAll('img[data-desktop][data-mobile]');
        
        slideImages.forEach(img => {
            const mobileSrc = img.getAttribute('data-mobile');
            const desktopSrc = img.getAttribute('data-desktop');
            
            // Aplicar imagen basada en el tamaño de pantalla
            if (isMobile && img.getAttribute('src') !== mobileSrc) {
                img.setAttribute('src', mobileSrc);
            } else if (!isMobile && img.getAttribute('src') !== desktopSrc) {
                img.setAttribute('src', desktopSrc);
            }
            
            // Asegurar que la imagen se carga correctamente
            img.onload = () => {
                // Re-ajustar contenedor si es necesario
                if (this.slidesContainer) {
                    // Solo ajustar altura si es el slide actual
                    if (img.closest('.slide').classList.contains('active')) {
                        this.adjustContainerHeight(img);
                    }
                }
            };
        });
        
        // Guardar ancho del slide para cálculos de swipe
        if (this.slides.length > 0) {
            this.slideWidth = this.slides[0].offsetWidth;
        }
    }
    
    /**
     * Ajustar altura del contenedor según la imagen
     */
    adjustContainerHeight(img) {
        // Opcional: Ajustar altura del contenedor para adaptarse a la imagen
        if (img && img.complete) {
            const slideHeight = img.offsetHeight;
            if (slideHeight > 0) {
                this.slidesContainer.style.minHeight = `${slideHeight}px`;
            }
        }
    }
    
    /**
     * Manejar cambios de tamaño de ventana
     */
    handleWindowResize() {
        // Detector de cambios de tamaño con debounce para mejor rendimiento
        let resizeTimer;
        
        const handleResize = () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                this.applyResponsiveImages();
                
                // Actualizar ancho del slide para cálculos de swipe
                if (this.slides.length > 0) {
                    this.slideWidth = this.slides[0].offsetWidth;
                }
                
                // Ajustar altura del contenedor
                const activeSlide = this.slides[this.currentSlide];
                if (activeSlide) {
                    const img = activeSlide.querySelector('img');
                    if (img) {
                        this.adjustContainerHeight(img);
                    }
                }
                
                // Notificar que se ha redimensionado
                this.dispatchEvent('sliderResized');
            }, 250);
        };
        
        window.addEventListener('resize', handleResize);
        
        // También reiniciar cuando cambia la orientación del dispositivo
        window.addEventListener('orientationchange', () => {
            handleResize();
        });
    }
    
    /**
     * Emitir un evento personalizado
     */
    dispatchEvent(name, detail = {}) {
        const event = new CustomEvent(`sliderResponsivo.${name}`, {
            bubbles: true,
            detail: detail
        });
        this.slider.dispatchEvent(event);
    }
}