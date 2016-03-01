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
