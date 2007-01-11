<?php
/**
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Translation
 */

/**
 * Thrown when the translation file does not exist.
 *
 * @package Translation
 * @version //autogentag//
 */
class ezcTranslationMissingTranslationFileException extends ezcTranslationException
{
    function __construct( $fileName )
    {
        parent::__construct( "The translation file '{$fileName}' does not exist." );
    }
}
?>
