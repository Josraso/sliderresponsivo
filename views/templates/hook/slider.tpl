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

{if isset($images) && count($images) > 0}
<div class="slider-responsivo" data-effect="{$effect|escape:'html':'UTF-8'}" data-autoplay="{$autoplay|intval}" data-autoplay-speed="{$autoplay_speed|intval}">
    <div class="slides-container">
        {foreach from=$images item=image name=slides}
            <div class="slide{if $smarty.foreach.slides.first} active{/if}" aria-hidden="{if $smarty.foreach.slides.first}false{else}true{/if}" data-slide-index="{$smarty.foreach.slides.index}">
                {if $image.url}
                    <a href="{$image.url}" class="slide-link-wrapper" title="{$image.title|escape:'html':'UTF-8'}" {if $image.url|strpos:'http' === 0}target="_blank" rel="noopener"{/if}>
                {/if}
                <img 
                    src="{if $smarty.foreach.slides.first}{$img_url}{$image.desktop_image}{/if}" 
                    data-desktop="{$img_url}{$image.desktop_image}"
                    data-mobile="{$img_url}{$image.mobile_image}"
                    alt="{$image.alt|escape:'html':'UTF-8'}"
                    loading="{if $smarty.foreach.slides.first}eager{else}lazy{/if}"
                    width="{if $smarty.foreach.slides.first}{Configuration::get('SLIDERRESPONSIVO_WIDTH_DESKTOP')}{/if}"
                    height="{if $smarty.foreach.slides.first}{Configuration::get('SLIDERRESPONSIVO_HEIGHT_DESKTOP')}{/if}"
                />
                {if $image.url}
                    </a>
                {/if}
            </div>
        {/foreach}
    </div>

    {* Controles de navegación manual - solo si hay más de 1 imagen *}
    {if $images|count > 1}
        <button class="slider-control prev" aria-label="{l s='Imagen anterior' mod='sliderresponsivo'}" tabindex="0">
            <span aria-hidden="true">❮</span>
            <span class="sr-only">{l s='Anterior' mod='sliderresponsivo'}</span>
        </button>
        <button class="slider-control next" aria-label="{l s='Imagen siguiente' mod='sliderresponsivo'}" tabindex="0">
            <span aria-hidden="true">❯</span>
            <span class="sr-only">{l s='Siguiente' mod='sliderresponsivo'}</span>
        </button>

        {* Paginación (dots) *}
        <div class="slider-pagination" role="group" aria-label="{l s='Navegación de slides' mod='sliderresponsivo'}">
            {foreach from=$images item=image name=dots}
                <button class="slider-dot{if $smarty.foreach.dots.first} active{/if}" 
                        aria-label="{l s='Ir a la imagen' mod='sliderresponsivo'} {$smarty.foreach.dots.iteration} {l s='de' mod='sliderresponsivo'} {$images|count}"
                        aria-current="{if $smarty.foreach.dots.first}true{else}false{/if}"
                        data-slide-index="{$smarty.foreach.dots.index}"
                        tabindex="0">
                </button>
            {/foreach}
        </div>
    {/if}
    
    {* Elemento de anuncio para lectores de pantalla *}
    <div class="sr-only" aria-live="polite" aria-atomic="true"></div>
</div>
{/if}
