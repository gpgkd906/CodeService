<?php

namespace CodeService\Code;

use CodeService\Code\Wrapper\AstWrapper;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\NodeTraverser;

class Analytic
{
    /**
     *
     * @api
     * @var mixed $parser 
     * @access private
     * @link
     */
    static private $parser = null;

    /**
     *
     * @api
     * @var mixed $traverser 
     * @access private
     * @link
     */
    static private $traverser = null;

    /**
     * 
     * @api
     * @return mixed $parser
     * @link
     */
    static public function getParser ()
    {
        if(self::$parser === null) {
            if(PHP_MAJOR_VERSION >= 7) {
                self::$parser = new Parser\Php7(new Lexer);
            } else {
                self::$parser = new Parser\Php5(new Lexer);
            }
        }
        return self::$parser;
    }
    
    /**
     * 
     * @api
     * @return mixed $traverser
     * @link
     */
    static public function getTraverser ()
    {
        if(self::$traverser === null) {
            self::$traverser = new NodeTraverser;
        }
        return self::$traverser;
    }
    
    static public function analyticCode($code)
    {
        $ast = new AstWrapper;
        $stmts = self::getParser()->parse($code);
        $ast->setStmts($stmts);
        $traverser = self::getTraverser();
        $traverser->addVisitor(new NodeVisitor($ast));
        $traverser->traverse($stmts);
        return $ast;
    }
    
    static public function analytic($file)
    {
        return self::analyticCode(file_get_contents($file));
    }
}
