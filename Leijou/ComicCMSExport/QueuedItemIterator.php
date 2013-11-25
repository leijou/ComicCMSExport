<?php
namespace Leijou\ComicCMSExport;

abstract class QueuedItemIterator extends SplitFileIterator
{
    protected $storagepath = '/tmp/cron';
    protected $storageitems = 20;

    protected $queueditemtype;

    protected function populateContents()
    {
        $id = 1;
        try {
            $this->loadItemFile($id);
            while (!$this->file->eof()) {
                $line = $this->file->fgets();
                if (strpos($line, '|') !== false) {
                    $l = explode('|', $line);
                    if ($l[1] == $this->queueditemtype) {
                        $this->contents[] = $id;
                    }
                }

                $id++;
                if ($id > $this->fileend) {
                    $this->loadItemFile($id);
                }
            }
        } catch (\OutOfBoundsException $e) {

        }
    }

    public function constructFromLine($id, $line)
    {
        $assoc = $this->comiccms->lineToData(
            $line,
            array(
                'timestamp'=>'int',
                'type'=>'string',
                'function'=>'string',
                'data'=>'storedarray',
                'files'=>'storedarray',
            )
        );

        if ($assoc['type'] == 'comic') {
            $item = new Comic;

            $img = $assoc['files']['comicimg'];
            $item->ext = substr($img['name'], (strrpos($img['name'], '.') + 1));
            $item->filepath = str_replace('tmp:///', $this->comiccms->storagepath.'/tmp/files/', $img['tmp_name']);
        } elseif ($assoc['type'] == 'news') {
            $item = new News;
        } else {
            return new Item;
        }

        foreach ($assoc['data'] as $key => $value) {
            $item->$key = $value;
        }

        $item->id = null;
        if ($item->timestamp == 9999999999) {
            $item->timestamp = null;
        } else {
            $item->timestamp = \DateTime::createFromFormat('U', $item->timestamp);
        }

        return $item;
    }
}
