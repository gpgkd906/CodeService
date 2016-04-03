<?php

namespace CodeService\Code\Wrapper;

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
use PhpParser\Comment\Doc;

class AstWrapper extends AbstractWrapper
{
    private $namespace = null;
    private $class = null;
    private $use = [];
    private $traitUse = [];
    private $method = [];
    private $property = [];
    private $classConst = [];
    private $classWrapper = null;
    private $namespaceWrapper = null;
    
    public function setUseNode ($use)
    {
        return $this->use = $use;
    }

    public function getUseNode ()
    {
        return $this->use;
    }
    
    public function addUseNode ($name, $use)
    {
        return $this->use[$name] = $use;
    }

    public function setNamespaceNode ($namespace)
    {
        return $this->namespace = $namespace;
    }

    public function getNamespaceNode ()
    {
        return $this->namespace;
    }

    public function setClassNode ($class)
    {
        return $this->class = $class;
    }

    public function getClassNode ()
    {
        return $this->class;
    }

    public function setTraitUseNode ($traitUse)
    {
        return $this->traitUse = $traitUse;
    }

    public function getTraitUseNode ()
    {
        return $this->traitUse;
    }
    
    public function addTraitUseNode ($name, $traitUse)
    {
        return $this->traitUse[$name] = $traitUse;
    }

    public function setMethodNode ($method)
    {
        return $this->method = $method;
    }

    public function getMethodNode ()
    {
        return $this->method;
    }

    public function addMethodNode ($name, $method)
    {
        return $this->method[$name] = $method;
    }

    public function setPropertyNode ($property)
    {
        return $this->property = $property;
    }

    public function getPropertyNode ()
    {
        return $this->property;
    }

    public function addPropertyNode ($name, $property)
    {
        return $this->property[$name] = $property;
    }

    public function setClassConstNode ($classConst)
    {
        return $this->classConst = $classConst;
    }

    public function getClassConstNode ()
    {
        return $this->classConst;
    }

    public function addClassConstNode ($name, $classConst)
    {
        return $this->classConst[$name] = $classConst;
    }
    
    public function getNamespace ()
    {
        if($this->namespaceWrapper === null) {
            $this->namespaceWrapper = new NamespaceWrapper($this->namespace);
        }
        return $this->namespaceWrapper;
    }

    public function getClass ()
    {
        if($this->classWrapper === null) {
            $this->classWrapper = new ClassWrapper($this->class);
        }
        return $this->classWrapper;
    }

    public function hasClass()
    {
        return !empty($this->class);
    }

    public function setNamespace($namespace)
    {
        $this->getNamespace()->setName($namespace);
    }

    public function setClass($class)
    {
        $this->getClass()->setName($class);
    }

    public function isEmpty()
    {
        return empty($this->getNode());
    }

    public function getComment()
    {
        if($this->isEmpty()) {
            return false;
        }
        if($this->comment === null) {
            $node = $this->getNode()[0];
            $commentNode = $node->getDocComment();
            if($commentNode === null) {
                $commentNode = new Doc("");
                $node->setAttribute("comments", [$commentNode]);
            }
            $this->comment = new CommentWrapper($commentNode);
        }
        return $this->comment;
    }

    public function setComment($comment)
    {
        if($this->isEmpty()) {
            return false;
        }
        $this->getComment();
        $this->comment->setText($comment);
    }
    
    public function toCode()
    {
        return "<?php " . PHP_EOL . PHP_EOL . $this->toString();
    }
}