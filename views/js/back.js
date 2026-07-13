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

var sliderResponsivoAdmin = window.sliderResponsivoAdmin || {
    currentUrl: '',
    token: '',
    defaultLangId: 1,
    confirmDelete: '¿Está seguro que desea eliminar esta imagen? Esta acción no se puede deshacer.',
    i18n: {}
};

$(document).ready(function() {
    setupConfigurationAccordion();
    initializeSortable();
    initializeImagePreview();
    initializeDropzone();
    initializeLanguageTabs();
    initializeFormHandling();
    initializeTooltips();
    initializeRemoveLangImage();
});

/**
 * Configurar el panel de configuración como acordeón
 */
function setupConfigurationAccordion() {
    if ($('form[name="sliderresponsivo_form"]').length > 0) {
        const $configPanel = $('form[name="sliderresponsivo_form"]').closest('.panel');
        $configPanel.attr('id', 'module-configuration-panel');
        $configPanel.find('.panel-heading').append('<span class="panel-heading-action"><a class="list-toolbar-btn toggle-config" href="#"><i class="icon-caret-down"></i></a></span>');

        const $panelBody = $configPanel.find('.panel-body');
        const $panelFooter = $configPanel.find('.panel-footer');
        $panelBody.add($panelFooter).wrapAll('<div class="config-collapse" style="display:none;"></div>');

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
            },
            update: function(event, ui) {
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

        $(this).find('.position-value').val(newPosition);
        $(this).find('.position-display').text(newPosition);
    });

    $.ajax({
        url: sliderResponsivoAdmin.currentUrl + '&updatePositions=1&token=' + sliderResponsivoAdmin.token,
        method: 'POST',
        data: {
            image_position: positions
        },
        success: function() {
            showSuccessMessage(sliderResponsivoAdmin.i18n.positionsUpdated || 'Posiciones actualizadas');
        },
        error: function() {
            showErrorMessage(sliderResponsivoAdmin.i18n.positionsError || 'Error al actualizar posiciones');
        }
    });
}

/**
 * Inicializa la previsualización de imágenes (imágenes predeterminadas y las específicas por idioma)
 */
function initializeImagePreview() {
    $(document).on('change', '.image-upload', function() {
        const input = this;
        const previewId = $(this).data('preview');
        const preview = $('#' + previewId);
        const previewBox = preview.closest('.preview-box');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.attr('src', e.target.result);
                previewBox.fadeIn(300);
                previewBox.find('.remove-lang-image-checkbox').prop('checked', false);

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
    if ($('.live-preview-container').length) {
        const title = $('#title_' + sliderResponsivoAdmin.defaultLangId).val() || 'Vista previa';
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
 * Inicializa la funcionalidad de Dropzone (incluye las de idioma, añadidas dinámicamente)
 */
function initializeDropzone() {
    $(document).on('click', '.dropzone', function() {
        $(this).find('input[type="file"]').trigger('click');
    });

    $(document).on('dragover dragenter', '.dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dropzone-active');
    });

    $(document).on('dragleave dragend drop', '.dropzone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dropzone-active');
    });

    $(document).on('drop', '.dropzone', function(e) {
        const files = e.originalEvent.dataTransfer.files;
        if (files.length) {
            const input = $(this).find('input[type="file"]');
            input[0].files = files;
            input.trigger('change');
        }
    });
}

/**
 * Inicializa las pestañas de idiomas
 */
function initializeLanguageTabs() {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
        updateLivePreview();
    });
}

/**
 * Permite quitar una imagen específica de idioma y volver a la predeterminada
 */
function initializeRemoveLangImage() {
    $(document).on('change', '.remove-lang-image-checkbox', function() {
        const $box = $(this).closest('.lang-preview-box');
        if ($(this).is(':checked')) {
            $box.find('.preview-image').css('opacity', 0.35);
        } else {
            $box.find('.preview-image').css('opacity', 1);
        }
    });
}

/**
 * Inicializa la manipulación de formularios
 */
function initializeFormHandling() {
    $('.btn-edit-image').on('click', function(e) {
        e.preventDefault();

        const imageId = $(this).data('id');
        $('#form-title').text(sliderResponsivoAdmin.i18n.editTitle || 'Editar imagen');

        $('#slider-image-list').fadeOut(300, function() {
            $('#slider-image-form').fadeIn(300);
        });

        resetForm();
        showLoadingMessage(sliderResponsivoAdmin.i18n.loading || 'Cargando datos de la imagen...');

        $.ajax({
            url: sliderResponsivoAdmin.currentUrl + '&action=getImage&id_image=' + imageId + '&token=' + sliderResponsivoAdmin.token,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                hideLoadingMessage();

                if (response.success) {
                    fillImageForm(response.image);
                    updateLivePreview();
                } else {
                    showErrorMessage(sliderResponsivoAdmin.i18n.loadError || 'Error al cargar la imagen');
                    $('#slider-image-list').fadeIn(300);
                    $('#slider-image-form').hide();
                }
            },
            error: function() {
                hideLoadingMessage();
                showErrorMessage(sliderResponsivoAdmin.i18n.loadError || 'Error al cargar la imagen');
                $('#slider-image-list').fadeIn(300);
                $('#slider-image-form').hide();
            }
        });
    });

    $('#btn-cancel-image').on('click', function(e) {
        e.preventDefault();

        $('#slider-image-form').fadeOut(300, function() {
            $('#slider-image-list').fadeIn(300);
        });

        resetForm();
    });

    $('#btn-add-image').on('click', function(e) {
        e.preventDefault();

        resetForm();
        $('#form-title').text(sliderResponsivoAdmin.i18n.addTitle || 'Añadir nueva imagen');

        $('#slider-image-list').fadeOut(300, function() {
            $('#slider-image-form').fadeIn(300);
        });
    });

    // Evitar doble confirmación cuando se hace clic varias veces seguidas
    var deleteConfirmShown = false;
    $('.btn-delete-image').on('click', function(e) {
        if (deleteConfirmShown) {
            return;
        }
        if (!confirm(sliderResponsivoAdmin.confirmDelete)) {
            e.preventDefault();
        } else {
            deleteConfirmShown = true;
        }
    });

    $('.status-toggle').on('click', function(e) {
        e.preventDefault();

        const link = $(this);
        const imageId = link.data('id');

        showLoadingMessage(sliderResponsivoAdmin.i18n.updatingStatus || 'Actualizando estado...');

        $.ajax({
            url: sliderResponsivoAdmin.currentUrl + '&changeImageStatus=1&id_image=' + imageId + '&ajax=1&token=' + sliderResponsivoAdmin.token,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                hideLoadingMessage();

                if (response.success) {
                    const isNowActive = response.status == 1;

                    if (isNowActive) {
                        link.removeClass('action-disabled').addClass('action-enabled');
                        link.find('i').removeClass('icon-check-empty').addClass('icon-check');
                        link.attr('title', sliderResponsivoAdmin.i18n.enabled || 'Habilitado');
                    } else {
                        link.removeClass('action-enabled').addClass('action-disabled');
                        link.find('i').removeClass('icon-check').addClass('icon-check-empty');
                        link.attr('title', sliderResponsivoAdmin.i18n.disabled || 'Deshabilitado');
                    }

                    link.data('current-status', isNowActive ? 1 : 0);

                    showSuccessMessage(response.message || sliderResponsivoAdmin.i18n.statusUpdated || 'Estado actualizado correctamente');
                } else {
                    showErrorMessage(response.message || sliderResponsivoAdmin.i18n.statusError || 'Error al actualizar el estado');
                }
            },
            error: function() {
                hideLoadingMessage();
                showErrorMessage(sliderResponsivoAdmin.i18n.connectionError || 'Error de conexión al actualizar el estado');
            }
        });
    });

    $(document).on('input', '.slider-image-form input[type="text"], .slider-image-form textarea', function() {
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
    $('.preview-image').attr('src', '').css('opacity', 1);
    $('.preview-box').hide();
    $('.live-preview-desktop, .live-preview-mobile').hide();
    $('.remove-lang-image-checkbox').prop('checked', false);
}

/**
 * Función para llenar el formulario con datos de la imagen
 */
function fillImageForm(image) {
    $('#id_image').val(image.id_image);
    $('#image-url').val(image.url);
    $('#image-active').prop('checked', image.active == 1);

    if (image.desktop_image) {
        $('#desktop-preview').attr('src', image.desktop_url).closest('.preview-box').show();
    }

    if (image.mobile_image) {
        $('#mobile-preview').attr('src', image.mobile_url).closest('.preview-box').show();
    }

    for (const langId in image.languages) {
        if (!image.languages.hasOwnProperty(langId)) {
            continue;
        }

        const data = image.languages[langId];
        $('#title_' + langId).val(data.title);
        $('#description_' + langId).val(data.description);
        $('#alt_' + langId).val(data.alt);

        if (data.desktop_url) {
            $('#desktop-preview-' + langId).attr('src', data.desktop_url).closest('.preview-box').show();
        }

        if (data.mobile_url) {
            $('#mobile-preview-' + langId).attr('src', data.mobile_url).closest('.preview-box').show();
        }
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
    $.growl.notice({ title: '', message: message });
}

/**
 * Muestra un mensaje de error
 */
function showErrorMessage(message) {
    $.growl.error({ title: '', message: message });
}
