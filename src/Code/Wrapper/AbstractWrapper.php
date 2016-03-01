<?php

namespace CodeService\Code\Wrapper;

use CodeService\Code\Formatter;
use PhpParser\BuilderFactory;
use PhpParser\Builder\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Name\Relative;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr;

class AbstractWrapper
{
    static private $factory = null;
    private $stmts = null;
    
    public function __construct($node = null)
    {
        if($node) {
            $this->stmts = $node;
        }
    }

    public function getNode()
    {
        return $this->stmts;
    }
    
    protected function getFactory()
    {
        if(self::$factory === null) {
            self::$factory = new BuilderFactory;
        }
        return self::$factory;
    }
    
    public function toString()
    {
        return Formatter::format($this->getStmts());
    }

    public function toHtml()
    {
        return nl2br('&lt?php' . PHP_EOL . $this->toString());
    }

    public function setStmts ($stmts)
    {
        return $this->stmts = $stmts;
    }

    public function getStmts ()
    {
        return $this->stmts;
    }

    public function makeValue($value)
    {
        switch(true) {
        case is_string($value):
            $value = new Scalar\String_($value);
            break;
        case is_integer($value):
            $value = new Scalar\LNumber($value);
            break;
        case is_float($value):
            $value = new Scalar\DNumber($value);
            break;
        case is_bool($value):
            if($value) {
                $value = new Expr\ConstFetch(new Name('true'));
            } else {
                $value = new Expr\ConstFetch(new Name('false'));
            }
            break;
        case is_array($value):
            $value = new Expr\Array_($value);
            break;
        default:
            $value = new Expr\ConstFetch(new Name('null'));
            break;
        }
        return $value;
    }
}
