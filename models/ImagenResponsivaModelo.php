<?php
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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Modelo para gestionar imágenes del slider
 */
class SliderResponsivo_ImagenResponsivaModelo extends ObjectModel
{
    public $id_image;
    public $desktop_image;
    public $mobile_image;
    public $url;
    public $title;
    public $description;
    public $alt;
    public $position;
    public $active;
    public $date_add;
    public $date_upd;
    
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'sliderresponsivo_imagen',
        'primary' => 'id_image',
        'multilang' => true,
        'fields' => [
            'desktop_image' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'mobile_image' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'url' => ['type' => self::TYPE_STRING, 'validate' => 'isUrl', 'size' => 255],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
            
            // Campos multilenguaje
            'title' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'required' => true, 'size' => 255],
            'description' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 4000],
            'alt' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName', 'size' => 255],
        ],
    ];
    
    /**
     * Sobrescribir método para añadir fechas automáticamente
     */
    public function add($auto_date = true, $null_values = false)
    {
        if ($auto_date) {
            $this->date_add = date('Y-m-d H:i:s');
        }
        
        return parent::add($auto_date, $null_values);
    }
    
    /**
     * Sobrescribir método para actualizar fechas automáticamente
     */
    public function update($null_values = false)
    {
        $this->date_upd = date('Y-m-d H:i:s');
        
        return parent::update($null_values);
    }
    
    /**
     * Eliminar la imagen y sus archivos asociados
     */
    public function delete()
    {
        // Eliminar archivos físicos
        $module_dir = _PS_MODULE_DIR_.'sliderresponsivo/views/img/';
        
        if (!empty($this->desktop_image) && file_exists($module_dir.$this->desktop_image)) {
            @unlink($module_dir.$this->desktop_image);
        }
        
        if (!empty($this->mobile_image) && file_exists($module_dir.$this->mobile_image)) {
            @unlink($module_dir.$this->mobile_image);
        }
        
        return parent::delete();
    }
}
