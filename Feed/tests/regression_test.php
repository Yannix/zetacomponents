<?php
/**
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version //autogentag//
 * @filesource
 * @package Feed
 * @subpackage Tests
 */

/**
 * @package Feed
 * @subpackage Tests
 */
class ezcFeedRegressionTest extends ezcTestRegressionTest
{
    protected function createFeed( $type, $data )
    {
        $feed = new ezcFeed( $type );
        $supportedModules = ezcFeed::getSupportedModules();
        if ( is_array( $data ) )
        {
            foreach ( $data as $property => $value )
            {
                if ( is_array( $value ) )
                {
                    foreach ( $value as $val )
                    {
                        if ( isset( $supportedModules[$property] ) )
                        {
                            $element = $feed->addModule( $property );
                        }
                        else
                        {
                            $element = $feed->add( $property );
                        }
                        if ( is_array( $val ) )
                        {
                            foreach ( $val as $subKey => $subValue )
                            {
                                if ( $subKey === '#' )
                                {
                                    $element->set( $subValue );
                                }
                                else if ( $subKey === 'MULTI' )
                                {
                                    $values = array();
                                    foreach ( $subValue as $multi )
                                    {
                                        foreach ( $multi as $subSubKey => $subSubValue )
                                        {
                                            if ( isset( $supportedModules[$subSubKey] ) )
                                            {
                                                $subElement = $element->addModule( $subSubKey );
                                            }
                                            else
                                            {
                                                if ( $property === 'skipDays' )
                                                {
                                                    $values[] = $subSubValue;
                                                    $element->days = $values;
                                                }
                                                else if ( $property === 'skipHours' )
                                                {
                                                    $values[] = $subSubValue;
                                                    $element->hours = $values;
                                                }
                                                else
                                                {
                                                    $subElement = $element->add( $subSubKey );
                                                }
                                            }

                                            if ( $property !== 'skipDays'
                                                 && $property !== 'skipHours' )
                                            {
                                                $subElement->set( $subSubValue );
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    if ( is_array( $subValue ) )
                                    {
                                        if ( count( $subValue ) === 0 || !isset( $subValue[0] ) )
                                        {
                                            if ( isset( $supportedModules[$subKey] ) )
                                            {
                                                $subElement = $element->addModule( $subKey );
                                            }
                                            else
                                            {
                                                $subElement = $element->add( $subKey );
                                            }
                                        }

                                        foreach ( $subValue as $subSubKey => $subSubValue )
                                        {
                                            if ( $subSubKey === '#' )
                                            {
                                                $subElement->set( $subSubValue );
                                            }
                                            else
                                            {
                                                if ( is_array( $subSubValue ) )
                                                {
                                                    if ( isset( $supportedModules[$subKey] ) )
                                                    {
                                                        $subElement = $element->addModule( $subKey );
                                                    }
                                                    else
                                                    {
                                                        $subElement = $element->add( $subKey );
                                                    }
                                                    foreach ( $subSubValue as $subSubSubKey => $subSubSubValue )
                                                    {
                                                        if ( $subSubSubKey === '#' )
                                                        {
                                                            $subElement->set( $subSubSubValue );
                                                        }
                                                        else
                                                        {
                                                            if ( is_array( $subSubSubValue ) )
                                                            {
                                                                foreach ( $subSubSubValue as $subSubSubSubKey => $subSubSubSubValue )
                                                                {
                                                                    $subSubElement = $subElement->add( $subSubSubKey );
                                                                    foreach ( $subSubSubSubValue as $subSubSubSubSubKey => $subSubSubSubSubValue )
                                                                    {
                                                                        if ( $subSubSubSubSubKey === '#' )
                                                                        {
                                                                            $subSubElement->set( $subSubSubSubSubValue );
                                                                        }
                                                                        else
                                                                        {
                                                                            $subSubElement->$subSubSubSubSubKey = $subSubSubSubSubValue;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            else
                                                            {
                                                                $subElement->$subSubSubKey = $subSubSubValue;
                                                            }
                                                        }
                                                    }
                                                }
                                                else
                                                {
                                                    $subElement->$subSubKey = $subSubValue;
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $element->$subKey = $subValue;
                                    }
                                }
                            }
                        }
                        else
                        {
                            $element->set( $val );
                        }
                    }
                }
                else
                {
                    $feed->$property = $value;
                }
            }
        }

        return $feed;
    }
}
?>
