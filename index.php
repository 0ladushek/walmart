<?php
require_once __DIR__ . '/vendor/autoload.php';

use Curl\Curl;
use Curl\MultiCurl;

$zipCode = 10001;
$productUrl = 'https://www.walmart.com/terra-firma/item/51630300/';

if(isset($zipCode)) {
    $productUrl .= 'location/10001';
}


// получаем список вариантов товара
$curl = new Curl();
$curl->setOpt(CURLOPT_FOLLOWLOCATION, true);

$cookiefile = __DIR__ . '/tmp/cookies.txt';
$curl->setCookieJar($cookiefile);
$curl->setCookieFile($cookiefile);

$curl->setUserAgent('Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0');

$curl->setopt(CURLOPT_SSL_VERIFYHOST,false);
$curl->setopt(CURLOPT_SSL_VERIFYPEER, false);

$curl->get($productUrl);

if ($curl->error) {
    echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
} else {
   $payload  = $curl->response->payload;
}

$products = $payload->products;
$productsId = [];

foreach ($products as $val) {
    $productsId[] = $val->productId;
}



// Добавляем информацию об оферах
$multi_curl = new MultiCurl();

foreach ($productsId as $val) {
    // формируем запросы для получение оферов для каждого товара
    $request = 'https://www.walmart.com/terra-firma/item/' . $val . '/location/' . $zipCode;
    $multi_curl->addGet($request);
}

$multi_curl->success(function($instance) use (&$products){
    // добовляем полученные оферы в товар
    $productsId = explode('/', $instance->url)[5];
    $offerId = $products->$productsId->offers[0];
    $products->$productsId->offers = $instance->response->payload->offers->$offerId;

});
$multi_curl->error(function($instance) {
    echo 'call to "' . $instance->url . '" was unsuccessful.' . "\n";
    echo 'error code: ' . $instance->errorCode . "\n";
    echo 'error message: ' . $instance->errorMessage . "\n";
});
$multi_curl->start();




// выводим данные в таблицу
include __DIR__ . '/table.php';