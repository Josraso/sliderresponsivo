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
 * Simplifica el modelo de datos: las imágenes dejan de tener una versión
 * "general" y una versión "por idioma". Ahora solo existen las imágenes
 * por idioma; si un idioma no tiene imagen propia, en el front se usa la
 * de otro idioma que sí la tenga (junto con su título/alt/descripción).
 */
function upgrade_module_1_2_0($module)
{
    $main_table = _DB_PREFIX_.'sliderresponsivo_imagen';
    $lang_table = _DB_PREFIX_.'sliderresponsivo_imagen_lang';

    $main_columns = [];
    $columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.$main_table.'`');
    if (is_array($columns)) {
        foreach ($columns as $column) {
            $main_columns[] = $column['Field'];
        }
    }

    $had_general_images = in_array('desktop_image', $main_columns, true) || in_array('mobile_image', $main_columns, true);

    if ($had_general_images) {
        // Migrar la imagen general (si la había) al idioma por defecto de la
        // tienda, únicamente si ese idioma aún no tiene su propia imagen.
        $default_id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $images = Db::getInstance()->executeS('SELECT id_image, desktop_image, mobile_image FROM `'.$main_table.'`');

        if (is_array($images)) {
            foreach ($images as $image) {
                if (empty($image['desktop_image']) && empty($image['mobile_image'])) {
                    continue;
                }

                $lang_row = Db::getInstance()->getRow(
                    'SELECT desktop_image, mobile_image FROM `'.$lang_table.'` '
                    .'WHERE id_image = '.(int)$image['id_image'].' AND id_lang = '.$default_id_lang
                );

                if ($lang_row === false) {
                    // No existe fila para el idioma por defecto (no debería
                    // ocurrir, pero por seguridad la creamos).
                    Db::getInstance()->insert($lang_table, [
                        'id_image' => (int)$image['id_image'],
                        'id_lang' => $default_id_lang,
                        'desktop_image' => pSQL($image['desktop_image']),
                        'mobile_image' => pSQL($image['mobile_image']),
                    ], false, true, Db::INSERT_IGNORE);
                } elseif (empty($lang_row['desktop_image']) && empty($lang_row['mobile_image'])) {
                    Db::getInstance()->update(
                        'sliderresponsivo_imagen_lang',
                        [
                            'desktop_image' => pSQL($image['desktop_image']),
                            'mobile_image' => pSQL($image['mobile_image']),
                        ],
                        'id_image = '.(int)$image['id_image'].' AND id_lang = '.$default_id_lang
                    );
                }
            }
        }

        Db::getInstance()->execute('ALTER TABLE `'.$main_table.'` DROP COLUMN `desktop_image`');
        Db::getInstance()->execute('ALTER TABLE `'.$main_table.'` DROP COLUMN `mobile_image`');
    }

    return true;
}
