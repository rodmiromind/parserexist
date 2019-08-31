<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use DiDom\Document;

$client = New Client();

$response = $client->get('https://exist.ru/Catalog/Goods/5/66');

$resBody = null;

if ($response->getStatusCode() == 200) {
    $resBody = (string)$response->getBody();
} else {
    echo "Некорректный код статуса" . $response->getStatusCode();
}

$document = new Document($resBody);
$titles = $document->find('div.wrap p');
foreach ($titles as $title) {
    echo $title . PHP_EOL;
}
