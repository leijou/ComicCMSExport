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
