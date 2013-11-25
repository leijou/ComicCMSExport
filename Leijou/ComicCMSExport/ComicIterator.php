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

        $assoc = $this->comiccms->lineToData(
            $line,
            array(
                'timestamp'=>'int',
                'title'=>'string',
                'author'=>'int',
                'ext'=>'string',
                'width'=>'int',
                'height'=>'int',
                'tagline'=>'string',
                'blurb'=>'string',
                'news'=>'array',
                'tag'=>'array',
            )
        );

        foreach ($assoc as $key => $value) {
            $comic->$key = $value;
        }

        $comic->filepath = $this->comiccms->comicimgpath.'/'.$comic->id.'.'.$comic->ext;

        return $comic;
    }
}
