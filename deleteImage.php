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

// Este archivo debe colocarse en la raíz del módulo

if (!defined('_PS_VERSION_')) {
    exit;
}

// Inicializar contexto
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');

// Verificar autenticación de administrador
if (!Context::getContext()->employee->isLoggedBack()) {
    die('Acceso denegado');
}

// Verificar token de seguridad
$module_name = 'sliderresponsivo';
$token = Tools::getValue('token');
if ($token != Tools::getAdminTokenLite('AdminModules')) {
    die('Token inválido');
}

// Obtener ID de imagen
$id_image = (int)Tools::getValue('id_image');
if ($id_image <= 0) {
    die('ID de imagen inválido');
}

// Obtener información de la imagen para los archivos
$image_data = Db::getInstance()->getRow('SELECT desktop_image, mobile_image FROM '._DB_PREFIX_.'sliderresponsivo_imagen WHERE id_image = '.(int)$id_image);
if (!$image_data) {
    die('Imagen no encontrada');
}

// Guardar referencia a los archivos
$files_to_delete = [];
$upload_dir = _PS_MODULE_DIR_.'sliderresponsivo/views/img/';

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

// Verificar resultado
if ($deleted1 !== false && $deleted2 !== false) {
    // Eliminar archivos físicos
    foreach ($files_to_delete as $file) {
        @unlink($file);
    }
    
    // Reordenar posiciones
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
    
    // Redirigir con mensaje de éxito
    $url = Context::getContext()->link->getAdminLink('AdminModules') . '&configure=sliderresponsivo&deleteSuccess=1';
    Tools::redirectAdmin($url);
} else {
    // Redirigir con mensaje de error
    $url = Context::getContext()->link->getAdminLink('AdminModules') . '&configure=sliderresponsivo&deleteError=1';
    Tools::redirectAdmin($url);
}
