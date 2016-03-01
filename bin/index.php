<?php
require dirname(__FILE__) . "/../vendor/autoload.php";

use CodeService\CodeService;

$service = new CodeService;
$code = $service->createGenerator();
$code->getClass()->extend('AbstractService');
$code->getClass()->appendImplement('ServiceManagerAwareInterface');
$code->getClass()->appendConst('Test', false);
$code->getClass()->appendProperty('serviceManager');
$code->getClass()->appendMethod('index', 'public');
$code->getClass()->getMethod('index')->setReturn("ViewModelManager::getViewModel([ 'viewModel' => PageViewModel::class ]);");
$code->getClass()->getMethod('index')->appendProcess('$Model = new Test;');
$code->getClass()->getMethod('index')->appendParam('$dir = "test"');
$param = $code->getClass()->getMethod('index')->getParam('$dir');
//print($param->getDefault());die;
$param->setDefault([]);
$code->getClass()->getTrait('Framework\Event\Event\EventTargetTrait');
print($code->toString());