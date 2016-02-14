<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/12/22
 * Time: 16:26
 */

namespace Flight2wwu\Component\File;

use Flight2wwu\Component\Utils\FormatUtils;

abstract class CommonFile
{

    /**
     * @param string $path
     * @return void
     */
    abstract public function writeTo($path);

    /**
     * @var string
     */
    protected $mime = '';

    /**
     * @var string
     */
    protected $ext = '';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->ext;
    }

    /**
     * @param string $ext
     * @return string
     */
    public static function getMimeFromExtension($ext)
    {
        $ext = FormatUtils::formatExtension($ext);
        switch ($ext) {
            case ".pdf": $ctype = "application/pdf"; break;
            case ".exe": $ctype = "application/octet-stream"; break;
            case ".zip": $ctype = "application/zip"; break;
            case ".doc": $ctype = "application/msword"; break;
            case ".docx": $ctype = "application/vnd.openxmlformats-officedocument.wordprocessingml.document"; break;
            case ".xls": $ctype = "application/vnd.ms-excel"; break;
            case ".xlsx": $ctype = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"; break;
            case ".ppt": $ctype = "application/vnd.ms-powerpoint"; break;
            case ".pptx": $ctype = "application/vnd.openxmlformats-officedocument.presentationml.presentation"; break;
            case ".gif": $ctype = "image/gif"; break;
            case ".png": $ctype = "image/png"; break;
            case ".jpeg":
            case ".jpg": $ctype = "image/jpg"; break;
            case ".tif":
            case ".tiff": $ctype = "image/tiff"; break;
            case ".txt":
            case ".csv":
            case ".tsv": $ctype = "text/plain"; break;
            case ".xml": $ctype = "text/xml"; break;
            case ".html": $ctype = "text/html"; break;
            default: $ctype = "application/force-download";
        }
        return $ctype;
    }

    /**
     * @param string $mime
     * @return string
     */
    public static function getExtensionFromMime($mime)
    {
        switch ($mime) {
            case 'application/pdf': $ext = '.pdf'; break;
            case 'application/zip': $ext = '.zip'; break;
            case 'application/msword': $ext = '.doc'; break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document': $ext = '.docx'; break;
            case 'application/vnd.ms-excel': $ext = '.xls'; break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': $ext = '.xlsx'; break;
            case 'application/vnd.ms-powerpoint': $ext = '.ppt'; break;
            case 'application/vnd.openxmlformats-officedocument.presentationml.presentation': $ext = '.pptx'; break;
            case 'image/gif': $ext = '.gif'; break;
            case 'image/png': $ext = '.png'; break;
            case 'image/jpg': $ext = '.jpg'; break;
            case 'image/tiff': $ext = '.tiff'; break;
            case 'text/xml': $ext = '.xml'; break;
            case 'text/html': $ext = '.html'; break;
            default: $ext = '';
        }
        return $ext;
    }

    /**
     * @param int $fsize
     */
    protected function printHeader($fsize = 0)
    {
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: " . $this->getMime());
        header("Content-Transfer-Encoding: binary");
        if ($fsize) {
            header("Content-Length: " . $fsize);
        }
    }
} 