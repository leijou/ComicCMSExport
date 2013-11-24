<?php
namespace Leijou\ComicCMSExport;

class UserGroupIterator extends SplitFileIterator
{
    protected $storagepath = '/private/usergroups.php';

    public function constructFromLine($id, $line)
    {
        $usergroup = new UserGroup;
        $usergroup->id = $id;

        // TODO
        return $usergroup;
    }
}
