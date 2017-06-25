<?php
require dirname(__FILE__) . "/../vendor/autoload.php";

use CodeService\CodeService;
use phpDocumentor\Reflection\DocBlockFactory;

$service = new CodeService;

$service->ls("/Users/chen/docker/chen_dev/application/Framework", function($_, $file) use ($service) {
    if(pathinfo($file, PATHINFO_EXTENSION) !== "php") {
        return false;
    }
    $ast = $service->analysis($file, 7);
    $ast->getNamespace()->appendUse('DateTime');
    echo $ast->toCode();
});
