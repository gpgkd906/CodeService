<?php

namespace CodeService\Code\Wrapper;

class NamespaceWrapper extends AbstractWrapper
{
    public function setName($newNamespace)
    {
        $parts = explode('\\', $newNamespace);
        $this->getNode()->name->parts = $parts;
    }

    public function appendUse($use, $as = null)
    {
        $factory = $this->getFactory();
        $use = $factory->use($use);
        if($as !== null) {
            $use->as($as);
        }
        $this->getNode()->stmts[] = $use->getNode();
        usort($this->getNode()->stmts, function($a, $b) {
            if($a->getType() === 'Stmt_Use') {
                return -1;
            } else {
                return 1;
            }
        });
    }

    public function addStmt($stmt)
    {
        $this->getNode()->stmts[] = $stmt;
    }

    public function getNameSpace()
    {
        return $this->getNode()->name->toString();
    }
}
