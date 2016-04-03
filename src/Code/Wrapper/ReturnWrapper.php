<?php

namespace CodeService\Code\Wrapper;

use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Ternary;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\BinaryOp\Coalesce;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\ClassConstFetch;

class ReturnWrapper extends AbstractWrapper
{
    public function getExpr($expr = null)
    {
        if($expr === null) {
            $expr = $this->getNode()->expr;
        }
        switch(get_class($expr)) {
        case PropertyFetch::class:
            return $expr->var->name . " " . $expr->name;
            break;
        case StaticPropertyFetch::class:
            return "static " . $this->toString($expr->class) . " " . $expr->name;
            break;
        case Assign::class:
            $assign = $expr->var;
            return $this->getExpr($assign);
            break;
        case Variable::class:
            return $expr->name;
            break;
        case Ternary::class:
            return "multiType " . $this->getExpr($expr->if) . " OR " .  $this->getExpr($expr->else);
            break;
        case ArrayDimFetch::class:
            return $this->getExpr($expr->var);
            break;
        case FuncCall::class:
            return "FuncCall" . $this->toString($expr->name);
            break;
        case MethodCall::class:
            return "MethodCall " . $this->getExpr($expr->var) . "::" .  $expr->name;
            break; 
        case StaticCall::class:
            return "StaticCall " . $this->toString($expr->class) . "::" .  $expr->name;
            break;
        case Coalesce::class:
            return "Optional < " . $this->getExpr($expr->left) . " , " . $this->toString($expr->right) . " >";
            break;
       case Empty_::class:
            return "boolean " . $this->toString($expr); 
            break;
        case Concat::class:
            return "string " . $this->toString($expr);
            break;
        case New_::class:
            return "instance " . $this->toString($expr->class);
            break;
        case ConstFetch::class:
            return [
                'true' => 'boolean',
                'false' => 'boolean',
                'null' => 'null',                
            ][$this->toString($expr->name)];
            break;
        case ClassConstFetch::class:
            return "ClassConstant " . $this->toString($expr->class) . " " . $expr->name;
            break;
        default:
            
            var_dump($expr, $this->toString());die;
            break;
        }
    }
    
    public function getType()
    {
        return $this->getNode()->expr->getType();
    }

    public function isStaticCall()
    {
        return $this->getType() === 'Expr_StaticCall';
    }
}