<?php
/**
 * File containing the ezcImageMissingFilterParameter.
 * 
 * @package ImageConversion
 * @version //autogen//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Thrown if an expected parameter for a filter was not submitted.
 *
 * @package ImageConversion
 * @version //autogen//
 */
class ezcImageMissingFilterParameterException extends ezcImageException
{
    function __construct( $filterName, $parameterName )
    {
        parent::__construct( "The filter '{$filterName}' expects a parameter called '{$parameterName}' which was not submitted." );
    }
}

?>
