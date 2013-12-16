<?php
namespace Leijou\ComicCMSExport;

class News extends Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var \DateTime Time when this News post was added to the site
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
