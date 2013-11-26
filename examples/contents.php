<?php
require_once(__DIR__.'/../vendor/autoload.php');

// First argument should be path to ComicCMS
if (empty($argv[1])) {
    $argv[1] = '.';
}

echo '-------------------'."\n";
echo '  ComicCMS Export  '."\n";
echo '-------------------'."\n\n";

try {
    $comiccms = new \Leijou\ComicCMSExport\ComicCMS($argv[1]);
    $comiccms->test();
} catch (\Exception $e) {
    echo 'Failed to read ComicCMS directory'."\n";
    echo $e->getMessage()."\n\n";
    echo 'Usage: php '.$argv[0].' <path_to_comiccms>'."\n\n";
    exit;
}

$comics = new \Leijou\ComicCMSExport\ComicIterator($comiccms);
echo 'Comics ('.count($comics).')'."\n";
echo '-------------------'."\n";
foreach ($comics as $comic) {
    echo $comic->timestamp->format('Y-m-d H:i:s') . "\t" . $comic->id . "\t" . $comic->title . "\n";
}
echo "\n";

$queuedcomics = new \Leijou\ComicCMSExport\QueuedComicIterator($comiccms);
echo 'Queued Comics ('.count($queuedcomics).')'."\n";
echo '-------------------'."\n";
foreach ($queuedcomics as $comic) {
    echo ($comic->timestamp?$comic->timestamp->format('Y-m-d H:i:s'):'                   ') . "\t*\t" . $comic->title . "\n";
}
echo "\n";

$news = new \Leijou\ComicCMSExport\NewsIterator($comiccms);
echo 'News ('.count($news).')'."\n";
echo '-------------------'."\n";
foreach ($news as $post) {
    echo $post->timestamp->format('Y-m-d H:i:s') . "\t" . $post->id . "\t" . $post->title . "\n";
}
echo "\n";

$queuednews = new \Leijou\ComicCMSExport\QueuedNewsIterator($comiccms);
echo 'Queued News ('.count($queuednews).')'."\n";
echo '-------------------'."\n";
foreach ($queuednews as $post) {
    echo ($post->timestamp?$post->timestamp->format('Y-m-d H:i:s'):'                   ') . "\t*\t" . $post->title . "\n";
}
echo "\n";

$users = new \Leijou\ComicCMSExport\UserIterator($comiccms);
echo 'Users ('.count($users).')'."\n";
echo '-------------------'."\n";
foreach ($users as $user) {
    echo $user->id . "\t" . $user->name . "\n";
}
echo "\n";

$usergroups = new \Leijou\ComicCMSExport\UserGroupIterator($comiccms);
echo 'User Groups ('.count($usergroups).')'."\n";
echo '-------------------'."\n";
foreach ($usergroups as $usergroup) {
    echo $usergroup->id . "\t" . $usergroup->name . "\n";
}
echo "\n";
