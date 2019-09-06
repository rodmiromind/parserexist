<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;
use DiDom\Document;
use DiDom\ClassAttribute;
use League\Csv\Writer;

$arrayBrands = [];
$arrayTitles = ['Title'];
$arrayParams = ['Patron'];
$arrayPrices = ['Price'];

$client = New Client();
$response = $client->get('https://exist.ru/Catalog/Goods/5/66/B7F0104A');

$body = (string)$response->getBody();
$document = new Document($body);

