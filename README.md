ComicCMS Exporter
=================
Classes to handle the export of ComicCMS data without the need to dive in to its source code.

Requirements
------------
- PHP 5.2
- A ComicCMS install to point to

Quickstart
----------
- Download [comiccmsexport.php](examples/comiccmsexport.php) to include in your project
- Copy the [contents.php](examples/contents.php) example script and hack it to your needs

**Note:** This is a non-namespaced, single-file version of the code in this repo. The only
external difference between the two versions is the conversion from namespace:
Classes are called `ComicCMSExport_*` instead of `\Leijou\ComicCMSExport\*`

Namespaced Requirements
-----------------------
If you're lucky enough to have PHP 5.3 on the target servers of your project you can instead
choose to use the namespaced version:
- PHP 5.3
- [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) compliant
  autoloader or [Composer](http://getcomposer.org/):
  [`"leijou/comiccms-export": "dev-master"`](https://packagist.org/packages/leijou/comiccms-export)
- A ComicCMS install to point to

The example contents script is also available for this namespaced version:
[contents_namespaced.php](examples/contents_namespaced.php)

Usage
-----
A `ComicCMS` object must be constructed and passed to the other classes. Once created it can
be re-used for all iterators.

```php
$comiccms = new \Leijou\ComicCMSExport\ComicCMS('/absolute/path/to/comiccms');

$comics = new \Leijou\ComicCMSExport\ComicIterator($comiccms);
foreach ($comics as $comic) {
    // ...
}
```

##Item types available
Click for available property and method documentation:
- [Comic](Leijou/ComicCMSExport/Comic.php)
- [News](Leijou/ComicCMSExport/News.php)
- [User](Leijou/ComicCMSExport/User.php)
- [UserGroup](Leijou/ComicCMSExport/UserGroup.php)

Each are constructed by its own Iterator:
`ComicIterator`
`NewsIterator`
`UserIterator`
`UserGroupIterator`

Additionaly there are two iterators for queued/draft items:
`QueuedComicIterator`
`QueuedNewsIterator`

Internals
---------
On construction of an iterator the relevent storage file(s) are scanned and a list of IDs used (not
deleted) are loaded. The full details can then be pulled one by one by looping over the iterator or
using standard [SeekableIterator](http://php.net/class.seekableiterator.php) methods.

**Note:** The `seek` method uses the offset of Item, not its ID. For such usage the method
`seekById` is available.

**Note:** Generation of the the non-namespaced distributable version is done by build.php. It's
just basic RegExp, not anything clever. So please be gentle & careful with it if you're aiming to
edit and re-build this tool.
