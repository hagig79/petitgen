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

$frontMatter = new \Webuni\FrontMatter\FrontMatter();

foreach ($file_list as $file_path) {
    $my_text = file_get_contents($file_path);

    if (mb_detect_encoding($my_text, ['UTF-8', 'CP932']) === 'CP932') {
        $my_text = mb_convert_encoding($my_text, 'UTF-8', 'CP932');
    }
    $doc = $frontMatter->parse($my_text);

    $meta = $doc->getData();
    $title = isset($meta['title']) ? $meta['title'] : '';

    $my_html = Markdown::defaultTransform($doc->getContent());
    $body = str_replace('{{Content}}', $my_html, $templete);
    $body = str_replace('{{Title}}', $title, $body);
    file_put_contents($output_directory . DIRECTORY_SEPARATOR . basename($file_path, '.md') . '.html', $body);
}
