<?php
namespace Leijou\ComicCMSExport;

abstract class ItemIterator implements \SeekableIterator
{
    protected $env;
    protected $position = 0;
    protected $contents = array();

    public function __construct(Environment $env)
    {
        $this->env = $env;
        $this->env->test();

        $this->populateContents();
    }

    /**
     * Populate the $contents array with all the non-deleted IDs in use
     */
    abstract protected function populateContents();

    /**
     * Return the storage line for a given ID
     * @return string
     */
    abstract protected function storageLine($id);

    /**
     * Return the Item given its storage line
     * @return Item
     */
    abstract protected function constructFromLine($id, $line);

    /**
     * Search the iterator using the Item's ID instead of position in list
     * @param mixed $id ID of the Item to find
     */
    public function seekById($id)
    {
        $pos = array_search($id, $this->contents);

        if ($pos === false) {
            $pos = -1;
        }

        $this->seek($pos);
    }

    /**
     * Return the Item at the current position
     * @return Item
     */
    public function current()
    {
        if (!$this->valid()) {
            throw new \OutOfBoundsException('Invalid position ('.$this->position.')');
        }

        $id = $this->contents[$this->position];
        $line = $this->storageLine($id);

        return $this->constructFromLine($id, $line);
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        $this->position++;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function valid()
    {
        return array_key_exists($this->position, $this->contents);
    }

    public function seek($position)
    {
        $this->position = $position;
    }
}
