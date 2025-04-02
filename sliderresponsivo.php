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

require_once _PS_MODULE_DIR_.'sliderresponsivo/models/ImagenResponsivaModelo.php';
require_once _PS_MODULE_DIR_.'sliderresponsivo/models/SliderResponsivoModelo.php';

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class SliderResponsivo extends Module implements WidgetInterface
{
    protected $config_form = false;
    protected $default_width_desktop = 1920;
    protected $default_height_desktop = 600;
    protected $default_width_mobile = 768;
    protected $default_height_mobile = 500;
    protected $default_quality = 90;
    protected $default_effect = 'slide';

    public function __construct()
    {
        $this->name = 'sliderresponsivo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Slider Responsivo');
        $this->description = $this->l('Módulo de slider con imágenes específicas para escritorio y móvil');
        $this->confirmUninstall = $this->l('¿Está seguro que desea desinstalar este módulo?');
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => '8.99.99'];
    }

    /**
     * Instalación del módulo
     */
    public function install()
    {
        // Crear las tablas necesarias
        if (!file_exists(dirname(__FILE__).'/sql/instalar.php')) {
            return false;
        }
        
        $return = (bool)include(dirname(__FILE__).'/sql/instalar.php');
        
        if (!$return) {
            return false;
        }

        // Configuración predeterminada
        Configuration::updateValue('SLIDERRESPONSIVO_WIDTH_DESKTOP', $this->default_width_desktop);
        Configuration::updateValue('SLIDERRESPONSIVO_HEIGHT_DESKTOP', $this->default_height_desktop);
        Configuration::updateValue('SLIDERRESPONSIVO_WIDTH_MOBILE', $this->default_width_mobile);
        Configuration::updateValue('SLIDERRESPONSIVO_HEIGHT_MOBILE', $this->default_height_mobile);
        Configuration::updateValue('SLIDERRESPONSIVO_QUALITY', $this->default_quality);
        Configuration::updateValue('SLIDERRESPONSIVO_EFFECT', $this->default_effect);
        Configuration::updateValue('SLIDERRESPONSIVO_AUTOPLAY', 1);
        Configuration::updateValue('SLIDERRESPONSIVO_AUTOPLAY_SPEED', 5000);

        // Crear carpeta de imágenes si no existe
        $img_dir = _PS_MODULE_DIR_.$this->name.'/views/img/';
        if (!is_dir($img_dir)) {
            mkdir($img_dir, 0777, true);
        }

        // Crear carpeta de caché si no existe
        $cache_dir = _PS_MODULE_DIR_.$this->name.'/views/img/cache/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0777, true);
        }

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('backOfficeHeader');
    }

    /**
     * Desinstalación del módulo
     */
    public function uninstall()
    {
        // Ejecutar script de desinstalación
        if (!file_exists(dirname(__FILE__).'/sql/desinstalar.php')) {
            return false;
        }
        
        $return = (bool)include(dirname(__FILE__).'/sql/desinstalar.php');
        
        if (!$return) {
            return false;
        }

        // Eliminar configuración
        Configuration::deleteByName('SLIDERRESPONSIVO_WIDTH_DESKTOP');
        Configuration::deleteByName('SLIDERRESPONSIVO_HEIGHT_DESKTOP');
        Configuration::deleteByName('SLIDERRESPONSIVO_WIDTH_MOBILE');
        Configuration::deleteByName('SLIDERRESPONSIVO_HEIGHT_MOBILE');
        Configuration::deleteByName('SLIDERRESPONSIVO_QUALITY');
        Configuration::deleteByName('SLIDERRESPONSIVO_EFFECT');
        Configuration::deleteByName('SLIDERRESPONSIVO_AUTOPLAY');
        Configuration::deleteByName('SLIDERRESPONSIVO_AUTOPLAY_SPEED');

        return parent::uninstall();
    }

    /**
     * Carga de recursos CSS y JS para el panel de administración
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addJqueryUI('ui.draggable');
            $this->context->controller->addJqueryUI('ui.droppable');
        }
    }

    /**
     * Carga de recursos CSS y JS para el front office
     */
    public function hookHeader()
    {
        // Solo cargar recursos si hay imágenes activas
        $slider_model = new SliderResponsivo_SliderResponsivoModelo();
        $images = $slider_model->getActiveImages();
        
        if (!empty($images)) {
            $this->context->controller->addJS($this->_path.'views/js/front.js');
            $this->context->controller->addCSS($this->_path.'views/css/front.css');
            
            // Aseguramos que el punto de ruptura sea un valor válido
            $breakpoint = (int)Configuration::get('SLIDERRESPONSIVO_WIDTH_MOBILE');
            if ($breakpoint <= 0) {
                $breakpoint = 768; // Valor por defecto
            }
            
            // Pasar configuración al JS
            Media::addJsDef([
                'sliderResponsivoConfig' => [
                    'effect' => Configuration::get('SLIDERRESPONSIVO_EFFECT'),
                    'breakpoint' => $breakpoint,
                    'autoplay' => (int)Configuration::get('SLIDERRESPONSIVO_AUTOPLAY'),
                    'autoplaySpeed' => (int)Configuration::get('SLIDERRESPONSIVO_AUTOPLAY_SPEED')
                ]
            ]);
        }
    }

    /**
     * Renderizado del widget para el front office
     */
    public function renderWidget($hookName, array $configuration)
    {
        $slider_model = new SliderResponsivo_SliderResponsivoModelo();
        $images = $slider_model->getActiveImages();
        
        if (empty($images)) {
            return '';
        }
        
        $this->smarty->assign([
            'images' => $images,
            'effect' => Configuration::get('SLIDERRESPONSIVO_EFFECT'),
            'img_url' => $this->_path.'views/img/',
            'autoplay' => (int)Configuration::get('SLIDERRESPONSIVO_AUTOPLAY'),
            'autoplay_speed' => (int)Configuration::get('SLIDERRESPONSIVO_AUTOPLAY_SPEED')
        ]);
        
        return $this->fetch('module:sliderresponsivo/views/templates/hook/slider.tpl');
    }

    /**
     * Obtener la configuración del widget
     */
    public function getWidgetVariables($hookName, array $configuration)
    {
        return [];
    }

    /**
     * Configuración del módulo
     */
    public function getContent()
    {
        $output = '';
        
        // Manejar eliminación de imagen
        if (Tools::isSubmit('deleteImage')) {
            $id_image = (int)Tools::getValue('id_image');
            
            if ($id_image > 0) {
                // Obtener información de la imagen primero
                $image_data = Db::getInstance()->getRow('SELECT desktop_image, mobile_image FROM '._DB_PREFIX_.'sliderresponsivo_imagen WHERE id_image = '.(int)$id_image);
                
                if ($image_data) {
                    // Archivo de imágenes a borrar
                    $files_to_delete = [];
                    $upload_dir = _PS_MODULE_DIR_.$this->name.'/views/img/';
                    
                    if (!empty($image_data['desktop_image']) && file_exists($upload_dir.$image_data['desktop_image'])) {
                        $files_to_delete[] = $upload_dir.$image_data['desktop_image'];
                    }
                    
                    if (!empty($image_data['mobile_image']) && file_exists($upload_dir.$image_data['mobile_image'])) {
                        $files_to_delete[] = $upload_dir.$image_data['mobile_image'];
                    }
                    
                    // Eliminar primero los registros de idioma
                    $deleted1 = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'sliderresponsivo_imagen_lang` WHERE `id_image` = '.(int)$id_image);
                    
                    // Luego eliminar el registro principal
                    $deleted2 = Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'sliderresponsivo_imagen` WHERE `id_image` = '.(int)$id_image);
                    
                    if ($deleted1 !== false && $deleted2 !== false) {
                        // Eliminar archivos físicos
                        foreach ($files_to_delete as $file) {
                            @unlink($file);
                        }
                        
                        // Reordenar posiciones
                        $this->reorderImagePositions();
                        
                        $output .= $this->displayConfirmation($this->l('Imagen eliminada correctamente'));
                    } else {
                        $output .= $this->displayError($this->l('Error al eliminar la imagen de la base de datos'));
                    }
                } else {
                    $output .= $this->displayError($this->l('La imagen no existe'));
                }
            }
        }
        
        // AJAX: Obtener información de imagen para editar
        if (Tools::isSubmit('action') && Tools::getValue('action') == 'getImage') {
            $id_image = (int)Tools::getValue('id_image');
            if ($id_image > 0) {
                // Obtener datos de la imagen directamente con SQL
                $image_data = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'sliderresponsivo_imagen WHERE id_image = '.(int)$id_image);
                
                if ($image_data) {
                    // Obtener datos multilingüe
                    $languages_data = [];
                    $langs_result = Db::getInstance()->executeS(
                        'SELECT * FROM '._DB_PREFIX_.'sliderresponsivo_imagen_lang WHERE id_image = '.(int)$id_image
                    );
                    
                    foreach ($langs_result as $lang) {
                        $languages_data[$lang['id_lang']] = [
                            'title' => $lang['title'],
                            'description' => $lang['description'],
                            'alt' => $lang['alt']
                        ];
                    }
                    
                    die(json_encode([
                        'success' => true,
                        'image' => [
                            'id_image' => $image_data['id_image'],
                            'desktop_image' => $image_data['desktop_image'],
                            'mobile_image' => $image_data['mobile_image'],
                            'url' => $image_data['url'],
                            'position' => $image_data['position'],
                            'active' => $image_data['active'],
                            'desktop_url' => $this->_path.'views/img/'.$image_data['desktop_image'],
                            'mobile_url' => $this->_path.'views/img/'.$image_data['mobile_image'],
                            'languages' => $languages_data
                        ]
                    ]));
                }
            }
            
            die(json_encode(['success' => false]));
        }

        // Procesar el formulario de configuración
        if (Tools::isSubmit('submitSliderResponsivoConfig')) {
            $width_desktop = (int)Tools::getValue('SLIDERRESPONSIVO_WIDTH_DESKTOP');
            $height_desktop = (int)Tools::getValue('SLIDERRESPONSIVO_HEIGHT_DESKTOP');
            $width_mobile = (int)Tools::getValue('SLIDERRESPONSIVO_WIDTH_MOBILE');
            $height_mobile = (int)Tools::getValue('SLIDERRESPONSIVO_HEIGHT_MOBILE');
            $quality = (int)Tools::getValue('SLIDERRESPONSIVO_QUALITY');
            $effect = Tools::getValue('SLIDERRESPONSIVO_EFFECT');
            $autoplay = (int)Tools::getValue('SLIDERRESPONSIVO_AUTOPLAY');
            $autoplay_speed = (int)Tools::getValue('SLIDERRESPONSIVO_AUTOPLAY_SPEED');

            // Validaciones
            $errors = [];
            if ($width_desktop <= 0) {
                $errors[] = $this->l('El ancho para escritorio debe ser mayor que 0');
            }
            if ($height_desktop <= 0) {
                $errors[] = $this->l('La altura para escritorio debe ser mayor que 0');
            }
            if ($width_mobile <= 0) {
                $errors[] = $this->l('El ancho para móvil debe ser mayor que 0');
            }
            if ($height_mobile <= 0) {
                $errors[] = $this->l('La altura para móvil debe ser mayor que 0');
            }
            if ($quality <= 0 || $quality > 100) {
                $errors[] = $this->l('La calidad debe estar entre 1 y 100');
            }
            if (!in_array($effect, ['slide', 'fade', 'zoom'])) {
                $errors[] = $this->l('Efecto no válido');
            }
            if ($autoplay_speed < 1000) {
                $errors[] = $this->l('La velocidad de autoplay debe ser al menos 1000ms');
            }

            // Guardar configuración si no hay errores
            if (empty($errors)) {
                Configuration::updateValue('SLIDERRESPONSIVO_WIDTH_DESKTOP', $width_desktop);
                Configuration::updateValue('SLIDERRESPONSIVO_HEIGHT_DESKTOP', $height_desktop);
                Configuration::updateValue('SLIDERRESPONSIVO_WIDTH_MOBILE', $width_mobile);
                Configuration::updateValue('SLIDERRESPONSIVO_HEIGHT_MOBILE', $height_mobile);
                Configuration::updateValue('SLIDERRESPONSIVO_QUALITY', $quality);
                Configuration::updateValue('SLIDERRESPONSIVO_EFFECT', $effect);
                Configuration::updateValue('SLIDERRESPONSIVO_AUTOPLAY', $autoplay);
                Configuration::updateValue('SLIDERRESPONSIVO_AUTOPLAY_SPEED', $autoplay_speed);
                
                $output .= $this->displayConfirmation($this->l('Configuración actualizada'));
            } else {
                foreach ($errors as $error) {
                    $output .= $this->displayError($error);
                }
            }
        }
        
        // Procesar formulario de imagen
        $this->processImageForm($output);
        
        // Preparar variables para la plantilla
        $this->context->smarty->assign([
            'module_dir' => $this->_path,
            'current_url' => $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name,
            'SLIDERRESPONSIVO_WIDTH_DESKTOP' => Configuration::get('SLIDERRESPONSIVO_WIDTH_DESKTOP', null, null, null, $this->default_width_desktop),
            'SLIDERRESPONSIVO_HEIGHT_DESKTOP' => Configuration::get('SLIDERRESPONSIVO_HEIGHT_DESKTOP', null, null, null, $this->default_height_desktop),
            'SLIDERRESPONSIVO_WIDTH_MOBILE' => Configuration::get('SLIDERRESPONSIVO_WIDTH_MOBILE', null, null, null, $this->default_width_mobile),
            'SLIDERRESPONSIVO_HEIGHT_MOBILE' => Configuration::get('SLIDERRESPONSIVO_HEIGHT_MOBILE', null, null, null, $this->default_height_mobile),
            'SLIDERRESPONSIVO_QUALITY' => Configuration::get('SLIDERRESPONSIVO_QUALITY', null, null, null, $this->default_quality),
            'SLIDERRESPONSIVO_EFFECT' => Configuration::get('SLIDERRESPONSIVO_EFFECT', null, null, null, $this->default_effect),
            'SLIDERRESPONSIVO_AUTOPLAY' => Configuration::get('SLIDERRESPONSIVO_AUTOPLAY', null, null, null, 1),
            'SLIDERRESPONSIVO_AUTOPLAY_SPEED' => Configuration::get('SLIDERRESPONSIVO_AUTOPLAY_SPEED', null, null, null, 5000),
        ]);
        
        // Renderizar plantilla personalizada para configuración
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/config_form.tpl');
        
        // Listar imágenes existentes
        $output .= $this->renderImageList();
        
        return $output;
    }

    /**
     * Reordenar posiciones de las imágenes después de eliminar una
     */
    protected function reorderImagePositions()
    {
        // Obtener todas las imágenes ordenadas por posición
        $images = Db::getInstance()->executeS('SELECT id_image FROM '._DB_PREFIX_.'sliderresponsivo_imagen ORDER BY position ASC');
        
        if (!empty($images)) {
            $position = 1;
            foreach ($images as $image) {
                Db::getInstance()->update(
                    'sliderresponsivo_imagen',
                    ['position' => $position],
                    'id_image = '.(int)$image['id_image']
                );
                $position++;
            }
        }
    }

    /**
     * Procesar formulario de imagen
     */
    protected function processImageForm(&$output)
    {
        // Guardar nueva imagen o actualizar existente
        if (Tools::isSubmit('submitImage')) {
            $id_image = (int)Tools::getValue('id_image');
            $url = Tools::getValue('url');
            $active = (int)Tools::getValue('active');
            $title = [];
            $description = [];
            $alt = [];
            $errors = [];
            
            // Validar campos multilingües
            $languages = Language::getLanguages(false);
            foreach ($languages as $language) {
                $title[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
                $description[$language['id_lang']] = Tools::getValue('description_'.$language['id_lang']);
                $alt[$language['id_lang']] = Tools::getValue('alt_'.$language['id_lang']);
                
                if (empty($title[$language['id_lang']])) {
                    $errors[] = $this->l('El título es obligatorio para ').strtoupper($language['iso_code']);
                }
            }
            
            // Validar si es una imagen nueva o edición
            if ($id_image > 0) {
                // Edición: Comprobar que la imagen existe mediante SQL directo
                $exists = (bool)Db::getInstance()->getValue('SELECT COUNT(*) FROM '._DB_PREFIX_.'sliderresponsivo_imagen WHERE id_image = '.(int)$id_image);
                if (!$exists) {
                    $errors[] = $this->l('La imagen que se intenta editar no existe');
                }
            } else {
                // Nueva imagen: Comprobar que se han subido imágenes
                if (!isset($_FILES['desktop_image']) || empty($_FILES['desktop_image']['tmp_name'])) {
                    $errors[] = $this->l('La imagen de escritorio es obligatoria');
                }
                if (!isset($_FILES['mobile_image']) || empty($_FILES['mobile_image']['tmp_name'])) {
                    $errors[] = $this->l('La imagen de móvil es obligatoria');
                }
            }
            
            // Procesar si no hay errores
            if (empty($errors)) {
                // Usar SQL directo en lugar de ObjectModel
                $upload_dir = _PS_MODULE_DIR_.$this->name.'/views/img/';
                $date_now = date('Y-m-d H:i:s');
                
                if ($id_image > 0) {
                    // Editar imagen existente - obtener datos actuales
                    $current_image = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'sliderresponsivo_imagen WHERE id_image = '.(int)$id_image);
                    $desktop_image = $current_image['desktop_image'];
                    $mobile_image = $current_image['mobile_image'];
                    
                    // Imagen de escritorio
                    if (isset($_FILES['desktop_image']) && !empty($_FILES['desktop_image']['tmp_name'])) {
                        $ext = pathinfo($_FILES['desktop_image']['name'], PATHINFO_EXTENSION);
                        $desktop_filename = 'desktop_'.time().'_'.$id_image.'.'.$ext;
                        
                        // Redimensionar y optimizar imagen
                        $this->processUploadedImage(
                            $_FILES['desktop_image']['tmp_name'],
                            $upload_dir.$desktop_filename,
                            Configuration::get('SLIDERRESPONSIVO_WIDTH_DESKTOP'),
                            Configuration::get('SLIDERRESPONSIVO_HEIGHT_DESKTOP'),
                            Configuration::get('SLIDERRESPONSIVO_QUALITY')
                        );
                        
                        // Eliminar imagen anterior si existe
                        if (!empty($current_image['desktop_image']) && file_exists($upload_dir.$current_image['desktop_image'])) {
                            @unlink($upload_dir.$current_image['desktop_image']);
                        }
                        
                        $desktop_image = $desktop_filename;
                    }
                    
                    // Imagen de móvil
                    if (isset($_FILES['mobile_image']) && !empty($_FILES['mobile_image']['tmp_name'])) {
                        $ext = pathinfo($_FILES['mobile_image']['name'], PATHINFO_EXTENSION);
                        $mobile_filename = 'mobile_'.time().'_'.$id_image.'.'.$ext;
                        
                        // Redimensionar y optimizar imagen
                        $this->processUploadedImage(
                            $_FILES['mobile_image']['tmp_name'],
                            $upload_dir.$mobile_filename,
                            Configuration::get('SLIDERRESPONSIVO_WIDTH_MOBILE'),
                            Configuration::get('SLIDERRESPONSIVO_HEIGHT_MOBILE'),
                            Configuration::get('SLIDERRESPONSIVO_QUALITY')
                        );
                        
                        // Eliminar imagen anterior si existe
                        if (!empty($current_image['mobile_image']) && file_exists($upload_dir.$current_image['mobile_image'])) {
                            @unlink($upload_dir.$current_image['mobile_image']);
                        }
                        
                        $mobile_image = $mobile_filename;
                    }
                    
                    // Actualizar la imagen en la base de datos
                    $updated = Db::getInstance()->update(
                        'sliderresponsivo_imagen',
                        [
                            'desktop_image' => pSQL($desktop_image),
                            'mobile_image' => pSQL($mobile_image),
                            'url' => pSQL($url),
                            'active' => (int)$active,
                            'date_upd' => pSQL($date_now)
                        ],
                        'id_image = '.(int)$id_image
                    );
                    
                    // Actualizar los campos multilingüe
                    $success = $updated;
                    foreach ($languages as $language) {
                        $success &= Db::getInstance()->update(
                            'sliderresponsivo_imagen_lang',
                            [
                                'title' => pSQL($title[$language['id_lang']]),
                                'description' => pSQL($description[$language['id_lang']], true),
                                'alt' => pSQL($alt[$language['id_lang']])
                            ],
                            'id_image = '.(int)$id_image.' AND id_lang = '.(int)$language['id_lang']
                        );
                    }
                    
                    if ($success) {
                        $output .= $this->displayConfirmation($this->l('Imagen actualizada correctamente'));
                    } else {
                        $output .= $this->displayError($this->l('Error al actualizar la imagen'));
                    }
                } else {
                    // Nueva imagen
                    $position = (int)Db::getInstance()->getValue('SELECT IFNULL(MAX(position), 0) + 1 FROM '._DB_PREFIX_.'sliderresponsivo_imagen');
                    
                    // Procesar imagen de escritorio
                    $ext = pathinfo($_FILES['desktop_image']['name'], PATHINFO_EXTENSION);
                    $desktop_filename = 'desktop_'.time().'_new.'.$ext;
                    
                    $this->processUploadedImage(
                        $_FILES['desktop_image']['tmp_name'],
                        $upload_dir.$desktop_filename,
                        Configuration::get('SLIDERRESPONSIVO_WIDTH_DESKTOP'),
                        Configuration::get('SLIDERRESPONSIVO_HEIGHT_DESKTOP'),
                        Configuration::get('SLIDERRESPONSIVO_QUALITY')
                    );
                    
                    // Procesar imagen de móvil
                    $ext = pathinfo($_FILES['mobile_image']['name'], PATHINFO_EXTENSION);
                    $mobile_filename = 'mobile_'.time().'_new.'.$ext;
                    
                    $this->processUploadedImage(
                        $_FILES['mobile_image']['tmp_name'],
                        $upload_dir.$mobile_filename,
                        Configuration::get('SLIDERRESPONSIVO_WIDTH_MOBILE'),
                        Configuration::get('SLIDERRESPONSIVO_HEIGHT_MOBILE'),
                        Configuration::get('SLIDERRESPONSIVO_QUALITY')
                    );
                    
                    // Insertar en la base de datos
                    $inserted = Db::getInstance()->insert(
                        'sliderresponsivo_imagen',
                        [
                            'desktop_image' => pSQL($desktop_filename),
                            'mobile_image' => pSQL($mobile_filename),
                            'url' => pSQL($url),
                            'position' => (int)$position,
                            'active' => (int)$active,
                            'date_add' => pSQL($date_now),
                            'date_upd' => pSQL($date_now)
                        ]
                    );
                    
                    if ($inserted) {
                        $id_image = (int)Db::getInstance()->Insert_ID();
                        
                        // Insertar campos multilingüe
                        $success = true;
                        foreach ($languages as $language) {
                            $success &= Db::getInstance()->insert(
                                'sliderresponsivo_imagen_lang',
                                [
                                    'id_image' => (int)$id_image,
                                    'id_lang' => (int)$language['id_lang'],
                                    'title' => pSQL($title[$language['id_lang']]),
                                    'description' => pSQL($description[$language['id_lang']], true),
                                    'alt' => pSQL($alt[$language['id_lang']])
                                ]
                            );
                        }
                        
                        if ($success) {
                            $output .= $this->displayConfirmation($this->l('Nueva imagen añadida correctamente'));
                        } else {
                            $output .= $this->displayError($this->l('Error al guardar los datos multilingüe'));
                            
                            // Intentar eliminar la imagen principal si hubo error en las traducciones
                            Db::getInstance()->delete('sliderresponsivo_imagen', 'id_image = '.(int)$id_image);
                        }
                    } else {
                        $output .= $this->displayError($this->l('Error al guardar la nueva imagen'));
                        
                        // Eliminar archivos si hubo error
                        @unlink($upload_dir.$desktop_filename);
                        @unlink($upload_dir.$mobile_filename);
                    }
                }
            } else {
                foreach ($errors as $error) {
                    $output .= $this->displayError($error);
                }
            }
        }
        
        // Cambiar estado de imagen - CORREGIDO
        if (Tools::isSubmit('changeImageStatus')) {
            $id_image = (int)Tools::getValue('id_image');
            if ($id_image > 0) {
                try {
                    // Obtener estado actual
                    $result = Db::getInstance()->getRow('SELECT active FROM '._DB_PREFIX_.'sliderresponsivo_imagen WHERE id_image = '.(int)$id_image);
                    
                    if ($result) {
                        $new_status = $result['active'] ? 0 : 1;
                        
                        // Actualizar usando SQL directo para mayor fiabilidad
                        $updated = Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'sliderresponsivo_imagen SET active = '.(int)$new_status.' WHERE id_image = '.(int)$id_image);
                        
                        if ($updated) {
                            $output .= $this->displayConfirmation($this->l('Estado actualizado correctamente'));
                            
                            // Si es una solicitud AJAX, devolver respuesta JSON
                            if (Tools::getValue('ajax')) {
                                die(json_encode([
                                    'success' => true,
                                    'status' => $new_status,
                                    'message' => $this->l('Estado actualizado correctamente')
                                ]));
                            }
                        } else {
                            $output .= $this->displayError($this->l('Error al actualizar el estado'));
                            
                            // Si es una solicitud AJAX, devolver respuesta JSON
                            if (Tools::getValue('ajax')) {
                                die(json_encode([
                                    'success' => false,
                                    'message' => $this->l('Error al actualizar el estado')
                                ]));
                            }
                        }
                    } else {
                        $output .= $this->displayError($this->l('La imagen no existe'));
                       
                       // Si es una solicitud AJAX, devolver respuesta JSON
                       if (Tools::getValue('ajax')) {
                           die(json_encode([
                               'success' => false,
                               'message' => $this->l('La imagen no existe')
                           ]));
                       }
                   }
               } catch (Exception $e) {
                   $output .= $this->displayError($this->l('Error al actualizar el estado: ').$e->getMessage());
                   
                   // Si es una solicitud AJAX, devolver respuesta JSON
                   if (Tools::getValue('ajax')) {
                       die(json_encode([
                           'success' => false,
                           'message' => $this->l('Error al actualizar el estado: ').$e->getMessage()
                       ]));
                   }
               }
           }
       }
       
       // Reordenar imágenes
       if (Tools::isSubmit('updatePositions')) {
           $positions = Tools::getValue('image_position');
           if (is_array($positions)) {
               $success = true;
               foreach ($positions as $id => $position) {
                   // Actualizar directamente con consulta SQL
                   $success &= Db::getInstance()->update(
                       'sliderresponsivo_imagen',
                       ['position' => (int)$position],
                       'id_image = '.(int)$id
                   );
               }
               
               if ($success) {
                   $output .= $this->displayConfirmation($this->l('Posiciones actualizadas correctamente'));
               } else {
                   $output .= $this->displayError($this->l('Error al actualizar algunas posiciones'));
               }
           }
       }
   }

   /**
    * Procesar imagen subida con mejor preservación de proporciones
    */
   protected function processUploadedImage($source, $destination, $width, $height, $quality)
   {
       // Crear directorio si no existe
       $dir = dirname($destination);
       if (!is_dir($dir)) {
           mkdir($dir, 0777, true);
       }
       
       // Obtener información de la imagen
       list($src_width, $src_height, $type) = getimagesize($source);
       
       // Crear imagen según el formato
       switch ($type) {
           case IMAGETYPE_JPEG:
               $src_img = imagecreatefromjpeg($source);
               break;
           case IMAGETYPE_PNG:
               $src_img = imagecreatefrompng($source);
               break;
           case IMAGETYPE_GIF:
               $src_img = imagecreatefromgif($source);
               break;
           default:
               return false;
       }
       
       // Verificar si la imagen es cuadrada o casi cuadrada
       $is_square = abs($src_width - $src_height) / max($src_width, $src_height) < 0.1;
       
       // Si es cuadrada, usamos dimensiones cuadradas para el destino
       if ($is_square) {
           $min_dimension = min($width, $height);
           $width = $height = $min_dimension;
       }
       
       // Calcular las proporciones
       $ratio_orig = $src_width / $src_height;
       $ratio_dest = $width / $height;
       
       // Calcular las nuevas dimensiones manteniendo la proporción original
       if ($ratio_dest > $ratio_orig) {
           $new_width = $height * $ratio_orig;
           $new_height = $height;
       } else {
           $new_width = $width;
           $new_height = $width / $ratio_orig;
       }
       
       // Crear imagen de destino
       $dst_img = imagecreatetruecolor($new_width, $new_height); // Usar dimensiones proporcionales
       
       // Conservar transparencia si es PNG
       if ($type == IMAGETYPE_PNG) {
           imagealphablending($dst_img, false);
           imagesavealpha($dst_img, true);
           $transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
           imagefilledrectangle($dst_img, 0, 0, $new_width, $new_height, $transparent);
       } else {
           // Rellenar con blanco para JPEG/GIF
           $white = imagecolorallocate($dst_img, 255, 255, 255);
           imagefilledrectangle($dst_img, 0, 0, $new_width, $new_height, $white);
       }
       
       // Redimensionar manteniendo proporciones
       imagecopyresampled(
           $dst_img,
           $src_img,
           0,
           0,
           0,
           0,
           $new_width,
           $new_height,
           $src_width,
           $src_height
       );
       
       // Guardar imagen según formato
       $result = false;
       switch ($type) {
           case IMAGETYPE_JPEG:
               $result = imagejpeg($dst_img, $destination, $quality);
               break;
           case IMAGETYPE_PNG:
               // Convertir calidad de 0-100 a 0-9 para PNG
               $png_quality = 9 - round(($quality / 100) * 9);
               $result = imagepng($dst_img, $destination, $png_quality);
               break;
           case IMAGETYPE_GIF:
               $result = imagegif($dst_img, $destination);
               break;
       }
       
       // Liberar memoria
       imagedestroy($src_img);
       imagedestroy($dst_img);
       
       return $result;
   }

   /**
    * Renderizar lista de imágenes
    */
   protected function renderImageList()
   {
       $slider_model = new SliderResponsivo_SliderResponsivoModelo();
       $images = $slider_model->getAllImages();
       
       $this->context->smarty->assign([
           'link' => $this->context->link,
           'images' => $images,
           'img_url' => $this->_path.'views/img/',
           'current_url' => $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name,
           'languages' => Language::getLanguages(false),
           'default_lang' => (int)Configuration::get('PS_LANG_DEFAULT'),
           'id_language' => $this->context->language->id,
           'token' => Tools::getAdminTokenLite('AdminModules'),
           'module_version' => $this->version
       ]);
       
       return $this->display(__FILE__, 'views/templates/admin/image_list.tpl');
   }
}