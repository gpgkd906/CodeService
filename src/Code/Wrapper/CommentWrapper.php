<?php

namespace CodeService\Code\Wrapper;

use PhpParser\Comment\Doc;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlock\TagFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;

class CommentWrapper extends AbstractWrapper
{
    private $docBlock = null;
    private $summary = null;
    private $tags = null;
    private $content = null;

    private static $docBlockFactory = null;
    private static $standardTagFactory = null;
    
    private function makeDocBlock()
    {
        if($this->docBlock === null) {
            if(self::$docBlockFactory === null) {
                self::$docBlockFactory = DocBlockFactory::createInstance();
            }
            $content = $this->getContent();
            if(empty($content)) {
                $content = join(PHP_EOL, ["/**", "*/"]);
            }
            $this->docBlock = self::$docBlockFactory->create($content);
        }
        return $this->docBlock;
    }

    private function makeTag($tagLine)
    {
        if(self::$standardTagFactory === null) {
            $fqsenResolver = new FqsenResolver();
            $tagFactory = new StandardTagFactory($fqsenResolver);
            $descriptionFactory = new DescriptionFactory($tagFactory);
            
            $tagFactory->addService($descriptionFactory);
            $tagFactory->addService(new TypeResolver($fqsenResolver));
            self::$standardTagFactory = $tagFactory;
        }
        return self::$standardTagFactory->create($tagLine);
    }
    
    public function getSummary()
    {
        if($this->summary === null) {
            $this->summary = $this->makeDocBlock()->getSummary();
        }
        return $this->summary;
    }

    public function setSummary($summary)
    {
        if($this->summary === null) {
            $this->makeDocBlock();
        }
        $this->summary = $summary;
        return $this->summary;
    }
    
    public function getTags()
    {
        if($this->tags === null) {
            $this->tags = $this->makeDocBlock()->getTags();
        }
        return $this->tags;
    }    

    public function setTag($tag, $newTag)
    {
        $tags = $this->getTags();
    }

    public function addTag($name, $description = null)
    {
        $tagLine = "@" . $name;
        if($description !== null) {
            $tagLine .= " " . $description;
        }
        $newTag = $this->makeTag($tagLine);
        $this->getTags();
        $this->tags[] = $newTag;
    }

    public function tagWalk($func)
    {
        foreach($this->getTags() as $tag) {
            call_user_func($func, $tag);
        }
    }

    public function clearTags()
    {
        $this->tags = [];
    }
    
    public function removeTag($tag, $name)
    {
        throw new exception("not implements");
    }
    
    public function getContent()
    {
        if($this->content == null) {
            $this->content = $this->getNode()->getText();
        }
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
        $this->getNode()->setText($this->content);
    }

    public function reload()
    {
        $summary = $this->getSummary();
        if(is_array($summary)) {
            $commentLines = $summary;
        } else {
            $commentLines = array_map(function ($summaryLine) {
                return " " . $summaryLine;
            }, explode(PHP_EOL, $this->getSummary()));
        }
        $this->tagWalk(function($tag) use (&$commentLines) {
            $commentLines[] = $tag->render();
        });        
        $content = join(PHP_EOL, [
            "/**",
            join(PHP_EOL, array_map(function ($line) {
                return "* " . trim($line);
            }, $commentLines)),
            "*/",
        ]);
        $this->setContent($content);
    }
}
