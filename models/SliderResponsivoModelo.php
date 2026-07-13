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
 * Modelo para gestionar el slider completo
 */
class SliderResponsivo_SliderResponsivoModelo
{
    /**
     * Columnas comunes seleccionadas para las imágenes del slider.
     * Las imágenes de i.* actúan como imagen por defecto y las de il.*
     * como sobrescritura opcional para el idioma solicitado.
     */
    protected function getImageSelect()
    {
        return 'i.id_image, i.url, i.position, i.active, i.date_add, i.date_upd, '
            .'i.desktop_image AS default_desktop_image, i.mobile_image AS default_mobile_image, '
            .'il.id_lang, il.title, il.description, il.alt, '
            .'il.desktop_image AS lang_desktop_image, il.mobile_image AS lang_mobile_image';
    }

    /**
     * Resuelve la imagen efectiva (específica del idioma si existe, si no la predeterminada)
     * para una fila o un array de filas devueltas por getImageSelect().
     */
    protected function resolveLocalizedImages($rows)
    {
        if (empty($rows)) {
            return $rows;
        }

        $is_single = !isset($rows[0]) || !is_array($rows[0]);
        $items = $is_single ? [$rows] : $rows;

        foreach ($items as &$row) {
            $row['desktop_image'] = !empty($row['lang_desktop_image']) ? $row['lang_desktop_image'] : $row['default_desktop_image'];
            $row['mobile_image'] = !empty($row['lang_mobile_image']) ? $row['lang_mobile_image'] : $row['default_mobile_image'];
        }
        unset($row);

        return $is_single ? $items[0] : $items;
    }

    /**
     * Obtiene todas las imágenes activas ordenadas por posición
     */
    public function getActiveImages()
    {
        $query = new DbQuery();
        $query->select($this->getImageSelect());
        $query->from('sliderresponsivo_imagen', 'i');
        $query->innerJoin('sliderresponsivo_imagen_lang', 'il', 'i.id_image = il.id_image');
        $query->where('i.active = 1');
        $query->where('il.id_lang = '.(int)Context::getContext()->language->id);
        $query->orderBy('i.position ASC');

        $result = Db::getInstance()->executeS($query);

        return $result ? $this->resolveLocalizedImages($result) : [];
    }

    /**
     * Obtiene todas las imágenes (activas e inactivas) para administración
     */
    public function getAllImages()
    {
        $query = new DbQuery();
        $query->select($this->getImageSelect());
        $query->from('sliderresponsivo_imagen', 'i');
        $query->innerJoin('sliderresponsivo_imagen_lang', 'il', 'i.id_image = il.id_image');
        $query->where('il.id_lang = '.(int)Context::getContext()->language->id);
        $query->orderBy('i.position ASC');

        $result = Db::getInstance()->executeS($query);

        return $result ? $this->resolveLocalizedImages($result) : [];
    }

    /**
     * Obtiene una imagen por su ID
     */
    public function getImageById($id_image)
    {
        $query = new DbQuery();
        $query->select($this->getImageSelect());
        $query->from('sliderresponsivo_imagen', 'i');
        $query->innerJoin('sliderresponsivo_imagen_lang', 'il', 'i.id_image = il.id_image');
        $query->where('i.id_image = '.(int)$id_image);
        $query->where('il.id_lang = '.(int)Context::getContext()->language->id);

        $result = Db::getInstance()->getRow($query);

        return $result ? $this->resolveLocalizedImages($result) : false;
    }

    /**
     * Obtiene la última posición para una nueva imagen
     */
    public function getLastPosition()
    {
        $query = new DbQuery();
        $query->select('MAX(position) as max_position');
        $query->from('sliderresponsivo_imagen');

        $result = Db::getInstance()->getRow($query);

        return $result && isset($result['max_position']) ? (int)$result['max_position'] + 1 : 1;
    }

    /**
     * Verifica si una imagen existe por su ID
     */
    public function imageExists($id_image)
    {
        $query = new DbQuery();
        $query->select('COUNT(*)');
        $query->from('sliderresponsivo_imagen');
        $query->where('id_image = '.(int)$id_image);

        return (bool)Db::getInstance()->getValue($query);
    }

    /**
     * Actualiza la posición de una imagen
     */
    public function updatePosition($id_image, $position)
    {
        return Db::getInstance()->update(
            'sliderresponsivo_imagen',
            ['position' => (int)$position],
            'id_image = '.(int)$id_image
        );
    }

    /**
     * Actualiza el estado activo/inactivo de una imagen
     */
    public function updateStatus($id_image, $active)
    {
        return Db::getInstance()->update(
            'sliderresponsivo_imagen',
            ['active' => (int)$active],
            'id_image = '.(int)$id_image
        );
    }

    /**
     * Obtiene todas las imágenes para un idioma específico
     */
    public function getImagesForLanguage($id_lang)
    {
        $query = new DbQuery();
        $query->select($this->getImageSelect());
        $query->from('sliderresponsivo_imagen', 'i');
        $query->innerJoin('sliderresponsivo_imagen_lang', 'il', 'i.id_image = il.id_image');
        $query->where('il.id_lang = '.(int)$id_lang);
        $query->orderBy('i.position ASC');

        $result = Db::getInstance()->executeS($query);

        return $result ? $this->resolveLocalizedImages($result) : [];
    }

    /**
     * Obtiene el total de imágenes
     */
    public function getTotalImages()
    {
        $query = new DbQuery();
        $query->select('COUNT(*)');
        $query->from('sliderresponsivo_imagen');

        return (int)Db::getInstance()->getValue($query);
    }

    /**
     * Obtiene el total de imágenes activas
     */
    public function getTotalActiveImages()
    {
        $query = new DbQuery();
        $query->select('COUNT(*)');
        $query->from('sliderresponsivo_imagen');
        $query->where('active = 1');

        return (int)Db::getInstance()->getValue($query);
    }

    /**
     * Habilita todas las imágenes
     */
    public function enableAllImages()
    {
        return Db::getInstance()->update('sliderresponsivo_imagen', ['active' => 1]);
    }

    /**
     * Deshabilita todas las imágenes
     */
    public function disableAllImages()
    {
        return Db::getInstance()->update('sliderresponsivo_imagen', ['active' => 0]);
    }
}
