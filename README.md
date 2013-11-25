ComicCMS Exporter
=================
Classes to handle the export of ComicCMS data without the need to dive in to its source code.

Currently in development. TODO:
- Queued Comics
- Queued News
- Single-file, PHP 5.1 version

Requirements
------------
- PHP 5.3
- [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) compliant
  autoloader or [Composer](http://getcomposer.org/): `"leijou/comiccms-export": "dev-master"`
- A ComicCMS install to point to

Once finalized I intend to compile a distributable single-file version requiring only PHP 5.1 (will
not contain namespaces).

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

###Item types available
Click for available properties and methods:
- [Comic](Leijou/ComicCMSExport/Comic.php)
- [News](Leijou/ComicCMSExport/News.php)
- [User](Leijou/ComicCMSExport/User.php)
- [UserGroup](Leijou/ComicCMSExport/UserGroup.php)

Each are constructed by its own Iterator:
`ComicIterator`
`NewsIterator`
`UserIterator`
`UserGroupIterator`

Internals
---------
On construction of an iterator the relevent storage file(s) are scanned and a list of IDs used (not
deleted) are loaded. The full details can then be pulled one by one by looping over the iterator or
using standard [SeekableIterator](http://php.net/class.seekableiterator.php) methods.

**Note:** The `seek` method uses the offset of Item, not its ID. For such usage the method
`seekById` is available.
