<?php
namespace CodeService\Code\Wrapper;

use PhpParser\Node\Stmt\Class_;

trait AccessTrait
{
    public function setAccess($access)
    {
        if(isset(ClassWrapper::$accessTable[$access])) {
            $this->getNode()->type = ClassWrapper::$accessTable[$access];
        }
    }
    
    public function getAccess()
    {
        return array_search($this->getNode()->type, ClassWrapper::$accessTable);
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
}
