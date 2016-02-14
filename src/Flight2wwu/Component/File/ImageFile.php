<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/22
 * Time: 14:45
 */

namespace Flight2wwu\Component\File;

use Flight2wwu\Component\Utils\FormatUtils;

class ImageFile extends BinaryFile
{

    /**
     * @return void
     */
    public function printContent()
    {
        self::printHeader();
        echo $this->content;
    }

    /**
     * @param string $path
     */
    function __construct($path = '')
    {
        if ($path) {
            $this->ext = FormatUtils::formatExtension(strtolower(pathinfo($path, PATHINFO_EXTENSION)));
            $this->mime = self::getMimeFromExtension($this->ext);
            $this->path = $path;
            $this->content = file_get_contents($path);
        }
    }

    /**
     * @param string $content
     * @param string $extension
     * @return ImageFile
     */
    public static function createFromString($content, $extension)
    {
        $img = new ImageFile();
        $img->content = (string)$content;
        $img->ext = FormatUtils::formatExtension($extension);
        $img->mime = self::getMimeFromExtension($img->getExtension());
        return $img;
    }

    /**
     * @param string $mime
     * @return bool
     */
    public static function isImage($mime)
    {
        return in_array($mime, ['image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'image/tiff']);
    }
} 