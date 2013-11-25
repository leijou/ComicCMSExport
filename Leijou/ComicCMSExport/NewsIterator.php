<?php
namespace Leijou\ComicCMSExport;

class NewsIterator extends SplitFileIterator
{
    protected $storagepath = '/news';
    protected $storageitems = 50;

    public function constructFromLine($id, $line)
    {
        $news = new News;
        $news->id = $id;

        $assoc = $this->comiccms->lineToData(
            $line,
            array(
                'timestamp'=>'int',
                'title'=>'string',
                'author'=>'int',
                'post'=>'string',
            )
        );

        foreach ($assoc as $key => $value) {
            $news->$key = $value;
        }

        return $news;
    }
}
