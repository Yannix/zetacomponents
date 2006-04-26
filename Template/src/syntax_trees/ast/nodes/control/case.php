<?php
/**
 * File containing the ezcTemplateCaseAstNode class
 *
 * @package Template
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a case control structure.
 *
 * @package Template
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogen//
 */
class ezcTemplateCaseAstNode extends ezcTemplateStatementAstNode
{
    /**
     * The expression to use as case match.
     * @var ezcTemplateAstNode
     */
    public $match;

    /**
     * The body element for the case statement.
     * @var ezcTemplateBodyAstNode
     */
    public $body;

    /**
     * Initialize with function name code and optional arguments
     */
    public function __construct( ezcTemplateAstNode $match = null, ezcTemplateBodyAstNode $body = null )
    {
        parent::__construct();
        $this->match = $match;
        $this->body = $body;
    }
}
?>
