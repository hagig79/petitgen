<?php
require_once 'vendor/autoload.php';

use Michelf\Markdown;

function getFileList($path)
{
    $results = [];
    foreach(glob($path . '/{*.md}', GLOB_BRACE) as $file) {
        if(is_file($file)) {
            $results[] = $file;
        }
    }
    return $results;
}

$file_list = getFileList("contents");

$output_directory = 'public';

if (!file_exists($output_directory)) {
    mkdir($output_directory);
}

$templete = file_get_contents('templates/page.html');

foreach ($file_list as $file_path) {
    $my_text = file_get_contents($file_path);
    $my_html = Markdown::defaultTransform($my_text);
    $body = str_replace('{{ .Content }}', $my_html, $templete);
    file_put_contents($output_directory . DIRECTORY_SEPARATOR . basename($file_path, '.md') . '.html', $body);
}
