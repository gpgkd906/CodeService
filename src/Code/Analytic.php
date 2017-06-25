<?php

namespace CodeService\Code;

use CodeService\Code\Wrapper\AstWrapper;
use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\NodeTraverser;
use PhpParser\Error;
use PhpParser\ParserFactory;

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
     * @return mixed $parser
     * @link
     */
    static public function getParser ($version = null)
    {
        $version = $version ? $version : PHP_MAJOR_VERSION;
        if(self::$parser === null) {
            if($version >= 7) {
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
        return new NodeTraverser;
    }

    static public function analyticCode($code, $version = null)
    {
        $ast = new AstWrapper;
        $stmts = self::getParser($version)->parse($code);
        $ast->setStmts($stmts);
        $traverser = self::getTraverser();
        $traverser->addVisitor(new NodeVisitor($ast));
        $traverser->traverse($stmts);
        return $ast;
    }

    static public function analytic($file, $version = null)
    {
        return self::analyticCode(file_get_contents($file), $version);
    }
}
