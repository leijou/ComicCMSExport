<?php
namespace Leijou\ComicCMSExport;

class User extends Item
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
