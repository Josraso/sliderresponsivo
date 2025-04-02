{*
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
*}

<div class="panel">
    <div class="panel-heading">
        <i class="icon-picture"></i> {l s='Gestión de imágenes del slider' mod='sliderresponsivo'}
        <span class="version-info">v{$module_version|escape:'html':'UTF-8'}</span>
    </div>
    
    <div class="panel-description">
        <p><i class="icon-info-circle"></i> {l s='Este módulo permite crear un slider responsivo con imágenes específicas para escritorio y móvil. Puede añadir, editar, ordenar y eliminar imágenes fácilmente.' mod='sliderresponsivo'}</p>
        <p>{l s='Arrastra y suelta las imágenes para cambiar su orden. Haz clic en las opciones de edición para modificar una imagen existente.' mod='sliderresponsivo'}</p>
    </div>
    
    <div id="slider-image-list">
        <div class="btn-toolbar">
            <button id="btn-add-image" class="btn btn-primary btn-add-image">
                <i class="icon-plus"></i> {l s='Añadir nueva imagen' mod='sliderresponsivo'}
            </button>
        </div>
        
        {if empty($images)}
            <div class="alert alert-info">
                <i class="icon-info-circle"></i> {l s='No hay imágenes disponibles. Añada una nueva imagen para comenzar.' mod='sliderresponsivo'}
            </div>
        {else}
            <div class="table-responsive">
                <table class="table slider-image-list">
                    <thead>
                        <tr>
                            <th style="width: 5%">{l s='ID' mod='sliderresponsivo'}</th>
                            <th style="width: 8%">{l s='Posición' mod='sliderresponsivo'}</th>
                            <th style="width: 40%">{l s='Vista previa' mod='sliderresponsivo'}</th>
                            <th style="width: 27%">{l s='Información' mod='sliderresponsivo'}</th>
                            <th style="width: 8%">{l s='Estado' mod='sliderresponsivo'}</th>
                            <th style="width: 12%">{l s='Acciones' mod='sliderresponsivo'}</th>
                        </tr>
                    </thead>
                    <tbody class="sortable-images">
                        {foreach from=$images item=image}
                            <tr data-id="{$image.id_image}">
                                <td class="text-center">
                                    #{$image.id_image}
                                </td>
                                <td class="text-center">
                                    <div class="dragHandle position-handle">
                                        <span class="btn btn-default">
                                            <i class="icon-arrows drag-handle-icon"></i>
                                        </span>
                                        <span class="drag-instruction">{l s='Arrastra para ordenar' mod='sliderresponsivo'}</span>
                                    </div>
                                    <input type="hidden" name="image_position[{$image.id_image}]" value="{$image.position}" class="position-value" />
                                    <div class="position-display">{$image.position}</div>
                                </td>
                                <td>
                                    <div class="image-preview-container">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="panel">
                                                    <div class="panel-heading">{l s='Escritorio' mod='sliderresponsivo'}</div>
                                                    <a href="{$img_url}{$image.desktop_image}" target="_blank" class="preview-link" title="{$image.title|escape:'html':'UTF-8'}">
                                                        <img src="{$img_url}{$image.desktop_image}" class="img-responsive img-thumbnail" alt="{$image.alt|escape:'html':'UTF-8'}" />
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="panel">
                                                    <div class="panel-heading">{l s='Móvil' mod='sliderresponsivo'}</div>
                                                    <a href="{$img_url}{$image.mobile_image}" target="_blank" class="preview-link" title="{$image.title|escape:'html':'UTF-8'}">
                                                        <img src="{$img_url}{$image.mobile_image}" class="img-responsive img-thumbnail" alt="{$image.alt|escape:'html':'UTF-8'}" />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="panel">
                                        <div class="panel-body">
                                            <p><strong>{l s='Título:' mod='sliderresponsivo'}</strong> {$image.title|escape:'html':'UTF-8'}</p>
                                            
                                            {if !empty($image.description)}
                                                <p><strong>{l s='Descripción:' mod='sliderresponsivo'}</strong> {$image.description|strip_tags|truncate:100}</p>
                                            {/if}
                                            
                                            {if !empty($image.url)}
                                                <p><strong>{l s='URL:' mod='sliderresponsivo'}</strong> 
                                                    <a href="{$image.url}" target="_blank" class="sr-tooltip">
                                                        {$image.url|truncate:30}
                                                        <span class="sr-tooltip-text">{$image.url}</span>
                                                    </a>
                                                </p>
                                            {/if}
                                            
                                            {if !empty($image.alt)}
                                                <p><strong>{l s='Alt:' mod='sliderresponsivo'}</strong> {$image.alt|escape:'html':'UTF-8'|truncate:50}</p>
                                            {/if}
                                            
                                            <p><strong>{l s='Añadida:' mod='sliderresponsivo'}</strong> {$image.date_add|date_format:"%d/%m/%Y %H:%M"}</p>
                                            <p><strong>{l s='Última actualización:' mod='sliderresponsivo'}</strong> {$image.date_upd|date_format:"%d/%m/%Y %H:%M"}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" class="list-action-enable status-toggle{if $image.active} action-enabled{else} action-disabled{/if}" title="{if $image.active}{l s='Habilitado' mod='sliderresponsivo'}{else}{l s='Deshabilitado' mod='sliderresponsivo'}{/if}" data-id="{$image.id_image}" data-current-status="{$image.active}">
                                        <i class="icon-check{if !$image.active}-empty{/if}"></i>
                                    </a>
                                </td>
                                <td class="text-right">
                                    <div class="btn-group-action">
                                        <a href="javascript:void(0)" class="btn btn-default btn-edit-image" data-id="{$image.id_image}" title="{l s='Editar' mod='sliderresponsivo'}">
                                            <i class="icon-pencil"></i> {l s='Editar' mod='sliderresponsivo'}
                                        <a href="{$current_url}&deleteImage=1&id_image={$image.id_image}&token={$token}" class="btn btn-danger btn-delete-image" title="{l s='Eliminar' mod='sliderresponsivo'}">
    <i class="icon-trash"></i> {l s='Eliminar' mod='sliderresponsivo'}
</a>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
        {/if}
    </div>
    
    <div id="slider-image-form" style="display: none;">
        <form id="image-form" class="slider-image-form" action="{$current_url}" method="post" enctype="multipart/form-data">
            <div class="panel">
                <div class="panel-heading">
                    <i class="icon-picture"></i> <span id="form-title">{l s='Añadir nueva imagen' mod='sliderresponsivo'}</span>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="submitImage" value="1" />
                    <input type="hidden" name="id_image" id="id_image" value="0" />
                    
                    <div class="form-group">
                        <label for="image-url">{l s='URL de destino' mod='sliderresponsivo'}</label>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="icon-link"></i></span>
                            <input type="url" name="url" id="image-url" class="form-control" placeholder="https://" />
                        </div>
                        <p class="help-block">{l s='URL donde se redirigirá al hacer clic en la imagen (opcional)' mod='sliderresponsivo'}</p>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="active" id="image-active" value="1" checked="checked" />
                                {l s='Activo' mod='sliderresponsivo'}
                            </label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">{l s='Imagen para escritorio' mod='sliderresponsivo'} <span class="text-danger">*</span></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="dropzone">
                                            <input type="file" name="desktop_image" id="desktop_image_input" class="image-upload" data-preview="desktop-preview" accept="image/*" />
                                            <i class="icon icon-cloud-upload"></i>
                                            <p class="dropzone-message">{l s='Arrastra tu imagen aquí o haz clic para seleccionar' mod='sliderresponsivo'}</p>
                                            <p class="dropzone-info">{l s='Formato recomendado: JPG, PNG - Tamaño recomendado:' mod='sliderresponsivo'} {Configuration::get('SLIDERRESPONSIVO_WIDTH_DESKTOP')}x{Configuration::get('SLIDERRESPONSIVO_HEIGHT_DESKTOP')}px</p>
                                        </div>
                                        <div class="preview-box text-center" style="display: none;">
                                            <div class="preview-title">{l s='Vista previa:' mod='sliderresponsivo'}</div>
                                            <img id="desktop-preview" class="img-responsive img-thumbnail preview-image" src="" alt="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="panel">
                                <div class="panel-heading">{l s='Imagen para móvil' mod='sliderresponsivo'} <span class="text-danger">*</span></div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <div class="dropzone">
                                            <input type="file" name="mobile_image" id="mobile_image_input" class="image-upload" data-preview="mobile-preview" accept="image/*" />
                                            <i class="icon icon-cloud-upload"></i>
                                            <p class="dropzone-message">{l s='Arrastra tu imagen aquí o haz clic para seleccionar' mod='sliderresponsivo'}</p>
                                            <p class="dropzone-info">{l s='Formato recomendado: JPG, PNG - Tamaño recomendado:' mod='sliderresponsivo'} {Configuration::get('SLIDERRESPONSIVO_WIDTH_MOBILE')}x{Configuration::get('SLIDERRESPONSIVO_HEIGHT_MOBILE')}px</p>
                                        </div>
                                        <div class="preview-box text-center" style="display: none;">
                                            <div class="preview-title">{l s='Vista previa:' mod='sliderresponsivo'}</div>
                                            <img id="mobile-preview" class="img-responsive img-thumbnail preview-image" src="" alt="" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Campos multilenguaje -->
                    <div class="panel">
                        <div class="panel-heading">{l s='Información SEO de la imagen' mod='sliderresponsivo'}</div>
                        <div class="panel-body">
                            <!-- Tabs para idiomas -->
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <ul class="nav nav-tabs" role="tablist">
                                            {foreach from=$languages item=language name=languages}
                                                <li role="presentation" class="{if $language.id_lang == $default_lang}active{/if}">
                                                    <a href="#lang-{$language.id_lang}" aria-controls="lang-{$language.id_lang}" role="tab" data-toggle="tab">
                                                        <img src="../img/l/{$language.id_lang}.jpg" alt="{$language.name}" /> {$language.name}
                                                    </a>
                                                </li>
                                            {/foreach}
                                        </ul>
                                        
                                        <!-- Contenido para cada idioma -->
                                        <div class="tab-content">
                                            {foreach from=$languages item=language}
                                                <div role="tabpanel" class="tab-pane {if $language.id_lang == $default_lang}active{/if}" id="lang-{$language.id_lang}">
                                                    <div class="form-group">
                                                        <label for="title_{$language.id_lang}">{l s='Título' mod='sliderresponsivo'} <span class="text-danger">*</span></label>
                                                        <input type="text" name="title_{$language.id_lang}" id="title_{$language.id_lang}" class="form-control" required />
                                                        <p class="help-block">{l s='Importante para SEO, no se muestra visiblemente sobre la imagen' mod='sliderresponsivo'}</p>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label for="description_{$language.id_lang}">{l s='Descripción' mod='sliderresponsivo'}</label>
                                                        <textarea name="description_{$language.id_lang}" id="description_{$language.id_lang}" class="form-control" rows="4"></textarea>
                                                        <p class="help-block">{l s='Descripción para SEO, no visible en el front-office' mod='sliderresponsivo'}</p>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label for="alt_{$language.id_lang}">{l s='Texto alternativo (SEO)' mod='sliderresponsivo'}</label>
                                                        <input type="text" name="alt_{$language.id_lang}" id="alt_{$language.id_lang}" class="form-control" />
                                                        <p class="help-block">{l s='Texto alternativo para la imagen, importante para SEO y accesibilidad' mod='sliderresponsivo'}</p>
                                                    </div>
                                                </div>
                                            {/foreach}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vista previa en tiempo real -->
                    <div class="panel">
                        <div class="panel-heading">{l s='Vista previa en tiempo real' mod='sliderresponsivo'}</div>
                        <div class="panel-body">
                            <div class="live-preview">
                                <div class="live-preview-title"></div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="text-center">{l s='Escritorio' mod='sliderresponsivo'}</h4>
                                        <div class="live-preview-desktop" style="display: none;">
                                            <div class="live-preview-container">
                                                <img src="" class="img-responsive" alt="" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="text-center">{l s='Móvil' mod='sliderresponsivo'}</h4>
                                        <div class="live-preview-mobile" style="display: none;">
                                            <div class="live-preview-container">
                                                <img src="" class="img-responsive" alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="panel-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="icon-save"></i> {l s='Guardar' mod='sliderresponsivo'}
                    </button>
                    <button type="button" id="btn-cancel-image" class="btn btn-default">
                        <i class="icon-cancel"></i> {l s='Cancelar' mod='sliderresponsivo'}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var currentUrl = '{$current_url|escape:'javascript':'UTF-8'}';
    var token = '{$token|escape:'javascript':'UTF-8'}';
    var defaultLangId = {$default_lang|intval};
    var confirmDeleteLang = '{l s='¿Está seguro que desea eliminar esta imagen? Esta acción no se puede deshacer.' mod='sliderresponsivo' js=1}';
    var confirmShown = false;
    
    $(document).ready(function() {
        // Inicializar ordenamiento
        initializeSortable();
        
        // Inicializar tooltips
        initializeTooltips();
        
        // Inicializar dropzone
        initializeDropzone();
        
        // Previsualización de imágenes al seleccionarlas
        initializeImagePreview();
        
        // Funciones para gestionar el formulario
        initializeFormHandling();
        
        // Nuevo manejador para prevenir doble confirmación
        $('.btn-delete-image').on('click', function(e) {
            if (!confirmShown) {
                if (!confirm(confirmDeleteLang)) {
                    e.preventDefault();
                } else {
                    confirmShown = true;
                }
            }
        });
        
        // AJAX para cambiar el estado
        $('.status-toggle').on('click', function(e) {
            e.preventDefault();
            var btn = $(this);
            var imageId = btn.data('id');
            
            // Mostrar indicador de carga
            showLoadingMessage('Actualizando estado...');
            
            // Realizar la llamada AJAX para cambiar el estado
            $.ajax({
                url: currentUrl + '&changeImageStatus=1&id_image=' + imageId + '&ajax=1&token=' + token,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    hideLoadingMessage();
                    
                    if (response.success) {
                        // Cambiar el estado visual y los atributos
                        var isNowActive = response.status == 1;
                        
                        if (isNowActive) {
                            btn.removeClass('action-disabled').addClass('action-enabled');
                            btn.find('i').removeClass('icon-check-empty').addClass('icon-check');
                            btn.attr('title', '{l s='Habilitado' mod='sliderresponsivo' js=1}');
                        } else {
                            btn.removeClass('action-enabled').addClass('action-disabled');
                            btn.find('i').removeClass('icon-check').addClass('icon-check-empty');
                            btn.attr('title', '{l s='Deshabilitado' mod='sliderresponsivo' js=1}');
                        }
                        
                        // Actualizar el estado para futuros clics
                        btn.data('current-status', isNowActive ? 1 : 0);
                        
                        showSuccessMessage('{l s='Estado actualizado correctamente' mod='sliderresponsivo' js=1}');
                    } else {
                        showErrorMessage(response.message || '{l s='Error al actualizar el estado' mod='sliderresponsivo' js=1}');
                    }
                },
                error: function() {
                    hideLoadingMessage();
                    showErrorMessage('{l s='Error de conexión al actualizar el estado' mod='sliderresponsivo' js=1}');
                }
            });
        });
    });
    
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
                    // Actualizar posiciones visualmente
                    var position = 1;
                    var positions = {};
                    
                    $('.sortable-images tr').each(function() {
                        var id = $(this).data('id');
                        $(this).find('.position-value').val(position);
                        $(this).find('.position-display').text(position);
                        positions[id] = position;
                        position++;
                    });
                    
                    // Enviar posiciones al servidor
                    $.ajax({
                        url: currentUrl + '&updatePositions=1&token=' + token,
                        method: 'POST',
                        data: {
                            image_position: positions
                        },
                        success: function(response) {
                            showSuccessMessage('{l s='Posiciones actualizadas' mod='sliderresponsivo' js=1}');
                        },
                        error: function() {
                            showErrorMessage('{l s='Error al actualizar posiciones' mod='sliderresponsivo' js=1}');
                        }
                    });
                }
            }).disableSelection();
        }
    }
    
    function initializeTooltips() {
        $('.sr-tooltip').hover(
            function() {
                $(this).find('.sr-tooltip-text').css('visibility', 'visible').css('opacity', '1');
            },
            function() {
                $(this).find('.sr-tooltip-text').css('visibility', 'hidden').css('opacity', '0');
            }
        );
    }
    
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
                    
                    // Actualizar vista previa en tiempo real
                    updateLivePreview();
                };
                
                reader.readAsDataURL(input.files[0]);
            }
        });
    }
    
    function updateLivePreview() {
        // Si existe la vista previa en tiempo real
        const title = $('#title_' + defaultLangId).val() || '{l s='Vista previa' mod='sliderresponsivo' js=1}';
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
    
    function initializeFormHandling() {
        // Mostrar formulario de edición
        $('.btn-edit-image').on('click', function(e) {
            e.preventDefault();
            
            const imageId = $(this).data('id');
            $('#form-title').text('{l s='Editar imagen' mod='sliderresponsivo' js=1}');
            
            // Ocultar lista y mostrar formulario
            $('#slider-image-list').fadeOut(300, function() {
                $('#slider-image-form').fadeIn(300);
            });
            
            // Resetear formulario primero
            resetForm();
            
            // Mostrar mensaje de carga
            showLoadingMessage('{l s='Cargando datos de la imagen...' mod='sliderresponsivo' js=1}');
            
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
                        showErrorMessage('{l s='Error al cargar la imagen' mod='sliderresponsivo' js=1}');
                        $('#slider-image-list').fadeIn(300);
                        $('#slider-image-form').hide();
                    }
                },
                error: function() {
                    // Ocultar mensaje de carga
                    hideLoadingMessage();
                    
                    showErrorMessage('{l s='Error al cargar la imagen' mod='sliderresponsivo' js=1}');
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
            $('#form-title').text('{l s='Añadir nueva imagen' mod='sliderresponsivo' js=1}');
            
            // Mostrar formulario y ocultar lista
            $('#slider-image-list').fadeOut(300, function() {
                $('#slider-image-form').fadeIn(300);
            });
        });
        
        // Escuchar cambios en los campos de texto para actualizar vista previa
        $('.slider-image-form input[type="text"], .slider-image-form textarea').on('input', function() {
            updateLivePreview();
        });
        
        // Activar tabs de Bootstrap
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // Actualizar vista previa cuando se cambia de pestaña
            updateLivePreview();
        });
    }
    
    function resetForm() {
        $('#image-form')[0].reset();
        $('#id_image').val(0);
        $('.preview-image').attr('src', '');
        $('.preview-box').hide();
        $('.live-preview-desktop, .live-preview-mobile').hide();
    }
    
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
    
    function showLoadingMessage(message) {
        if ($('#loading-message').length === 0) {
            $('body').append('<div id="loading-message" class="alert alert-info"><i class="icon-refresh icon-spin"></i> ' + message + '</div>');
        } else {
            $('#loading-message').html('<i class="icon-refresh icon-spin"></i> ' + message).show();
        }
    }
    
    function hideLoadingMessage() {
        $('#loading-message').fadeOut(300);
    }
    
    function showSuccessMessage(message) {
        $.growl.notice({ title: "", message: message });
    }
    
    function showErrorMessage(message) {
        $.growl.error({ title: "", message: message });
    }
</script>