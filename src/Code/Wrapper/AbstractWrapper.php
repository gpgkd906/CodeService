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
use PhpParser\Comment;

class AbstractWrapper
{
    static private $factory = null;
    private $stmts = null;
    protected $comment = null;
    
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
    
    public function getName()
    {
        return $this->getNode()->name;
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
            $valueNode = new Scalar\String_($value);
            break;
        case is_integer($value):
            $valueNode = new Scalar\LNumber($value);
            break;
        case is_float($value):
            $valueNode = new Scalar\DNumber($value);
            break;
        case is_bool($value):
            if($value) {
                $valueNode = new Expr\ConstFetch(new Name('true'));
            } else {
                $valueNode = new Expr\ConstFetch(new Name('false'));
            }
            break;
        case is_array($value):
            $valueNode = new Expr\Array_($value);
            break;
        default:
            $valueNode = new Expr\ConstFetch(new Name('null'));
            break;
        }
        return $valueNode;
    }

    public function getTypeInfer($valueNode)
    {
        $type = "mixed";
        switch(get_class($valueNode)) {
        case Scalar\String_::class:
            $type = "string";
            break;
        case Scalar\LNumber::class:
            $type = "integer";
            break;
        case Scalar\DNumber::class:
            $type = "float";
            break;
        case Expr\ConstFetch::class:
            $valueNodeName = $valueNode->name->__toString();
            switch($valueNodeName) {
            case "true":
            case "false":
                $type = "boolean";
                break;
            case "null":
                $type = "optional";
                break;
            }
            break;
        case Expr\Array_::class:
            $type = "array";
            break;
        default:
            //pass with mixed
            break;
        }
        return $type;
    }
    
    public function nodeWalk($call, $nodeType = null)
    {
        if($nodeType !== null) {
            if($this->getNode()) {
                foreach($this->getNode()->stmts as $stmt) {
                    if($stmt->getType() !== $nodeType) {
                        continue;
                    }
                    call_user_func($call, $stmt);
                }
            }
        } else {
            foreach($this->getNode()->stmts as $stmt) {
                call_user_func($call, $stmt);
            }
        }
    }
    
    public function getComment()
    {
        if($this->comment === null) {
            $commentNode = $this->getNode()->getDocComment();
            if($commentNode === null) {
                $commentNode = new Comment\Doc("");
                $this->getNode()->setAttribute("comments", [$commentNode]);
            }
            $this->comment = new CommentWrapper($commentNode);
        }
        return $this->comment;
    }
    
    public function setComment($comment)
    {
        $this->getComment();
        $this->comment->setContent($comment);
    }

    public function findNode($nodeType, $finder)
    {
        foreach($this->getNode()->stmts as $stmt) {
            if($stmt->getType() !== $nodeType) {
                continue;
            }
            if(call_user_func($finder, $stmt)) {
                return $stmt;
            }
        }
    }

    public function getNodeApi()
    {
        return get_class_methods($this->getNode());
    }
}
