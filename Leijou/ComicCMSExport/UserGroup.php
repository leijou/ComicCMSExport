<?php
namespace Leijou\ComicCMSExport;

class UserGroup extends Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
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
