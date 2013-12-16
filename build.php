<?php
/**
 * This is a rough file to strip namespaces and compact classes in to a PHP 5.2
 * compatible file. It is not generic or particularly robust but should serve
 * its purpose.
 */
$libpath = __DIR__.'/Leijou/ComicCMSExport';

$outfile = new SplFileObject(__DIR__.'/examples/comiccmsexport.php', 'w');

$outfile->fwrite(
    '<?php
/**
 * This is the ComicCMS data export tool (distributable, PHP 5.2 version)
 *
 * For information on usage and structured, more readable, source code visit
 * https://github.com/leijou/ComicCMSExport
 */
'
);

$ordered = array(
    'ItemIterator.php',
    'SplitFileIterator.php',
    'QueuedItemIterator.php',
);
foreach ($ordered as $filename) {
    $file = new SplFileObject($libpath.'/'.$filename, 'r');
    buildFile($file, $outfile);
}

$dir = new DirectoryIterator($libpath);
foreach ($dir as $item) {
    if ($item->isFile() && !in_array($item->getFilename(), $ordered)) {
        $file = new SplFileObject($item->getPathname(), 'r');
        buildFile($file, $outfile);
    }
}

function buildFile(SplFileObject $infile, SplFileObject $outfile)
{
    $patterns = array(
        // Strip namespace
        '/<\?(php)?/' => '',
        '/namespace.*$/' => '',

        // Replace namespace with prefix
        '/(class|extends) ([0-9a-zA-Z\-\_]+)/' => '$1 ComicCMSExport_$2',
        '/new ([0-9a-zA-Z\-\_]+)/' => 'new ComicCMSExport_$1',
        '/(^\\\\)([0-9a-zA-Z\-\_]+::)/' => '$1ComicCMSExport_$2',

        // Remove root namespace references
        '/new \\\\([0-9a-zA-Z\-\_]+)/' => 'new $1',
        '/\\\\([0-9a-zA-Z\-\_]+::)/' => '$1',
        '/\(\\\\([0-9a-zA-Z\-\_]+)/' => '($1',

        // Fix PHP 5.2 DateTime support (lacking ::createFromFormat)
        '/DateTime::createFromFormat\(\'U\', ([^\)]+)\);/' => 'new DateTime(date(\'Y-m-d H:i:s\', $1));',

        // Pick up a few missed bits (told you this script was rough!)
        '/\\\\(SeekableIterator|Countable|DateTime)/' => '$1',
        '/([^_])(ComicCMS)([ :])/' => '$1ComicCMSExport_$2$3',
    );

    foreach ($infile as $line) {
        foreach ($patterns as $pattern => $replacement) {
            $line = preg_replace($pattern, $replacement, $line);
        }

        $outfile->fwrite($line);
    }
}
