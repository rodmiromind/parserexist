<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use DiDom\Document;
use DiDom\ClassAttribute;
use League\Csv\Writer;

$arrayBrand = ['Brand'];
$arrayTitles = ['Title'];
$arrayParams = ['Patron'];
$arrayPrices = ['Price'];
//$arrayColumns = ['Title', 'Parameters', 'Price'];

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

      $cardParamsTable = $documentCard->find('div.ZeForm');
      foreach ($cardParamsTable as $param) {
        Нужно выцеплять параметры из карточки товара
      }

      // $brands = $documentCard->find('div.wrap p');
      // foreach ($brands as  $brand) {
      //     $arrayBrands[] = strip_tags($brand);
      // }
      // $titles = $documentCard->find('div.wrap p');
      // foreach ($titles as  $title) {
      //     $arrayTitles[] = strip_tags($title);
      // }
      // $params = $documentCard->find('div.desc span');
      // foreach ($params as  $param) {
      //     $arrayParams[] = strip_tags($param);
      // }
      // $prices = $documentCard->find('div.ucatprcdiv span.ucatprc');
      // foreach ($prices as $price) {
      //     $arrayPrices[] = strip_tags($price);
      }
    }

for ($i = 0; $i < 15; $i++) {
    $arrayForCsv[] = [$arrayTitles[$i], $arrayParams[$i], $arrayPrices[$i]];
  }


$writer = Writer::createFromPath('file.csv', 'w+');
$writer->setDelimiter(';');
$writer->insertAll($arrayForCsv);