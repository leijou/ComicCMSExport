<?php
namespace Leijou\ComicCMSExport;

class ComicIterator extends SplitFileIterator
{
    protected $storagepath = '/comics';
    protected $storageitems = 100;

    public function constructFromLine($id, $line)
    {
        $comic = new Comic;
        $comic->id = $id;

        // TODO

        $comic->filepath = $this->env->comicimgpath.'/'.$comic->id.'.'.$comic->ext;

        return $comic;
    }
}
