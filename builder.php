<?php

$comment = readline('Commit comment: ');

$json = file_get_contents(__DIR__ . '/composer.json');
$data = json_decode($json, true);
$version = explode('.', $data['version'] ?? '1.0.0.0');

$version = incVersion($version, count($version) - 1);

function incVersion($version, $position = 0)
{
    $version[$position] += 1;
    if (($version[$position] > 99) && ($position > 0)) {
        $version[$position] = 0;
        $version = incVersion($version, $position - 1);
    }
    return $version;
}

$version = implode('.', $version);
$message = ($comment) ? $comment : 'Patch v. ' . $version;
$data['version'] = $version;
file_put_contents(__DIR__ . '/composer.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

system('git add .');
system('git commit -m "' . $message . '"');
system('git push origin master ' . $version);

