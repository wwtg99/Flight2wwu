<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/22
 * Time: 16:48
 */

namespace Flight2wwu\Component\File;

use Flight2wwu\Component\Utils\FormatUtils;

class BinaryFile extends CommonFile implements Downloadable, Printable
{
    /**
     * @var string
     */
    protected $path = '';

    /**
     * @param string $path
     * @param string $content
     * @param string $ext
     * @param string $mime
     */
    function __construct($path = '', $content = '', $ext = '', $mime = '')
    {
        if ($path) {
            $this->ext = FormatUtils::formatExtension(strtolower(pathinfo($path, PATHINFO_EXTENSION)));
            $this->mime = self::getMimeFromExtension($this->ext);
            $this->path = $path;
        } elseif ($content) {
            if ($ext && $mime) {
                $this->ext = FormatUtils::formatExtension($ext);
                $this->mime = $mime;
            } elseif ($ext && !$mime) {
                $this->ext = FormatUtils::formatExtension($ext);
                $this->mime = self::getMimeFromExtension($this->ext);
            } elseif (!$ext && $mime) {
                $this->mime = $mime;
                $this->ext = self::getExtensionFromMime($this->mime);
            }
            $this->content = $content;
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public function writeTo($path)
    {
        file_put_contents($path, $this->content);
    }

    /**
     * @param string $filename
     * @return void
     */
    public function download($filename = '')
    {
        if (!$filename) {
            $filename = basename($this->path);
        } else {
            $filename .= $this->ext;
        }
        if (!$filename) {
            $filename = 'download' . $this->ext;
        }
        if ($this->path) {
            $fsize = filesize($this->path);
            $this->printHeader($fsize);
            header("Content-Disposition: attachment; filename=\"" . $filename . "\";" );
            ob_clean();
            flush();
            readfile($this->path);
        }elseif ($this->content) {
            $fsize = '';
            $this->printHeader($fsize);
            header("Content-Disposition: attachment; filename=\"" . $filename . "\";" );
            ob_clean();
            flush();
            echo $this->content;
        }
    }

    /**
     * @return void
     */
    public function printContent()
    {
        if ($this->path) {
            $fsize = filesize($this->path);
            $this->printHeader($fsize);
            ob_clean();
            flush();
            readfile($this->path);
        }elseif ($this->content) {
            $fsize = '';
            $this->printHeader($fsize);
            ob_clean();
            flush();
            echo $this->content;
        }
    }


} 