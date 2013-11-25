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

    public function test()
    {
        if (!is_dir($this->basepath)) {
            throw new \RuntimeError('ComicCMS directory does not exist: '.$this->basepath);
        }

        if (!is_dir($this->storagepath)) {
            throw new \RuntimeError('ComicCMS storage directory does not exist: '.$this->storagepath);
        }

        if (!is_file($this->storagepath.'/dbversion')) {
            throw new \RuntimeError('ComicCMS version information is missing');
        }

        if (((int) file_get_contents($this->storagepath.'/dbversion')) < 4) {
            throw new \RuntimeError('ComicCMS version is too old. Please upgrade ComicCMS before exporting');
        }
    }
}
