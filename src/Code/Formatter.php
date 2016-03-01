<?php

namespace CodeService\Code;

use PhpParser\PrettyPrinter;

class Formatter
{
    
    /**
     *
     * @api
     * @var mixed $prettyPrinter 
     * @access private
     * @link
     */
    static private $prettyPrinter = null;

    /**
     * 
     * @api
     * @return mixed $prettyPrinter
     * @link
     */
    static public function getPrettyPrinter ()
    {
        if(self::$prettyPrinter === null) {
            self::$prettyPrinter = new PrettyPrinter\Standard([
                'shortArraySyntax' => true
            ]);
        }
        return self::$prettyPrinter;
    }

    static public function format($stmts)
    {
        if(!is_array($stmts)) {
            $stmts = [$stmts];            
        }
        $code = self::getPrettyPrinter()->PrettyPrint($stmts);
        return $code;
    }
}