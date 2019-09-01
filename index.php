<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use DiDom\Document;
use League\Csv\Writer;

$arrayTitles = ['Title'];
$arrayParams = ['Parameters'];
$arrayPrices = ['Price'];
//$arrayColumns = ['Title', 'Parameters', 'Price'];

for ($i = 1; $i <= 15; $i++) {
    $client = New Client();
    $response = $client->get('https://exist.ru/Catalog/Goods/5/66#&&p={$i}&F=');

    $resBody = null;

    if ($response->getStatusCode() == 200) {
        $resBody = (string)$response->getBody();
    } else {
        echo "Некорректный ответ сервера. Код статуса" . $response->getStatusCode();
    }

    $document = new Document($resBody);

    
    $titles = $document->find('div.wrap p');
    foreach ($titles as  $title) {
        $arrayTitles[] = strip_tags($title);
    }
    
    $params = $document->find('div.desc span');
    foreach ($params as  $param) {
        $arrayParams[] = strip_tags($param);
    }

    $prices = $document->find('div.ucatprcdiv span.ucatprc');
    foreach ($prices as $price) {
        $arrayPrices[] = strip_tags($price);
    }
    print_r($i);
}

for ($i = 0; $i < 15; $i++) {
    $arrayForCsv[] = [$arrayTitles[$i], $arrayParams[$i], $arrayPrices[$i]];
  }


$writer = Writer::createFromPath('file.csv', 'w+');
$writer->setDelimiter(';');
$writer->insertAll($arrayForCsv);