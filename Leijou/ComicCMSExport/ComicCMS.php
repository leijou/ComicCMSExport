<?php
namespace Leijou\ComicCMSExport;

class ComicCMS
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
            throw new \RuntimeException('ComicCMS directory does not exist: '.$this->basepath);
        }

        if (!is_dir($this->storagepath)) {
            throw new \RuntimeException('ComicCMS storage directory does not exist: '.$this->storagepath);
        }

        if (!is_file($this->storagepath.'/dbversion')) {
            throw new \RuntimeException('ComicCMS version information is missing');
        }

        if (((int) file_get_contents($this->storagepath.'/dbversion')) < 4) {
            throw new \RuntimeException('ComicCMS version is too old. Please upgrade ComicCMS before exporting');
        }
    }

    /**
     * Convert storage line to an associative array (legacy code)
     * @param  string $line
     * @param  array  $layout
     * @return array
     */
    public function lineToData($line, array $layout)
    {
        $line = explode('|', rtrim($line));

        $result = array();
        $index=0;
        foreach ($layout as $name => $type) {
            // Populate associative array
            if (isset($line[$index])) {
                $result[$name] = $line[$index];
            } else {
                $result[$name] = false;
            }

            // CSV to array
            if (strpos($result[$name], ',') !== false) {
                $result[$name] = explode(',', $result[$name]);
            }

            // Sanitize
            switch ($type) {
                case 'bool':
                    $result[$name] = ($result[$name]?true:false);
                    break;
                case 'int':
                    if ($result[$name] !== '') {
                        $result[$name] *= 1;
                    }
                    break;
                case 'storedarray':
                    $result[$name] = (rtrim($line)?$this->destorify(unserialize(rtrim($result[$name]))):array());
                    // Also carry on to ensure array and destorify
                case 'array':
                    if (!is_array($result[$name])) {
                        $result[$name] = ($result[$name]?array($result[$name]):array());
                    }
                    // Also carry on to default to destorify
                default:
                    $result[$name] = $this->destorify($result[$name]);
                    break;
            }

            $index++;
        }

        return $result;
    }

    /**
     * Convert stored element in to plaintext (legacy code)
     * @param  mixed $inp
     * @return mixed
     */
    public function destorify($inp)
    {
        if (is_array($inp)) {
            foreach ($inp as $key => $value) {
                $inp[$key] = $this->destorify($value);
            }

            return $inp;
        }
        $change = array('&d'=>'.','&c'=>',','&p'=>'|','&b'=>"\n",'&a'=>'&');
        foreach ($change as $from => $to) {
            $inp = str_replace($from, $to, $inp);
        }

        return $inp;
    }
}
