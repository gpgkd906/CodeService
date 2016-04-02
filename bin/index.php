<?php
require dirname(__FILE__) . "/../vendor/autoload.php";

use CodeService\CodeService;
use phpDocumentor\Reflection\DocBlockFactory;
    
function classDoc($class, $comment)
{

}

function propertyDoc($property, $comment)
{
    $summary = $comment->getSummary();
    /**
     * $property->getName()
     * $property->getAccess()
     * $property->getDefault()
     */
    if(!$summary) {
        $comment->setSummary(ucfirst($property->getName()));
    }
    $holders = [
        "var", "access", "since", "version"
    ];
    $holderFlips = array_flip($holders);
    $comment->tagWalk(function ($tag) use ($holderFlips, &$holders) {
        $name = $tag->getName();
        if(isset($holderFlips[$name])) {
            $idx = $holderFlips[$name];
            unset($holders[$idx]);
        }
    });
    array_map(function ($item) use ($property, $comment) {
        switch($item) {
        case "var":
            $comment->addTag($item, $property->getDefaultTypeInfer() . ' $' . $property->getName());
            break;
        case "access":
            $comment->addTag($item, $property->getAccess());
            break;
        case "since":
        case "version":
            $comment->addTag($item);
            break;
        }
    }, $holders);
    $comment->reload();
}

function methodDoc($method, $comment)
{

}

function makeComment($description, $tags)
{

}

$service = new CodeService;

$service->ls("/Users/gpgkd906/dev/framework/Framework", function($_, $file) use ($service) {
    if(pathinfo($file, PATHINFO_EXTENSION) !== "php") {
        return false;
    }
    $ast = $service->analysis($file);
    
    echo $file, PHP_EOL;
    if($ast->hasClass()) {
        $class = $ast->getClass();
        classDoc($class, $class->getComment());
        $class->propertyWalk(function($property) {
            propertyDoc($property, $property->getComment());
        });
        $class->methodWalk(function($method) {
            methodDoc($method, $method->getComment());
        });
        echo $ast->toCode();
        die;
    }
});

