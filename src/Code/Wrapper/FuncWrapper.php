<?php

namespace CodeService\Code\Wrapper;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use CodeService\Code;

class FuncWrapper extends AbstractWrapper
{
    const RETURN_TYPE = 'Stmt_Return';
    
    public function setReturn($return)
    {
        $return .= ';';        
        $ast = Code\Analytic::analyticCode('<?php return ' . $return);
        $returnNode = $ast->getStmts()[0];
        $node = $this->getNode();
        $setFlag = false;
        foreach($node->stmts as $key => $stmt) {
            if($stmt->getType() === self::RETURN_TYPE) {
                $node->stmts[$key] = $returnNode;
                $setFlag = true;
                break;
            }
        }
        if($setFlag === false) {
            $node->stmts[] = $returnNode;
        }
    }

    public function getReturn()
    {
        $node = $this->getNode();
        if($node && $node->stmts) {
            foreach($node->stmts as $key => $stmt) {
                if($stmt->getType() === self::RETURN_TYPE) {
                    return new ReturnWrapper($stmt);
                }
            }
        }
    }

    public function appendProcess($process)
    {
        $process .= ';';
        $ast = Code\Analytic::analyticCode('<?php ' . $process);
        $processNode = $ast->getStmts()[0];        
        $node = $this->getNode();
        $count = count($node->stmts);
        $node->stmts = array_merge(
            array_slice($node->stmts, 0, -1),
            [$processNode],
            array_slice($node->stmts, -1)
        );
    }

    public function appendParam($param)
    {
        $ast = Code\Analytic::analyticCode('<?php function(' . $param . '){};');
        $paramNode = $ast->getStmts()[0]->params[0];
        $this->getNode()->params[] = $paramNode;
    }

    public function getParam($param)
    {
        $param = substr($param, 1);
        $node = $this->getNode();
        foreach($node->params as $paramNode) {
            if($paramNode->name === $param) {
                return new ParamWrapper($paramNode);
            }
        }
    }

    public function paramWalk($call)
    {
        $node = $this->getNode();
        foreach($node->params as $paramNode) {
            $param = new ParamWrapper($paramNode);
            call_user_func($call, $param);
        }
    }
}
