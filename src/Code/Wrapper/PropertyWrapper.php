<?php

namespace CodeService\Code\Wrapper;

use PhpParser\Node\Stmt\Class_;

class PropertyWrapper extends AbstractWrapper
{
    static private $accessTable = [
            'public' => Class_::MODIFIER_PUBLIC,
            'protected' => Class_::MODIFIER_PROTECTED,
            'private' => Class_::MODIFIER_PRIVATE,
    ];
    
    public function setAccess($access)
    {
        if(isset(self::$accessTable[$access])) {
            $this->getNode()->type = self::$accessTable[$access];
        }
    }

    public function getAccess()
    {
        return array_search($this->getNode()->type, self::$accessTable);
    }
    
    public function setStatic($static = true)
    {
        $target = $this->getNode()->type ^ Class_::MODIFIER_STATIC;
        $old = $this->getNode()->type;
        if($static) {
            $this->getNode()->type = $target > $old ? $target : $old;
        } else {
            $this->getNode()->type = $target > $old ? $old : $target;
        }
    }
    
    public function setDefault($value)
    {
        $this->getNode()->props[0]->default = $this->makeValue($value);
    }

    public function getDefault()
    {
        
    }
}
