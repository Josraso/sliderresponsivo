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

<div class="panel" id="configuration-panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i> {l s='Configuración del Slider' mod='sliderresponsivo'}
        <span class="panel-heading-action">
            <a class="list-toolbar-btn toggle-config" href="#">
                <i class="icon-caret-down"></i>
            </a>
        </span>
        <small class="toggle-hint"> {l s='(Haga clic en la flecha para mostrar/ocultar la configuración)' mod='sliderresponsivo'}</small>
    </div>
    
    <div class="config-collapse" style="display:none;">
        <form id="module_form" class="defaultForm form-horizontal" action="{$current_url|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" novalidate>
            <div class="panel-body">
                <div class="form-wrapper">
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Ancho de imagen para escritorio' mod='sliderresponsivo'}</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input type="text" name="SLIDERRESPONSIVO_WIDTH_DESKTOP" class="form-control" value="{$SLIDERRESPONSIVO_WIDTH_DESKTOP|escape:'html':'UTF-8'}">
                                <span class="input-group-addon">px</span>
                            </div>
                            <p class="help-block">{l s='Ancho en píxeles para la versión de escritorio' mod='sliderresponsivo'}</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Altura de imagen para escritorio' mod='sliderresponsivo'}</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input type="text" name="SLIDERRESPONSIVO_HEIGHT_DESKTOP" class="form-control" value="{$SLIDERRESPONSIVO_HEIGHT_DESKTOP|escape:'html':'UTF-8'}">
                                <span class="input-group-addon">px</span>
                            </div>
                            <p class="help-block">{l s='Altura en píxeles para la versión de escritorio' mod='sliderresponsivo'}</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Ancho de imagen para móvil' mod='sliderresponsivo'}</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input type="text" name="SLIDERRESPONSIVO_WIDTH_MOBILE" class="form-control" value="{$SLIDERRESPONSIVO_WIDTH_MOBILE|escape:'html':'UTF-8'}">
                                <span class="input-group-addon">px</span>
                            </div>
                            <p class="help-block">{l s='Ancho en píxeles para la versión móvil' mod='sliderresponsivo'}</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Altura de imagen para móvil' mod='sliderresponsivo'}</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input type="text" name="SLIDERRESPONSIVO_HEIGHT_MOBILE" class="form-control" value="{$SLIDERRESPONSIVO_HEIGHT_MOBILE|escape:'html':'UTF-8'}">
                                <span class="input-group-addon">px</span>
                            </div>
                            <p class="help-block">{l s='Altura en píxeles para la versión móvil' mod='sliderresponsivo'}</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Calidad de imagen' mod='sliderresponsivo'}</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input type="text" name="SLIDERRESPONSIVO_QUALITY" class="form-control" value="{$SLIDERRESPONSIVO_QUALITY|escape:'html':'UTF-8'}">
                                <span class="input-group-addon">%</span>
                            </div>
                            <p class="help-block">{l s='Calidad de compresión JPEG (1-100)' mod='sliderresponsivo'}</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Efecto de transición' mod='sliderresponsivo'}</label>
                        <div class="col-lg-9">
                            <select name="SLIDERRESPONSIVO_EFFECT" class="form-control">
                                <option value="slide" {if $SLIDERRESPONSIVO_EFFECT == 'slide'}selected="selected"{/if}>{l s='Deslizamiento' mod='sliderresponsivo'}</option>
                                <option value="fade" {if $SLIDERRESPONSIVO_EFFECT == 'fade'}selected="selected"{/if}>{l s='Desvanecer' mod='sliderresponsivo'}</option>
                                <option value="zoom" {if $SLIDERRESPONSIVO_EFFECT == 'zoom'}selected="selected"{/if}>{l s='Zoom' mod='sliderresponsivo'}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Autoplay' mod='sliderresponsivo'}</label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="SLIDERRESPONSIVO_AUTOPLAY" id="SLIDERRESPONSIVO_AUTOPLAY_on" value="1" {if $SLIDERRESPONSIVO_AUTOPLAY}checked="checked"{/if}>
                                <label for="SLIDERRESPONSIVO_AUTOPLAY_on">{l s='Sí' mod='sliderresponsivo'}</label>
                                <input type="radio" name="SLIDERRESPONSIVO_AUTOPLAY" id="SLIDERRESPONSIVO_AUTOPLAY_off" value="0" {if !$SLIDERRESPONSIVO_AUTOPLAY}checked="checked"{/if}>
                                <label for="SLIDERRESPONSIVO_AUTOPLAY_off">{l s='No' mod='sliderresponsivo'}</label>
                                <a class="slide-button btn"></a>
                            </span>
                            <p class="help-block">{l s='Activar reproducción automática del slider' mod='sliderresponsivo'}</p>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Velocidad de autoplay' mod='sliderresponsivo'}</label>
                        <div class="col-lg-9">
                            <div class="input-group">
                                <input type="text" name="SLIDERRESPONSIVO_AUTOPLAY_SPEED" class="form-control" value="{$SLIDERRESPONSIVO_AUTOPLAY_SPEED|escape:'html':'UTF-8'}">
                                <span class="input-group-addon">ms</span>
                            </div>
                            <p class="help-block">{l s='Tiempo entre transiciones en milisegundos (1000ms = 1s)' mod='sliderresponsivo'}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="panel-footer">
                <button type="submit" class="btn btn-default pull-right" name="submitSliderResponsivoConfig">
                    <i class="process-icon-save"></i> {l s='Guardar' mod='sliderresponsivo'}
                </button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Manejar el toggle del acordeón
        $('.toggle-config').on('click', function(e) {
            e.preventDefault();
            var $icon = $(this).find('i');
            var $collapse = $('.config-collapse');
            
            $collapse.slideToggle(300, function() {
                if ($(this).is(':visible')) {
                    $icon.removeClass('icon-caret-down').addClass('icon-caret-up');
                } else {
                    $icon.removeClass('icon-caret-up').addClass('icon-caret-down');
                }
            });
        });
    });
</script>
