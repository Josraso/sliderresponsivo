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

// Comprobar si las tablas ya existen antes de intentar crearlas
$tables_exists = false;
$result = Db::getInstance()->executeS('SHOW TABLES LIKE \''._DB_PREFIX_.'sliderresponsivo_imagen\'');
if (!empty($result)) {
    $tables_exists = true;
}

// Si las tablas ya existen, no continuar
if ($tables_exists) {
    return true;
}

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sliderresponsivo_imagen` (
    `id_image` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `desktop_image` varchar(255) NOT NULL,
    `mobile_image` varchar(255) NOT NULL,
    `url` varchar(255) DEFAULT NULL,
    `position` int(10) UNSIGNED NOT NULL DEFAULT 0,
    `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_image`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sliderresponsivo_imagen_lang` (
    `id_image` int(10) UNSIGNED NOT NULL,
    `id_lang` int(10) UNSIGNED NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `alt` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id_image`,`id_lang`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';

// Crear tablas SQL
foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}

return true;
