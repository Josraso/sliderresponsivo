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
                    <input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
                    
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
                    
                    <div class="alert alert-info">
                        {l s='Cada idioma tiene su propia imagen de escritorio, imagen de móvil y textos SEO. Solo es obligatorio completar un idioma; si dejas otro sin imagen, en la tienda se mostrará automáticamente el contenido del idioma que sí la tenga.' mod='sliderresponsivo'}
                    </div>

                    <!-- Pestañas de idioma: cada una con su imagen y sus textos -->
                    <ul class="nav nav-tabs" role="tablist">
                        {foreach from=$languages item=language name=languages}
                            <li role="presentation" class="{if $language.id_lang == $default_lang}active{/if}">
                                <a href="#lang-{$language.id_lang}" aria-controls="lang-{$language.id_lang}" role="tab" data-toggle="tab">
                                    <img src="../img/l/{$language.id_lang}.jpg" alt="{$language.name}" /> {$language.name}
                                    <span class="lang-tab-status" data-lang="{$language.id_lang}"></span>
                                </a>
                            </li>
                        {/foreach}
                    </ul>

                    <div class="tab-content">
                        {foreach from=$languages item=language}
                            <div role="tabpanel" class="tab-pane {if $language.id_lang == $default_lang}active{/if}" id="lang-{$language.id_lang}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>{l s='Imagen de escritorio' mod='sliderresponsivo'}</label>
                                        <div class="dropzone lang-dropzone">
                                            <input type="file" name="desktop_image_{$language.id_lang}" id="desktop_image_{$language.id_lang}" class="image-upload lang-image-upload" data-preview="desktop-preview-{$language.id_lang}" accept="image/*" />
                                            <i class="icon icon-cloud-upload"></i>
                                            <p class="dropzone-message">{l s='Arrastra tu imagen aquí o haz clic para seleccionar' mod='sliderresponsivo'}</p>
                                            <p class="dropzone-info">{l s='Tamaño recomendado:' mod='sliderresponsivo'} {Configuration::get('SLIDERRESPONSIVO_WIDTH_DESKTOP')}x{Configuration::get('SLIDERRESPONSIVO_HEIGHT_DESKTOP')}px</p>
                                        </div>
                                        <div class="preview-box text-center lang-preview-box" data-lang="{$language.id_lang}" data-type="desktop" style="display: none;">
                                            <img id="desktop-preview-{$language.id_lang}" class="img-responsive img-thumbnail preview-image" src="" alt="" />
                                            <div>
                                                <label class="remove-lang-image">
                                                    <input type="checkbox" name="remove_desktop_image_{$language.id_lang}" value="1" class="remove-lang-image-checkbox" />
                                                    {l s='Quitar imagen de este idioma' mod='sliderresponsivo'}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>{l s='Imagen de móvil' mod='sliderresponsivo'}</label>
                                        <div class="dropzone lang-dropzone">
                                            <input type="file" name="mobile_image_{$language.id_lang}" id="mobile_image_{$language.id_lang}" class="image-upload lang-image-upload" data-preview="mobile-preview-{$language.id_lang}" accept="image/*" />
                                            <i class="icon icon-cloud-upload"></i>
                                            <p class="dropzone-message">{l s='Arrastra tu imagen aquí o haz clic para seleccionar' mod='sliderresponsivo'}</p>
                                            <p class="dropzone-info">{l s='Tamaño recomendado:' mod='sliderresponsivo'} {Configuration::get('SLIDERRESPONSIVO_WIDTH_MOBILE')}x{Configuration::get('SLIDERRESPONSIVO_HEIGHT_MOBILE')}px</p>
                                        </div>
                                        <div class="preview-box text-center lang-preview-box" data-lang="{$language.id_lang}" data-type="mobile" style="display: none;">
                                            <img id="mobile-preview-{$language.id_lang}" class="img-responsive img-thumbnail preview-image" src="" alt="" />
                                            <div>
                                                <label class="remove-lang-image">
                                                    <input type="checkbox" name="remove_mobile_image_{$language.id_lang}" value="1" class="remove-lang-image-checkbox" />
                                                    {l s='Quitar imagen de este idioma' mod='sliderresponsivo'}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="title_{$language.id_lang}">{l s='Título (SEO)' mod='sliderresponsivo'}</label>
                                    <input type="text" name="title_{$language.id_lang}" id="title_{$language.id_lang}" class="form-control" />
                                    <p class="help-block">{l s='Obligatorio solo si este idioma tiene imagen propia. No se muestra visiblemente sobre la imagen.' mod='sliderresponsivo'}</p>
                                </div>

                                <div class="form-group">
                                    <label for="description_{$language.id_lang}">{l s='Descripción (SEO)' mod='sliderresponsivo'}</label>
                                    <textarea name="description_{$language.id_lang}" id="description_{$language.id_lang}" class="form-control" rows="3"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="alt_{$language.id_lang}">{l s='Texto alternativo (SEO)' mod='sliderresponsivo'}</label>
                                    <input type="text" name="alt_{$language.id_lang}" id="alt_{$language.id_lang}" class="form-control" />
                                </div>
                            </div>
                        {/foreach}
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
