<?php
namespace Leijou\ComicCMSExport;

class Environment
{
    public $basepath;
    public $storagepath;
    public $comicimgpath;

    public function __construct($basepath)
    {
        $this->basepath = $basepath;
        $this->storagepath = $basepath.'/storage';
        $this->comicimgpath = $basepath.'/img/comic';
    }
}
