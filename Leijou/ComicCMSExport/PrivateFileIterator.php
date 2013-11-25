<?php
namespace Leijou\ComicCMSExport;

abstract class PrivateFileIterator extends ItemIterator
{
    protected $storagepath;

    protected $file;

    protected function populateContents()
    {
        $this->file = new \SplFileObject($this->comiccms->storagepath.$this->storagepath, 'r');

        // Private files start on line 1
        $id = 0;
        while (!$this->file->eof()) {
            $line = $this->file->fgets();
            if (strpos($line, '|') !== false) {
                $this->contents[] = $id;
            }

            $id++;
        }
    }

    public function storageLine($id)
    {
        $this->file->seek($id);

        $line = $this->file->current();
        if (strpos($line, '|') === false) {
            throw new \RuntimeException('Can not find item ID ('.$id.')');
        }

        return $line;
    }
}
