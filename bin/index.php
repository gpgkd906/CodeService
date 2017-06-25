<?php
require dirname(__FILE__) . "/../vendor/autoload.php";

use CodeService\CodeService;
use phpDocumentor\Reflection\DocBlockFactory;

function fileDoc($file, $ast, $comment)
{
    $comment->setSummary([
        'File', null,
        '[:package description]', null,
    ]);
    $holders = [
        "copyright", "link", "since", "version", "license"
    ];
    $holderFlips = array_flip($holders);
    $comment->tagWalk(function ($tag) use ($holderFlips, &$holders) {
        $name = $tag->getName();
        if(isset($holderFlips[$name])) {
            $idx = $holderFlips[$name];
            unset($holders[$idx]);
        }
    });
    array_map(function ($item) use ($comment) {
        switch($item) {
        case "copyright":
            $comment->addTag($item, 'Copyright ' . date('Y') . ' Chen Han');
            break;
        case "license":
            $comment->addTag($item, 'http://www.opensource.org/licenses/mit-license.php MIT License');
            break;
        case "since":
        case "version":
        case "link":
            $comment->addTag($item);
            break;
        }
    }, $holders);
    $comment->reload();
}

function classDoc($class, $comment)
{
    if(!$comment->getSummary()) {
        $comment->setSummary([ucfirst($class->getName()), null]);
    }
    $holders = [
        "author", "package", "since", "version"
    ];
    $holderFlips = array_flip($holders);
    $comment->tagWalk(function ($tag) use ($holderFlips, &$holders) {
        $name = $tag->getName();
        if(isset($holderFlips[$name])) {
            $idx = $holderFlips[$name];
            unset($holders[$idx]);
        }
    });
    array_map(function ($item) use ($comment) {
        switch($item) {
        case "author":
            $comment->addTag($item, date('Y') . ' Chen Han');
            break;
        case "package":
            $comment->addTag($item);
            break;
        case "since":
        case "version":
            $comment->addTag($item);
            break;
        }
    }, $holders);
    $comment->reload();
}

function propertyDoc($property, $comment)
{
    if(!$comment->getSummary()) {
        $comment->setSummary([ucfirst($property->getName()), null]);
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
        /**
     *
     * @api
     * @param
     * @param
     * @return
     * @link
     */
    if(!$comment->getSummary()) {
        $comment->setSummary([ucfirst($method->getName()), null]);
    }
    $holders = [
        "access", "param", "return", "since", "version"
    ];
    $holderFlips = array_flip($holders);
    $comment->tagWalk(function ($tag) use ($holderFlips, &$holders) {
        $name = $tag->getName();
        if(isset($holderFlips[$name])) {
            $idx = $holderFlips[$name];
            unset($holders[$idx]);
        }
    });
    array_map(function ($item) use ($method, $comment) {
        switch($item) {
        case "access":
            $comment->addTag($item, $method->getAccess());
            break;
        case "param":
            $method->paramWalk(function ($param) use ($item, $comment) {
                $comment->addTag($item, $param->getDefaultTypeInfer() . ' $' . $param->getName());
            });
            break;
        case "return":
            if($method->getReturn() === null) {
                $comment->addTag($item, "null");
            } else {
                $comment->addTag($item, $method->getReturn()->getExpr());
            }
            break;
        case "since":
        case "version":
            $comment->addTag($item);
            break;
        }
    }, $holders);
    $comment->reload();
}

$service = new CodeService;

$service->ls("/Users/chen/docker/chen_dev/application/Framework", function($_, $file) use ($service) {
    if(pathinfo($file, PATHINFO_EXTENSION) !== "php") {
        return false;
    }
    $ast = $service->analysis($file);

    echo $file, PHP_EOL;
    if(!$ast->isEmpty()) {
        fileDoc($file, $ast, $ast->getComment());
    }
    if($ast->hasClass()) {
        $class = $ast->getClass();
        classDoc($class, $class->getComment());
        $class->propertyWalk(function($property) {
            propertyDoc($property, $property->getComment());
        });
        $class->methodWalk(function($method) {
            methodDoc($method, $method->getComment());
        });
    }
});
