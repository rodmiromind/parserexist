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
$arrayColumns = ['Brand', 'Title', 'Patron'];

for ($i = 1; $i <= 1; $i++) {

    // парсинг html с листинга товаров
    $client = New Client();
    $response = $client->get('https://exist.ru/Catalog/Goods/5/66#&&p={$i}&F=');

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
          echo "Некорректный ответ сервера. Код статуса" . $cardResponse->getStatusCode();
      }

      $documentCard = new Document($bodyCard);

      $cardParamsTable = $documentCard->find('div.ZeForm div span');

      foreach ($cardParamsTable as $param) {
        switch ($param->text()) {
            case 'Бренд':
                $arrayBrands[] = $param->parent()->nextSibling()->text();
                break;
            case 'Артикул':
                $arrayTitles[] = $param->parent()->nextSibling()->text();
                break;
            case 'Исполнение патрона':
                $arrayPatrons[] = $param->parent()->nextSibling()->text();
                break;
        }
      }

      }
    }


$arrayForCsv[] = $arrayColumns;
for ($i = 0; $i < 5; $i++) {
    $arrayForCsv[] = [$arrayBrands[$i], $arrayTitles[$i], $arrayPatrons[$i]];
}

print_r($arrayForCsv);


$writer = Writer::createFromPath('file.csv', 'w+');
$writer->setDelimiter(';');
$writer->insertAll($arrayForCsv);