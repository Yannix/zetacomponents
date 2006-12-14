<?php

class ezcTemplateFetchCacheInformation extends ezcTemplateTstWalker
{
    public $cacheTst = null;

    public $cacheTemplate = false;
    public $cacheKeys = array();
    public $hasTTL = false;


    public function __construct()
    {
        parent::__construct();
    }


    public function visitCacheTstNode( ezcTemplateCacheTstNode $node )
    {
        $this->cacheTemplate = true;
        $this->cacheTst = $node;

        foreach ( $node->keys as $key )
        {
            // XXX cannot translate.
            // Translate the 'old' variableName to the new name.
           // $k = $key->accept($this);
            $this->cacheKeys[] = $key;
        }

        // And translate the ttl.
        if ( $node->ttl != null ) 
        {
            $this->hasTTL = true;
            // XXX cannot translate.
            // $this->programNode->ttl = $type->ttl->accept($this);
        }

        // return new ezcTemplateNopAstNode();
    }

 







}


?>
