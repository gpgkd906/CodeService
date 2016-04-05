<?php

namespace CodeService\Code\Wrapper;

class ParamWrapper extends AbstractWrapper
{
    public function getName()
    {
        return $this->getNode()->name;
    }

    public function setName($newParam)
    {
        $this->getNode()->name = $newParam;
    }

    public function getDefaultTypeInfer()
    {
        $default = null;
        if($this->getNode()->type) {
            $type = $this->getNode()->type;
            if(is_string($type)) {
                return $type;
            }
            return $type->toString();
        }
        if(!empty($this->getNode()->props)) {
            $default = $this->getNode()->props[0]->default;
        }
        return parent::getTypeInfer($default);
    }
    
    public function getDefault()
    {
        $default = $this->getNode()->default;
        return $default ? $default->value : null;
    }
    
    public function setDefault($value = null)
    {
        $this->getNode()->default = $this->makeValue($value);
    }
}
