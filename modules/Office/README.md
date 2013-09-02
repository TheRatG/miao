```php
$obj = new \Miao\Office\Factory( '\\Project\\FrontOffice' );
$office = $obj->getOffice();

$office->getResponse()
    ->setStatusCode(404, "Not Found");
    ->setContent( 'Sorry, the page doesn't exist' );
$office->sendResponse();
```