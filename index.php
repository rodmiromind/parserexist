<?php
require 'vendor/autoload.php';
use GuzzleHttp\Client;

$client = New Client();
$response = $client->post('https://exist.ru/Catalog/Goods/5/66');
$resBody = (string)$response->getBody();
echo $resBody;