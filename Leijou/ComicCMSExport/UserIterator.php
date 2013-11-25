<?php
namespace Leijou\ComicCMSExport;

class UserIterator extends PrivateFileIterator
{
    protected $storagepath = '/private/users.php';

    public function constructFromLine($id, $line)
    {
        $user = new User;
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
