<?php
namespace Leijou\ComicCMSExport;

abstract class SplitFileIterator extends ItemIterator
{
    protected $storagepath;
    protected $storageitems;

    protected $file;
    protected $filestart = -1;
    protected $fileend = -1;

    protected function loadItemFile($id)
    {
        $min = (floor($id / $this->storageitems) * $this->storageitems) + 1;
        $max = $min + $this->storageitems - 1;

        $this->filestart = $min;
        $this->fileend = $max;

        $filename = $this->env->storagepath.$this->storagepath.'/'.$min.'-'.$max;
        if (!file_exists($filename)) {
            throw new \OutOfBoundsException('Unknown item ID ('.$this->position.')');
        }

        $this->file = new \SplFileObject($filename, 'r');
    }

    protected function populateContents()
    {
        $id = 1;
        try {
            $this->loadItemFile($id);
            while (!$this->file->eof()) {
                $line = $this->file->fgets();
                if (strpos($line, '|') !== false) {
                    $this->contents[] = $id;
                }

                $id++;
                if ($id > $this->fileend) {
                    $this->loadItemFile($id);
                }
            }
        } catch (\OutOfBoundsException $e) {

        }
    }

    public function storageLine($id)
    {
        if (($id < $this->filestart) || ($id > $this->fileend)) {
            $this->loadItemFile($id);
        }

        $fileline = $id - $this->filestart;
        $this->file->seek($fileline);

        $line = $this->file->current();
        if (strpos($line, '|') === false) {
            throw new \RuntimeException('Can not find item ID ('.$id.')');
        }

        return $line;
    }
}
