<?php
namespace Leijou\ComicCMSExport;

class UserIterator extends SplitFileIterator
{
    protected $storagepath = '/private/users.php';

    public function constructFromLine($id, $line)
    {
        $user = new User;
        $user->id = $id;

        // TODO
        return $user;
    }
}
