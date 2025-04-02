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

// Eliminar imágenes almacenadas físicamente
$img_dir = _PS_MODULE_DIR_.'sliderresponsivo/views/img/';
if (is_dir($img_dir)) {
    $files = glob($img_dir.'*');
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
}

// Eliminar tablas
$sql = [];
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'sliderresponsivo_imagen_lang`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'sliderresponsivo_imagen`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
