<?php
require dirname(__FILE__) . "/../vendor/autoload.php";

use CodeService\CodeService;

$service = new CodeService;
$ast = $service->newAst();

var_dump($ast->toString());
