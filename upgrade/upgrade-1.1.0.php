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
 * Añade las columnas de imagen específicas por idioma (sobrescritura opcional
 * de la imagen predeterminada de escritorio/móvil para cada idioma).
 */
function upgrade_module_1_1_0($module)
{
    $table = _DB_PREFIX_.'sliderresponsivo_imagen_lang';

    $existing_columns = [];
    $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.$table.'`');
    if (is_array($columns)) {
        foreach ($columns as $column) {
            $existing_columns[] = $column['Field'];
        }
    }

    $success = true;

    if (!in_array('desktop_image', $existing_columns, true)) {
        $success = $success && Db::getInstance()->execute(
            'ALTER TABLE `'.$table.'` ADD `desktop_image` varchar(255) DEFAULT NULL AFTER `alt`'
        );
    }

    if (!in_array('mobile_image', $existing_columns, true)) {
        $success = $success && Db::getInstance()->execute(
            'ALTER TABLE `'.$table.'` ADD `mobile_image` varchar(255) DEFAULT NULL AFTER `desktop_image`'
        );
    }

    return (bool)$success;
}
