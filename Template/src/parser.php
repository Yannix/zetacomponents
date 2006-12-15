<?php
/**
 * File containing the ezcTemplateParser class
 *
 * @package Template
 * @version //autogen//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Parser for template files.
 *
 * @package Template
 * @version //autogen//
 * @access private
 */
class ezcTemplateParser
{

    /**
     * @var ezcTemplate
     */
    public $template;

    /**
     * @var ezcTemplateSourceCode
     */
    public $source;

    /**
     * Controls whether debug is displayed or not.
     *
     * @var bool
     */
    public $debug;

    /**
     * Controls whether whitespace trimming is done on the parser tree or not.
     *
     * @var bool
     */
    public $trimWhitespace;

    /**
     * The object which is responsible for removing whitespace.
     *
     * @var ezcTemplateWhitespaceRemoval
     */
    protected $whitespaceRemoval;

    /**
     * Stores the symbol table. At the beginning of parsing (at ProgramSourceToTstParser)
     * a new symbol table is created. The rest of the nodes can access the symbol
     * table.
     *
     * @var ezcTemplateSymboLTable
     */
    public $symbolTable;

    /**
     *
     * @note The source code in $code must be loaded/created before passing it to this parser.
    */
    function __construct( ezcTemplateSourceCode $source, ezcTemplate $template )
    {
        $this->source = $source;
        $this->template = $template;
        $this->textElements = array();
        $this->trimWhitespace = true;
        $this->debug = false;

        $this->symbolTable = ezcTemplateSymbolTable::getInstance();
        $this->symbolTable->reset();

        $this->whitespaceRemoval = new ezcTemplateWhitespaceRemoval();
    }

    /**
     * Creates a new cursor object with the text $sourceText and returns it.
     * @note This must be used instead of using new operator to instantiate
     *       cursors. This then allows the creation method to by testable.
     *
     * @param string $sourceText The source code.
     * @return ezcTemplateCursor
     */
    public function createCursor( $sourceText )
    {
        return new ezcTemplateCursor( $sourceText );
    }

    /**
     * @todo Currently used for testing order of element creation, might not be needed.
     */
    public function reportElementCursor( $startCursor, $endCursor, $element )
    {
        // echo "element <", get_class( $element ) . ">\n";
    }


    /**
     * Figures out the operator precedence for the new operator $newOperator
     * by examining it with the current operator element.
     *
     * @param ezcTemplateTstNode $currentOperator Either the current operator
     *                                            element or general parameter
     *                                            element.
     * @param ezcTemplateOperatorTstNode $newOperator The newly found operator.
     * @return ezcTemplateOperatorTstNode
     */
    public function handleOperatorPrecedence( /*ezcTemplateTstNode*/ $currentOperator, ezcTemplateOperatorTstNode $newOperator )
    {
        if ( $currentOperator === null )
        {
            throw new ezcTemplateInternalException( "No current operator/operand has been set" );
        }

        if ( !( $currentOperator instanceof ezcTemplateOperatorTstNode ) )
        {
            if ( $this->debug )
            {
                // *** DEBUG START ***
                echo "non operator added <", get_class( $currentOperator ), "> to operator <", get_class( $newOperator ), ">\n";
                echo "non operator added as parameter, continuing on new operator\n";
                // *** DEBUG END ***
            }

            // Note this operand should be prepended (not appended) in case
            // the new operator already have some parameters set.
            $newOperator->prependParameter( $currentOperator );
            return $newOperator;
        }

        if ( $currentOperator->precedence > $newOperator->precedence )
        {
            if ( $this->debug )
            {
                // *** DEBUG START ***
                echo "new operator <", get_class( $newOperator ), ">:", $newOperator->precedence, " has lower precedence than <", get_class( $currentOperator ), ">:", $currentOperator->precedence, "\n";
                echo "searching for root operator\n";
                // *** DEBUG END ***
            }


            // Controls whether the $newOperator should be become the new root operator or not
            // This happens if all operators have a higher precedence than the new operator.
            $asRoot = false;

            // Find parent with less or equal precedence
            while ( $currentOperator->precedence > $newOperator->precedence )
            {
                if ( $currentOperator->parentOperator === null )
                {
                    $asRoot = true;
                    break;
                }
                $currentOperator = $currentOperator->parentOperator;
            }

            if ( $asRoot )
            {
                if ( $this->debug )
                {
                    // *** DEBUG START ***
                    echo "new operator <", get_class( $newOperator ), ">:", $newOperator->precedence, " has lower precedence than all operators\n";
                    echo "new operator is new root with old root as parameter, continuing on new parameter\n";
                    // *** DEBUG END ***
                }

                $newOperator->prependParameter( $currentOperator );
                $currentOperator->parentOperator = $newOperator;

                if ( $this->debug )
                {
                    // *** DEBUG START ***
                    echo "\n\n\n";
                    echo ezcTemplateTstTreeOutput::output( $newOperator );
                    echo "\n\n\n";
                    // *** DEBUG END ***
                }

                return $newOperator;
            }
        }


        // Check if the operators can merge parameters, reasons for this can be:
        // - The operators are of the same class
        // - The : part of a conditional operator is found
        if ( $currentOperator->canMergeParametersOf( $newOperator ) )
        {
            if ( $this->debug )
            {
                // *** DEBUG START ***
                echo "operators can merge their parameters <", get_class( $currentOperator ), "> & <", get_class( $newOperator ), ">\n";
                echo "no swap is done, parameters are merged\n";
                // *** DEBUG END ***
            }
            $currentOperator->mergeParameters( $newOperator );

            if ( $this->debug )
            {
                // *** DEBUG START ***
                echo "\n\n\n";
                echo ezcTemplateTstTreeOutput::output( $currentOperator );
                echo "\n\n\n";
                // *** DEBUG END ***
            }

            return $currentOperator;
        }

        if ( $currentOperator->precedence < $newOperator->precedence )
        {
            if ( $this->debug )
            {
                // *** DEBUG START ***
                echo "new operator <", get_class( $newOperator ), ">:", $newOperator->precedence, " has higher precedence than <", get_class( $currentOperator ), ">:", $currentOperator->precedence, "\n";
                echo "swapping last parameter with new operator, continuing on new parameter\n";
                // *** DEBUG END ***
            }

            $parameter = $currentOperator->getLastParameter();
            $currentOperator->setLastParameter( $newOperator );
            $newOperator->parentOperator = $currentOperator;
            if ( $parameter !== null )
                $newOperator->prependParameter( $parameter );

            if ( $this->debug )
            {
                // *** DEBUG START ***
                echo "\n\n\n";
                echo ezcTemplateTstTreeOutput::output( $currentOperator );
                echo "\n\n\n";
                // *** DEBUG END ***
            }

            return $newOperator;
        }

        // Same precedence, order must be checked
        if ( $currentOperator->precedence == $newOperator->precedence )
        {
            if ( $this->debug )
            {
                // *** DEBUG START ***
                echo "new operator <", get_class( $newOperator ), ">:", $newOperator->precedence, " is same precedence as <", get_class( $currentOperator ), ">:", $currentOperator->precedence, "\n";
                // *** DEBUG END ***
            }

//            $currentOperator->handleEqualPrecedence( $newOperator );
            if ( $this->debug )
                echo "setting current operator as parameter of new operator and using its parent as new parent, continuing on new parameter\n";

            $parentOperator = $currentOperator->parentOperator;
            $parameter = $currentOperator->getLastParameter();
//            $newOperator->appendParameter( $currentOperator );
            $newOperator->prependParameter( $currentOperator );
            if ( $parentOperator !== null )
                $parentOperator->setLastParameter( $newOperator );
            $currentOperator->parentOperator = $newOperator;
            $newOperator->parentOperator = $parentOperator;

            if ( $this->debug )
            {
                // *** DEBUG START ***
                echo "\n\n\n";
                echo ezcTemplateTstTreeOutput::output( $newOperator );
                echo "\n\n\n";
                // *** DEBUG END ***
            }

            return $newOperator;
        }

        if ( $this->debug )
        {
            // *** DEBUG START ***
            echo "new operator <", get_class( $newOperator ), ">:", $newOperator->precedence, " /\ <", get_class( $currentOperator ), ">:", $currentOperator->precedence, "\n";
            echo "\n\n\n";
            echo ezcTemplateTstTreeOutput::output( $currentOperator );
            echo "\n\n\n";
            // *** DEBUG END ***
        }

        throw new ezcTemplateInternalException( "Should not reach this place." );
    }

    /**
     * Handles a newly parsed operand, the operand will be appended to the
     * current operator if there is one, if not it becomes the current item.
     * The element which should be the current item is returned by this function.
     *
     * @param ezcTemplateTstNode $currentOperator The current operator/operand element, can be null.
     * @param ezcTemplateTstNode $operand The parsed operator/operand which should be added as parameter.
     * @return ezcTemplateTstNode
     */
    public function handleOperand( /*ezcTemplateTstNode*/ $currentOperator, ezcTemplateTstNode $operand )
    {
        if ( $currentOperator !== null )
        {
            if ( $this->debug )
            {
                if ( $operand instanceof ezcTemplateLiteralTstNode )
                    echo "adding operand <" . get_class( $operand ) . ">::<" . var_export( $operand->value, true ) . "> to operator <" . get_class( $currentOperator ) . ">\n";
                else
                    echo "adding operand <" . get_class( $operand ) . "> to operator <" . get_class( $currentOperator ) . ">\n";
            }
            $currentOperator->appendParameter( $operand );
            return $currentOperator;
        }
        else
        {
            if ( $this->debug )
            {
                if ( $operand instanceof ezcTemplateLiteralTstNode )
                    echo "setting operand <" . get_class( $operand ) . ">::<" . var_export( $operand->value, true ) . "> as \$currentOperator\n";
                else
                    echo "setting operand <" . get_class( $operand ) . "> as \$currentOperator\n";
            }
            return $operand;
        }
    }

    public function parseIntoNodeTree()
    {
        if ( !$this->source->hasCode() )
            throw new ezcTemplateParseException( ezcTemplateParseException::NO_SOURCE_CODE );

        $sourceText = $this->source->code;
        $cursor = new ezcTemplateCursor( $sourceText );

        $this->textElements = array();

        $parser = new ezcTemplateProgramSourceToTstParser( $this, null, null );
        $parser->setAllCursors( $cursor );
        
        if ( !$parser->parse() )
        {
            $currentParser = $parser->getFailingParser();
        }

        // Trim starting/trailing whitespace
        if ( $this->trimWhitespace )
        {
            $this->whitespaceRemoval->trimProgram( $parser->program );
        }

        // temporary compatability
        $parser->program->elements = $parser->program->children;

        return $parser->program;
    }

    /**
     * Trims away indentation for one block level.
     *
     * The parser will call the ezcTemplateBlockTstNode::trimIndentation() method
     * of the specified block object with the whitespace removal object passed
     * as parameter. This allows the block element to choose how to apply the trimming
     * process since it may have more than one child list.
     *
     * @note This does nothing if self::$trimWhitespace is set to false.
     * @param ezcTemplateBlockTstNode $block
     *        Block element which has its children trimmed of indentation whitespace.
     */
    public function trimBlockLevelIndentation( ezcTemplateBlockTstNode $block )
    {
        if ( !$this->trimWhitespace )
        {
            return;
        }

        // Tell the block to trim its indentation by assign the object
        // which has defined the rules for trimming whitespace
        $block->trimIndentation( $this->whitespaceRemoval );
    }

    /**
     * Trims away EOL whitespace for block lines for the specified block element.
     *
     * The parser will call the ezcTemplateBlockTstNode::trimLine() method
     * of the specified block object with the whitespace removal object passed
     * as parameter. This allows the block element to choose how to apply the trimming
     * process since it may have more than one child list.
     *
     * @note This does nothing if self::$trimWhitespace is set to false.
     * @param ezcTemplateBlockTstNode $block
     *        Block element which has its child blocks trimmed of EOL whitespace.
     */
    public function trimBlockLine( ezcTemplateBlockTstNode $block )
    {
        if ( !$this->trimWhitespace )
        {
            return;
        }

        // Tell the block to trim its block line for any whitespace and EOL characters
        // by passign the object  which has defined the rules for trimming whitespace
        $block->trimLine( $this->whitespaceRemoval );
    }
}

?>
