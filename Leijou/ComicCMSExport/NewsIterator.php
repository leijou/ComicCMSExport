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

        // TODO
        return $news;
    }
}
