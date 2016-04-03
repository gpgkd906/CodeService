<?php

namespace CodeService\Code\Wrapper;

class PropertyWrapper extends AbstractWrapper
{
    use AccessTrait;
    
    public function getDefaultTypeInfer()
    {
        $default = null;
        if(!empty($this->getNode()->props)) {
            $default = $this->getNode()->props[0]->default;
        }
        return parent::getTypeInfer($default);
    }
        
    public function setDefault($value)
    {
        $this->getNode()->props[0]->default = $this->makeValue($value);
    }

    public function getDefault()
    {
        return $this->getNode()->props[0]->default->name->__toString();
    }

    public function getName()
    {
        return $this->getNode()->props[0]->name;
    }

    public function setName($name)
    {
        $this->getNode()->props[0]->name = $name;
    }
}
