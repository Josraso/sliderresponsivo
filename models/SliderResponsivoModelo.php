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
 * Modelo para gestionar el slider completo.
 *
 * Las imágenes (y su título/descripción/alt) se guardan únicamente por
 * idioma. Si el idioma solicitado no tiene imagen propia, se usa la de
 * cualquier otro idioma que sí la tenga (priorizando el idioma por
 * defecto de la tienda), junto con su título/descripción/alt.
 */
class SliderResponsivo_SliderResponsivoModelo
{
    /**
     * Obtiene todas las imágenes activas ordenadas por posición, con el
     * contenido (imagen + textos) resuelto para el idioma actual del front.
     */
    public function getActiveImages()
    {
        return $this->getImagesWithFallback(true, Context::getContext()->language->id);
    }

    /**
     * Obtiene todas las imágenes (activas e inactivas) para administración,
     * con el contenido resuelto para el idioma actual del back office.
     */
    public function getAllImages()
    {
        return $this->getImagesWithFallback(false, Context::getContext()->language->id);
    }

    /**
     * Obtiene una imagen por su ID con el contenido resuelto para el idioma indicado.
     */
    public function getImageById($id_image, $id_lang = null)
    {
        $images = $this->getImagesWithFallback(false, $id_lang, (int)$id_image);

        return !empty($images) ? $images[0] : false;
    }

    /**
     * Núcleo de la resolución de imágenes: obtiene las filas base y, para
     * cada una, decide qué imagen/texto mostrar para el idioma solicitado.
     */
    protected function getImagesWithFallback($only_active, $id_lang = null, $only_id_image = null)
    {
        $id_lang = $id_lang !== null ? (int)$id_lang : (int)Context::getContext()->language->id;
        $default_id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $query = new DbQuery();
        $query->select('id_image, url, position, active, date_add, date_upd');
        $query->from('sliderresponsivo_imagen');
        if ($only_active) {
            $query->where('active = 1');
        }
        if ($only_id_image !== null) {
            $query->where('id_image = '.(int)$only_id_image);
        }
        $query->orderBy('position ASC');

        $images = Db::getInstance()->executeS($query);
        if (empty($images)) {
            return [];
        }

        $ids = [];
        foreach ($images as $image) {
            $ids[] = (int)$image['id_image'];
        }

        $lang_query = new DbQuery();
        $lang_query->select('id_image, id_lang, title, description, alt, desktop_image, mobile_image');
        $lang_query->from('sliderresponsivo_imagen_lang');
        $lang_query->where('id_image IN ('.implode(',', $ids).')');
        $lang_rows = Db::getInstance()->executeS($lang_query);

        $rows_by_image = [];
        foreach ($lang_rows as $row) {
            $rows_by_image[(int)$row['id_image']][(int)$row['id_lang']] = $row;
        }

        $result = [];
        foreach ($images as $image) {
            $id_image = (int)$image['id_image'];
            $rows = isset($rows_by_image[$id_image]) ? $rows_by_image[$id_image] : [];

            $content = $this->pickLanguageContent($rows, $id_lang, $default_id_lang);
            if ($content === null) {
                // Ningún idioma tiene imagen para esta entrada: no se puede mostrar.
                continue;
            }

            $result[] = array_merge($image, [
                'title' => $content['title'],
                'description' => $content['description'],
                'alt' => $content['alt'],
                'desktop_image' => $content['desktop_image'],
                'mobile_image' => $content['mobile_image'],
            ]);
        }

        return $result;
    }

    /**
     * De entre las filas de idioma de una imagen, elige cuál usar:
     * 1. La del idioma solicitado, si tiene imagen propia.
     * 2. Si no, la del idioma por defecto de la tienda, si tiene imagen.
     * 3. Si no, la primera fila (cualquier idioma) que tenga imagen.
     * Devuelve null si ningún idioma tiene imagen.
     */
    protected function pickLanguageContent(array $rows, $id_lang, $default_id_lang)
    {
        if (isset($rows[$id_lang]) && $this->rowHasImage($rows[$id_lang])) {
            return $rows[$id_lang];
        }

        if (isset($rows[$default_id_lang]) && $this->rowHasImage($rows[$default_id_lang])) {
            return $rows[$default_id_lang];
        }

        foreach ($rows as $row) {
            if ($this->rowHasImage($row)) {
                return $row;
            }
        }

        return null;
    }

    protected function rowHasImage($row)
    {
        return !empty($row['desktop_image']) && !empty($row['mobile_image']);
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
