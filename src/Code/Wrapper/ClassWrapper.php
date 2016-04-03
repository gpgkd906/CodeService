<?php

namespace CodeService\Code\Wrapper;

use PhpParser\Node\Stmt\Class_ as Stmt_Class;
use PhpParser\Builder\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Name\Relative;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr;

class ClassWrapper extends AbstractWrapper
{
    static public $accessTable = [
        'public' => Stmt_Class::MODIFIER_PUBLIC,
        'protected' => Stmt_Class::MODIFIER_PROTECTED,
        'private' => Stmt_Class::MODIFIER_PRIVATE,
    ];
    
    private $group = [
        'Stmt_TraitUse'    => [],
        'Stmt_ClassConst'  => [],
        'Stmt_Property'    => [],
        'Stmt_ClassMethod' => [],        
    ];

    public function setName($newClass)
    {
        $this->getNode()->name = $newClass;
    }

    public function extend($extend)
    {
        if($this->getNode()->extends === null) {
            $this->getNode()->extends = new Name($extend);
        } else {
            $this->getNode()->extends->parts = explode('\\', $extend);
        }
    }

    public function appendImplement($interface)
    {
        $this->getNode()->implements[] = new Name($interface);
    }

    public function appendTrait($trait)
    {
        $name = new FullyQualified($trait);
        $trait = new TraitUse([$name]);
        $this->addStmt($trait);
    }

    public function getTrait($trait)
    {
        $test = new FullyQualified($trait);
        $node = $this->findNode('Stmt_TraitUse', function($stmt) use ($test) {
            return $stmt->traits[0]->parts === $test->parts;            
        });
        if($node) {
            return new TraitUseWrapper($node);
        }
    }

    public function appendConst($const, $value = null)
    {
        $const = new Const_($const, $this->makeValue($value));
        $classConst = new ClassConst([$const]);
        $this->addStmt($classConst);
    }

    public function getConst($const)
    {
        $node = $this->findNode('Stmt_ClassConst', function($stmt) use ($const) {
            return $stmt->consts[0]->name === $const;
        });
        if($node) {
            return new ConstWrapper($node);
        }
    }
    
    public function appendProperty($property, $value = null, $access = 'private')
    {
        $factory = $this->getFactory();
        $property = $factory->property($property);
        $accessControl = 'make' . ucfirst($access);
        call_user_func([$property, $accessControl]);
        $property->setDefault($value);
        $this->addStmt($property->getNode());
    }
    
    public function getProperty($property)
    {
        $node = $this->findNode('Stmt_Property', function($stmt) use($property) {
            return $stmt->props[0]->name === $property;
        });
        if($node) {
            return new PropertyWrapper($node);
        }
    }

    public function appendMethod($name, $access = 'public')
    {
        $factory = $this->getFactory();
        $method = $factory->method($name);
        $accessControl = 'make' . ucfirst($access);
        call_user_func([$method, $accessControl]);
        $this->addStmt($method->getNode());
    }

    public function getMethod($method)
    {
        $node = $this->findNode('Stmt_ClassMethod', function($stmt) use($method) {
            return $stmt->name === $method;
        });
        if($node) {
            return new MethodWrapper($node);
        }
    }

    public function methodWalk($call)
    {
        $this->nodeWalk(function($method) use ($call) {
            $method = new MethodWrapper($method);
            call_user_func($call, $method);
        }, 'Stmt_ClassMethod');
    }
    
    public function propertyWalk($call)
    {
        $this->nodeWalk(function($property) use ($call) {
            $property = new PropertyWrapper($property);
            call_user_func($call, $property);
        }, 'Stmt_Property');
    }
    

    public function addStmt($stmt)
    {
        $sort = [
            'Stmt_TraitUse'    => 0,
            'Stmt_ClassConst'  => 1,
            'Stmt_Property'    => 2,
            'Stmt_ClassMethod' => 3,
        ];
        if(!isset($sort[$stmt->getType()])) {
            throw new Exception('Cannot add stmt not belong classNode');
        }
        $this->getNode()->stmts[] = $stmt;
        usort($this->getNode()->stmts, function($a, $b) use ($sort){
            Return $sort[$a->getType()] >= $sort[$b->getType()];
        });
    }
}