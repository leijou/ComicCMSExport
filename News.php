<?php
namespace Leijou\ComicCMSExport;

class News extends Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int Unix timestamp of when this News post was added to the site
     */
    public $timestamp;

    /**
     * @var string
     */
    public $title;

    /**
     * @var int Id of the User who owns this News post
     */
    public $author;

    /**
     * @var string
     */
    public $post;
}
