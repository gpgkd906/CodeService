<?php
namespace CodeService\Code;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinter;

class NodeVisitor extends NodeVisitorAbstract
{
    private $dispatch = [
        Stmt\Namespace_::class => 'mapNamespace',
        Stmt\Class_::class => 'mapClass',
        Stmt\ClassMethod::class => 'mapMethod',
        Stmt\Use_::class => 'mapUse',
        Stmt\TraitUse::class => 'mapTraitUse',
        Stmt\Property::class => 'mapProperty',
        Stmt\ClassConst::class => 'mapConst',
    ];
    
    /**
     *
     * @api
     * @var mixed $astWrapper 
     * @access private
     * @link
     */
    private $astWrapper = null;

    /**
     * 
     * @api
     * @param mixed $astWrapper
     * @return mixed $astWrapper
     * @link
     */
    public function __construct ($astWrapper)
    {
        return $this->astWrapper = $astWrapper;
    }

    /**
     * 
     * @api
     * @return mixed $astWrapper
     * @link
     */
    public function getAstWrapper ()
    {
        return $this->astWrapper;
    }
    
    public function enterNode(Node $node) {
        foreach($this->dispatch as $class => $dispatch) {
            if(is_a($node, $class)) {
                call_user_func([$this, $dispatch], $node);
            }
        }
        return $node;
    }

    private function mapUse(Stmt\Use_ $node)
    {
        $name = join('\\', $node->uses[0]->name->parts);
        $this->getAstWrapper()->addUseNode($name, $node);
    }

    private function mapNamespace(Stmt\Namespace_ $node)
    {
        $this->getAstWrapper()->setNamespaceNode($node);
    }

    private function mapClass(Stmt\Class_ $node)
    {
        $this->getAstWrapper()->setClassNode($node);
    }

    private function mapTraitUse(Stmt\TraitUse $node)
    {
        $name = join('\\', $node->traits[0]->parts);
        $this->getAstWrapper()->addTraitUseNode($name, $node);
    }

    private function mapMethod(Stmt\ClassMethod $node)
    {
        $this->getAstWrapper()->addMethodNode($node->name, $node);
    }

    private function mapProperty(Stmt\Property $node)
    {
        $this->getAstWrapper()->addPropertyNode($node->props[0]->name, $node);
    }
    
    private function mapConst(Stmt\ClassConst $node)
    {
        $this->getAstWrapper()->addClassConstNode($node->consts[0]->name, $node);
    }
}

