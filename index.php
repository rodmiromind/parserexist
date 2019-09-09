<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use DiDom\Document;
use DiDom\ClassAttribute;
use League\Csv\Writer;

$arrayBrands = [];
$arrayTitles = [];
$arrayPatrons = [];
$arrayPrices = [];
$arrayColumns = ['Brand', 'Title', 'Price'];

$categoryParsing = $_SERVER['argv'][1];
$numOfPages = intval($_SERVER['argv'][2] ?? 1);

for ($i = 1; $i <= $numOfPages; $i++) {

    // парсинг html с листинга товаров
    $client = New Client();
    $response = $client->get($categoryParsing . '#&&p={$i}&F=');

    $body = null;
    if ($response->getStatusCode() == 200) {
        $body = (string)$response->getBody();
    } else {
        echo "Некорректный ответ сервера. Код статуса" . $response->getStatusCode();
    }
    $document = new Document($body);

    // находим ссылки на каждую карточку
    $linksToCard = $document->find('div.cell2 a.catheader::attr(href)');

    // парсинг html с карточек товара
    foreach ($linksToCard as $link) {
        $cardClient = New Client();
        $httpsLink = 'https://exist.ru' . $link;
        $cardResponse = $cardClient->get($httpsLink);

        $bodyCard = null;
        if ($cardResponse->getStatusCode() == 200) {
            $bodyCard = (string)$cardResponse->getBody();
        } else {
            echo "Некорректный ответ сервера. Код статуса" . 
            $cardResponse->getStatusCode();
        }

        $documentCard = new Document($bodyCard);

        $cardParamsTable = $documentCard->find('div.ZeForm div span');
        $arrayPrices[] = $documentCard->find('div.pricerow price ucatprc'); //здесь лечить
        print_r($arrayPrices);
        foreach ($cardParamsTable as $param) {
            switch ($param->text()) {
            case 'Бренд':
                $arrayBrands[] = $param->parent()->nextSibling()->text();
                break;
            case 'Артикул':
                $arrayTitles[] = $param->parent()->nextSibling()->text();
                break;
            }
        }

    }
}


$arrayForCsv[] = $arrayColumns;
for ($i = 0, $lenArray = count($arrayBrands); $i < $lenArray; $i++) {
    $arrayForCsv[] = [$arrayBrands[$i], $arrayTitles[$i], $arrayPrices[$i]];
}


$writer = Writer::createFromPath('file.csv', 'w+');
$writer->setDelimiter(';');
$writer->insertAll($arrayForCsv);