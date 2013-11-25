<?php
namespace Leijou\ComicCMSExport;

class Comic extends Item
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var \DateTime Time when this Comic was added to the site
     * This may be in the future for queued comics, or may be null for drafts
     */
    public $timestamp;

    /**
     * @var string
     */
    public $title;

    /**
     * @var int Id of the User who owns this Comic
     */
    public $author;

    /**
     * @var string
     */
    public $ext;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var string
     */
    public $tagline;

    /**
     * @var string
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
