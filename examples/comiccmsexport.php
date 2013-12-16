<?php
/**
 * This is the ComicCMS data export tool (distributable, PHP 5.2 version)
 *
 * Version 1.0.0
 *
 * For information on usage and structured, more readable, source code visit
 * https://github.com/leijou/ComicCMSExport
 */



abstract class ComicCMSExport_ItemIterator implements SeekableIterator, Countable
{
    protected $comiccms;
    protected $position = 0;
    protected $contents = array();

    public function __construct(ComicCMSExport_ComicCMS $comiccms)
    {
        $this->comiccms = $comiccms;
        $this->comiccms->test();

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
            throw new OutOfBoundsException('Invalid position ('.$this->position.')');
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

    public function count()
    {
        return count($this->contents);
    }
}



abstract class ComicCMSExport_SplitFileIterator extends ComicCMSExport_ItemIterator
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

        $filename = $this->comiccms->storagepath.$this->storagepath.'/'.$min.'-'.$max;
        if (!file_exists($filename)) {
            throw new OutOfBoundsException('Unknown item ID ('.$this->position.')');
        }

        $this->file = new SplFileObject($filename, 'r');
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
        } catch (OutOfBoundsException $e) {

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
            throw new RuntimeException('Can not find item ID ('.$id.')');
        }

        return $line;
    }
}



abstract class ComicCMSExport_QueuedItemIterator extends ComicCMSExport_SplitFileIterator
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
        } catch (OutOfBoundsException $e) {

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
            $item = new ComicCMSExport_Comic;

            $img = $assoc['files']['comicimg'];
            $item->ext = substr($img['name'], (strrpos($img['name'], '.') + 1));
            $item->filepath = str_replace('tmp:///', $this->comiccms->storagepath.'/tmp/files/', $img['tmp_name']);
        } elseif ($assoc['type'] == 'news') {
            $item = new ComicCMSExport_News;
        } else {
            return new ComicCMSExport_Item;
        }

        foreach ($assoc['data'] as $key => $value) {
            $item->$key = $value;
        }

        $item->id = null;
        if ($item->timestamp == 9999999999) {
            $item->timestamp = null;
        } else {
            $item->timestamp = new DateTime(date('Y-m-d H:i:s', $item->timestamp));
        }

        return $item;
    }
}



class ComicCMSExport_News extends ComicCMSExport_Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var DateTime Time when this News post was added to the site
     * This may be in the future for queued news, or may be null for drafts
     */
    public $timestamp;

    /**
     * @var string Title of the News post
     */
    public $title;

    /**
     * @var int Id of the User who owns this News post
     */
    public $author;

    /**
     * @var string Content of the News post
     */
    public $post;
}



class ComicCMSExport_Comic extends ComicCMSExport_Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var DateTime Time when this Comic was added to the site
     * This may be in the future for queued comics, or may be null for drafts
     */
    public $timestamp;

    /**
     * @var string Title of the comic
     */
    public $title;

    /**
     * @var int Id of the User who owns this Comic
     */
    public $author;

    /**
     * @var string File extension of the Comic's image file
     */
    public $ext;

    /**
     * @var int Width in pixels of the Comic's image
     */
    public $width;

    /**
     * @var int Height in pixels of the Comic's image
     */
    public $height;

    /**
     * @var string Short tagline of the comic
     */
    public $tagline;

    /**
     * @var string Long blurb of the comic
     */
    public $blurb;

    /**
     * @var array List of News post IDs related to this Comic
     */
    public $news;

    /**
     * @var array List of strings representing tags on the comic
     */
    public $tag;

    /**
     * @var string Full path to the Comic's related image file
     */
    public $filepath;
}



abstract class ComicCMSExport_PrivateFileIterator extends ComicCMSExport_ItemIterator
{
    protected $storagepath;

    protected $file;

    protected function populateContents()
    {
        $this->file = new SplFileObject($this->comiccms->storagepath.$this->storagepath, 'r');

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
            throw new RuntimeException('Can not find item ID ('.$id.')');
        }

        return $line;
    }
}



class ComicCMSExport_ComicCMS
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
            throw new RuntimeException('ComicCMSExport_ComicCMS directory does not exist: '.$this->basepath);
        }

        if (!is_dir($this->storagepath)) {
            throw new RuntimeException('ComicCMSExport_ComicCMS storage directory does not exist: '.$this->storagepath);
        }

        if (!is_file($this->storagepath.'/dbversion')) {
            throw new RuntimeException('ComicCMSExport_ComicCMS version information is missing');
        }

        if (((int) file_get_contents($this->storagepath.'/dbversion')) < 4) {
            throw new RuntimeException('ComicCMSExport_ComicCMS version is too old. Please upgrade ComicCMSExport_ComicCMS before exporting');
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
                    $result[$name] = (rtrim($result[$name])?$this->destorify(unserialize(rtrim($result[$name]))):array());
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



class ComicCMSExport_ComicIterator extends ComicCMSExport_SplitFileIterator
{
    protected $storagepath = '/comics';
    protected $storageitems = 100;

    public function constructFromLine($id, $line)
    {
        $comic = new ComicCMSExport_Comic;
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

        $comic->timestamp = new DateTime(date('Y-m-d H:i:s', $comic->timestamp));
        $comic->filepath = $this->comiccms->comicimgpath.'/'.$comic->id.'.'.$comic->ext;

        return $comic;
    }
}



class ComicCMSExport_NewsIterator extends ComicCMSExport_SplitFileIterator
{
    protected $storagepath = '/news';
    protected $storageitems = 50;

    public function constructFromLine($id, $line)
    {
        $news = new ComicCMSExport_News;
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

        $news->timestamp = new DateTime(date('Y-m-d H:i:s', $news->timestamp));

        return $news;
    }
}



class ComicCMSExport_UserGroupIterator extends ComicCMSExport_PrivateFileIterator
{
    protected $storagepath = '/private/usergroups.php';

    public function constructFromLine($id, $line)
    {
        $usergroup = new ComicCMSExport_UserGroup;
        $usergroup->id = $id;

        $assoc = $this->comiccms->lineToData(
            $line,
            array(
                'name'=>'string',
                'permissions'=>'string',
            )
        );

        $usergroup->name = $assoc['name'];
        $usergroup->permissions = array();

        $permissions = array(
            'panel','_superuser','_checkupdate',
            'comic','comicadd','comicedit','comiceditdata','comiceditimg','comicdelete','comicdraft:main','comicdraft:edit','comicdraft:delete',
            'comictags','comicaddcollection','comiceditcollection','comicdeletecollection',
            'news','newsadd','newsedit','newseditdata','newsdelete','newsdraft:main','newsdraft:edit','newsdraft:delete',
            'newsaddcomment','newseditcomment','newsdeletecomment',
            'plugin','pluginadd','pluginuse','pluginedit','plugindelete','pluginevent',
            'template','templateeditglobal','templateadd','templateedit','templatedelete',
            'templatepage:add','templatepage:edit','templatepage:delete',
            'config','configsetting','configupdate','configuninstall','configcache',
            'configuser:main','configuser:add','configuser:edit','configuser:delete',
            'configusergroup:main','configusergroup:add','configusergroup:edit','configusergroup:delete',
        );
        foreach ($permissions as $x => $permission) {
            if ($assoc['permissions'][$x] === '1') {
                $usergroup->permissions[] = $permission;
            }
        }

        return $usergroup;
    }
}



class ComicCMSExport_QueuedNewsIterator extends ComicCMSExport_QueuedItemIterator
{
    protected $queueditemtype = 'news';
}



class ComicCMSExport_UserGroup extends ComicCMSExport_Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string Name of the UserGroup
     */
    public $name;

    /**
     * @var array List of permissions granted to this UserGroup
     *
     * Possible permissions:
     *   'panel','_superuser','_checkupdate',
     *   'comic','comicadd','comicedit','comiceditdata','comiceditimg','comicdelete','comicdraft:main','comicdraft:edit','comicdraft:delete',
     *   'comictags','comicaddcollection','comiceditcollection','comicdeletecollection',
     *   'news','newsadd','newsedit','newseditdata','newsdelete','newsdraft:main','newsdraft:edit','newsdraft:delete',
     *   'newsaddcomment','newseditcomment','newsdeletecomment',
     *   'plugin','pluginadd','pluginuse','pluginedit','plugindelete','pluginevent',
     *   'template','templateeditglobal','templateadd','templateedit','templatedelete',
     *   'templatepage:add','templatepage:edit','templatepage:delete',
     *   'config','configsetting','configupdate','configuninstall','configcache',
     *   'configuser:main','configuser:add','configuser:edit','configuser:delete',
     *   'configusergroup:main','configusergroup:add','configusergroup:edit','configusergroup:delete',
     */
    public $permissions;
}



class ComicCMSExport_QueuedComicIterator extends ComicCMSExport_QueuedItemIterator
{
    protected $queueditemtype = 'comic';
}



class ComicCMSExport_Item
{
}



class ComicCMSExport_UserIterator extends ComicCMSExport_PrivateFileIterator
{
    protected $storagepath = '/private/users.php';

    public function constructFromLine($id, $line)
    {
        $user = new ComicCMSExport_User;
        $user->id = $id;

        $assoc = $this->comiccms->lineToData(
            $line,
            array(
                'session'=>'string',
                'ip'=>'string',
                'group'=>'int',
                'name'=>'string',
                'password'=>'string',
                'email'=>'string',
            )
        );

        foreach ($assoc as $key => $value) {
            $user->$key = $value;
        }

        return $user;
    }
}



class ComicCMSExport_User extends ComicCMSExport_Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int ID of the UserGroup this User belongs to
     */
    public $group;

    /**
     * @var string Username of the User
     */
    public $name;

    /**
     * This is an old and insecure method of storing passwords!
     * Instead PLEASE use the modern password_hash, password_verify, etc. functions
     * For backwards compatibility: https://github.com/ircmaxell/password_compat
     * @var string Simple hash of the user's password
     */
    public $password;

    /**
     * @var string Email address of the User (used for gravatar)
     */
    public $email;

    /**
     * Check whether supplied plaintext password matches the User
     * @return bool
     */
    public function verifyPassword($password)
    {
        return crypt($password, md5($password)) == $this->password;
    }
}
