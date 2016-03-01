<?php

namespace CodeService\Code\Wrapper;

use PhpParser\Node\Stmt\Class_;

class PropertyWrapper extends AbstractWrapper
{
    public function setAccess($access)
    {
        $accessTable = [
            'public' => Class_::MODIFIER_PUBLIC,
            'protected' => Class_::MODIFIER_PROTECTED,
            'private' => Class_::MODIFIER_PRIVATE,
        ];
        if(isset($accessTable[$access])) {
            $this->getNode()->type = $accessTable[$access];
        }
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
    
    public function setValue($value)
    {
        $factory = $this->getFactory();
        $property = $factory->property('');
        $property->setDefault($value);        
        $this->getNode()->props[0]->default = $property->getNode()->props[0]->default;
    }
}
