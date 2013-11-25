<?php
namespace Leijou\ComicCMSExport;

class UserGroupIterator extends PrivateFileIterator
{
    protected $storagepath = '/private/usergroups.php';

    public function constructFromLine($id, $line)
    {
        $usergroup = new UserGroup;
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
