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

$(document).ready(function() {
    // Envolver el panel de configuración para convertirlo en acordeón
    setupConfigurationAccordion();
    
    // Inicializar ordenamiento para imágenes
    initializeSortable();
    
    // Previsualización de imágenes al seleccionarlas
    initializeImagePreview();
    
    // Inicializar Dropzone para subir imágenes
    initializeDropzone();
    
    // Inicializar tabs multilingüe
    initializeLanguageTabs();
    
    // Inicializar manipulación de formularios
    initializeFormHandling();
    
    // Inicializar tooltips
    initializeTooltips();
});

/**
 * Configurar el panel de configuración como acordeón
 */
function setupConfigurationAccordion() {
    // Solo proceder si existe el formulario de configuración
    if ($('form[name="sliderresponsivo_form"]').length > 0) {
        // Seleccionar el panel que contiene el formulario de configuración
        const $configPanel = $('form[name="sliderresponsivo_form"]').closest('.panel');
        
        // Añadir ID para fácil referencia
        $configPanel.attr('id', 'module-configuration-panel');
        
       // Añadir botón de acordeón en el encabezado
        $configPanel.find('.panel-heading').append('<span class="panel-heading-action"><a class="list-toolbar-btn toggle-config" href="#"><i class="icon-caret-down"></i></a></span>');
        
        // Envolver el contenido del panel en un elemento colapsable
        const $panelBody = $configPanel.find('.panel-body');
        const $panelFooter = $configPanel.find('.panel-footer');
        
        $panelBody.add($panelFooter).wrapAll('<div class="config-collapse" style="display:none;"></div>');
        
        // Añadir comportamiento de colapso/expansión
        $('.toggle-config').on('click', function(e) {
            e.preventDefault();
            const $icon = $(this).find('i');
            const $collapse = $('.config-collapse');
            
            $collapse.slideToggle(300, function() {
                if ($(this).is(':visible')) {
                    $icon.removeClass('icon-caret-down').addClass('icon-caret-up');
                } else {
                    $icon.removeClass('icon-caret-up').addClass('icon-caret-down');
                }
            });
        });
        
        // Añadir texto explicativo
        $configPanel.find('.panel-heading').append('<small style="margin-left:10px;color:#999;"> (Haga clic en la flecha para mostrar/ocultar la configuración)</small>');
    }
}

/**
 * Inicializa la funcionalidad de ordenamiento
 */
function initializeSortable() {
    if (typeof $.fn.sortable !== 'undefined') {
        $('.sortable-images').sortable({
            axis: 'y',
            handle: '.position-handle',
            helper: function(e, tr) {
                var $originals = tr.children();
                var $helper = tr.clone();
                $helper.children().each(function(index) {
                    $(this).width($originals.eq(index).width());
                });
                return $helper;
            },
            start: function(event, ui) {
                $(this).addClass('sorting');
                ui.item.data('oldPosition', ui.item.index() + 1);
            },
            stop: function(event, ui) {
                $(this).removeClass('sorting');
                showSuccessMessage('Posición actualizada');
            },
            update: function(event, ui) {
                // Actualizar posiciones
                updatePositions();
            }
        }).disableSelection();
    }
}

/**
 * Actualiza las posiciones de los elementos y los envía al servidor
 */
function updatePositions() {
    var positions = {};
    $('.sortable-images tr').each(function(index) {
        var id = $(this).data('id');
        var newPosition = index + 1;
        positions[id] = newPosition;
        
        // Actualizar visualmente la posición
        $(this).find('.position-value').val(newPosition);
        $(this).find('.position-display').text(newPosition);
    });
    
    // Enviar posiciones al servidor
    $.ajax({
        url: currentUrl + '&updatePositions=1&token=' + token,
        method: 'POST',
        data: {
            image_position: positions
        },
        success: function(response) {
            showSuccessMessage('Posiciones actualizadas');
        },
        error: function() {
            showErrorMessage('Error al actualizar posiciones');
        }
    });
}

/**
 * Inicializa la previsualización de imágenes
 */
function initializeImagePreview() {
    $('.image-upload').on('change', function() {
        const input = this;
        const previewId = $(this).data('preview');
        const preview = $('#' + previewId);
        const previewBox = preview.closest('.preview-box');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.attr('src', e.target.result);
                previewBox.fadeIn(300);
                
                // Actualizar la vista previa en tiempo real
                updateLivePreview();
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    });
}

/**
 * Actualiza la vista previa en tiempo real
 */
function updateLivePreview() {
    // Si existe la vista previa en tiempo real
    if ($('.live-preview-container').length) {
        const title = $('#title_' + defaultLangId).val() || 'Vista previa';
        const desktopSrc = $('#desktop-preview').attr('src');
        const mobileSrc = $('#mobile-preview').attr('src');
        
        $('.live-preview-title').text(title);
        
        if (desktopSrc) {
            $('.live-preview-desktop img').attr('src', desktopSrc);
            $('.live-preview-desktop').show();
        }
        
        if (mobileSrc) {
            $('.live-preview-mobile img').attr('src', mobileSrc);
            $('.live-preview-mobile').show();
        }
    }
}

/**
 * Inicializa la funcionalidad de Dropzone
 */
function initializeDropzone() {
    $('.dropzone').each(function() {
        const dropzone = $(this);
        const input = dropzone.find('input[type="file"]');
        
        // Eventos para arrastrar y soltar
        dropzone.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.addClass('dropzone-active');
        });
        
        dropzone.on('dragleave dragend drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.removeClass('dropzone-active');
        });
        
        dropzone.on('drop', function(e) {
            const files = e.originalEvent.dataTransfer.files;
            if (files.length) {
                // Asignar archivos al input
                input[0].files = files;
                
                // Disparar evento change para actualizar la vista previa
                input.trigger('change');
            }
        });
        
        // Click en la zona para abrir selector de archivos
        dropzone.on('click', function() {
            input.click();
        });
    });
}

/**
 * Inicializa las pestañas de idiomas
 */
function initializeLanguageTabs() {
    // Usar Bootstrap tabs nativo
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        // Actualizar vista previa cuando se cambia de pestaña
        updateLivePreview();
    });
}

/**
 * Inicializa la manipulación de formularios
 */
function initializeFormHandling() {
    // Mostrar formulario de edición
    $('.btn-edit-image').on('click', function(e) {
        e.preventDefault();
        
        const imageId = $(this).data('id');
        $('#form-title').text('Editar imagen');
        
        // Ocultar lista y mostrar formulario
        $('#slider-image-list').fadeOut(300, function() {
            $('#slider-image-form').fadeIn(300);
        });
        
        // Resetear formulario primero
        resetForm();
        
        // Mostrar mensaje de carga
        showLoadingMessage('Cargando datos de la imagen...');
        
        // Cargar datos de la imagen
        $.ajax({
            url: currentUrl + '&action=getImage&id_image=' + imageId + '&token=' + token,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                // Ocultar mensaje de carga
                hideLoadingMessage();
                
                if (response.success) {
                    fillImageForm(response.image);
                    updateLivePreview();
                } else {
                    showErrorMessage('Error al cargar la imagen');
                    $('#slider-image-list').fadeIn(300);
                    $('#slider-image-form').hide();
                }
            },
            error: function() {
                // Ocultar mensaje de carga
                hideLoadingMessage();
                
                showErrorMessage('Error al cargar la imagen');
                $('#slider-image-list').fadeIn(300);
                $('#slider-image-form').hide();
            }
        });
    });
    
    // Botón para cancelar edición
    $('#btn-cancel-image').on('click', function(e) {
        e.preventDefault();
        
        // Mostrar lista y ocultar formulario
        $('#slider-image-form').fadeOut(300, function() {
            $('#slider-image-list').fadeIn(300);
        });
        
        // Limpiar formulario
        resetForm();
    });
    
    // Botón para añadir nueva imagen
    $('#btn-add-image').on('click', function(e) {
        e.preventDefault();
        
        resetForm();
        $('#form-title').text('Añadir nueva imagen');
        
        // Mostrar formulario y ocultar lista
        $('#slider-image-list').fadeOut(300, function() {
            $('#slider-image-form').fadeIn(300);
        });
    });
    
    // Confirmar eliminación de imagen
    $('.btn-delete-image').on('click', function(e) {
        if (!confirm('¿Está seguro que desea eliminar esta imagen? Esta acción no se puede deshacer.')) {
            e.preventDefault();
        }
    });
    
    // Cambiar estado activo/inactivo rápidamente
    $('.list-action-enable').on('click', function(e) {
        e.preventDefault();
        
        const link = $(this);
        const imageId = link.data('id');
        
        // Mostrar mensaje de carga
        showLoadingMessage('Actualizando estado...');
        
        $.ajax({
            url: currentUrl + '&changeImageStatus=1&id_image=' + imageId + '&token=' + token,
            method: 'POST',
            success: function(response) {
                // Ocultar mensaje de carga
                hideLoadingMessage();
                
                // Alternar clase y actualizar apariencia
                link.toggleClass('action-enabled action-disabled');
                
                if (link.hasClass('action-enabled')) {
                    link.find('i').attr('class', 'icon-check');
                    link.attr('title', 'Habilitado');
                } else {
                    link.find('i').attr('class', 'icon-check-empty');
                    link.attr('title', 'Deshabilitado');
                }
                
                showSuccessMessage('Estado actualizado');
            },
            error: function() {
                // Ocultar mensaje de carga
                hideLoadingMessage();
                
                showErrorMessage('Error al cambiar el estado');
            }
        });
    });
    
    // Escuchar cambios en los campos de texto para actualizar vista previa
    $('.slider-image-form input[type="text"], .slider-image-form textarea').on('input', function() {
        updateLivePreview();
    });
}

/**
 * Inicializa tooltips personalizados
 */
function initializeTooltips() {
    $('.sr-tooltip').each(function() {
        $(this).hover(
            function() {
                $(this).find('.sr-tooltip-text').css('visibility', 'visible').css('opacity', '1');
            },
            function() {
                $(this).find('.sr-tooltip-text').css('visibility', 'hidden').css('opacity', '0');
            }
        );
    });
}

/**
 * Función para resetear el formulario
 */
function resetForm() {
    $('#image-form')[0].reset();
    $('#id_image').val(0);
    $('.preview-image').attr('src', '');
    $('.preview-box').hide();
    $('.live-preview-desktop, .live-preview-mobile').hide();
}

/**
 * Función para llenar el formulario con datos de la imagen
 */
function fillImageForm(image) {
    $('#id_image').val(image.id_image);
    $('#image-url').val(image.url);
    $('#image-active').prop('checked', image.active == 1);
    
    // Campos multilingüe
    for (const langId in image.languages) {
        if (image.languages.hasOwnProperty(langId)) {
            const data = image.languages[langId];
            $('#title_' + langId).val(data.title);
            $('#description_' + langId).val(data.description);
            $('#alt_' + langId).val(data.alt);
        }
    }
    
    // Mostrar miniaturas de imágenes existentes
    if (image.desktop_image) {
        $('#desktop-preview').attr('src', image.desktop_url).closest('.preview-box').show();
    }
    
    if (image.mobile_image) {
        $('#mobile-preview').attr('src', image.mobile_url).closest('.preview-box').show();
    }
}

/**
 * Muestra un mensaje de carga
 */
function showLoadingMessage(message) {
    if ($('#loading-message').length === 0) {
        $('body').append('<div id="loading-message" class="alert alert-info"><i class="icon-refresh icon-spin"></i> ' + message + '</div>');
    } else {
        $('#loading-message').html('<i class="icon-refresh icon-spin"></i> ' + message).show();
    }
}

/**
 * Oculta el mensaje de carga
 */
function hideLoadingMessage() {
    $('#loading-message').fadeOut(300);
}

/**
 * Muestra un mensaje de éxito
 */
function showSuccessMessage(message) {
    $.growl.notice({ title: "", message: message });
}

/**
 * Muestra un mensaje de error
 */
function showErrorMessage(message) {
    $.growl.error({ title: "", message: message });
}