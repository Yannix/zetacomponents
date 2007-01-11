<?php
/**
 * File containing the ezcTemplateAstToAstContextAppender
 *
 * @package Template
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 *
 * This class adds 'context' information to the AST tree. 
 * For example, the output nodes are escaped.
 *
 * @package Template
 * @version //autogen//
 * @access private
 */
class ezcTemplateAstToAstContextAppender extends ezcTemplateAstWalker
{
    /**
     * The context specified in the ezcTemplateConfiguration.
     *
     * @var ezcTemplateOutputContext
     */
    private $context;

    public function __construct( ezcTemplateOutputContext $context )
    {
        $this->context = $context;
    }

    public function __destruct()
    {
    }

    /**
     * Returns a contextized, usually escaped, output node.
     *
     * @return ezcTemplateOutputAstNode
     */
    public function visitOutputAstNode( ezcTemplateOutputAstNode $type )
    {
        parent::visitOutputAstNode( $type );

        if ( $type->isRaw )
        {
            return $type;
        }
        return $this->context->transformOutput( $type->expression );
    }
}
?>
