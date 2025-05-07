<?php

// This file shows the content of the data folder. Uncomment the next line if you like to disable it:
//exit();

function listDir($dir, $relative = '')
{
    $items = array_diff(scandir($dir), ['.', '..']);
    natcasesort($items);

    echo '<ul>';
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $itemRelative = $relative . '/' . $item;

        if (is_dir($path)) {
            echo '<li><strong>' . htmlspecialchars($item) . '/</strong>';
            listDir($path, $itemRelative);
            echo '</li>';
        } else {
            echo '<li><a href="' . htmlspecialchars($itemRelative) . '">' . htmlspecialchars($item) . '</a></li>';
        }
    }
    echo '</ul>';
}

// Startpunkt = aktueller Ordner
$current = getcwd();

echo <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Index of {$_SERVER['REQUEST_URI']}</title>
    <style>
        body { font-family: sans-serif; padding: 2em; background: #111; color: #eee; }
        a { color: #4FC3F7; text-decoration: none; }
        a:hover { text-decoration: underline; }
        ul { list-style-type: none; padding-left: 1em; }
        li { margin: .3em 0; }
        strong { color: #81C784; }
    </style>
</head>
<body>
    <h1>Index of {$_SERVER['REQUEST_URI']}</h1>
HTML;

listDir($current, '');

echo '</body></html>';
