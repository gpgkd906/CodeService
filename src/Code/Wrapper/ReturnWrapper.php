<?php

namespace CodeService\Code\Wrapper;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use CodeService\Code;

class ReturnWrapper extends AbstractWrapper
{
    public function getType()
    {
        return $this->getNode()->expr->getType();
    }

    public function isStaticCall()
    {
        return $this->getType() === 'Expr_StaticCall';
    }
}