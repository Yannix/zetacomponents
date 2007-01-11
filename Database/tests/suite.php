<?php
/**
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package Database
 * @subpackage Tests
 */

/**
 * Including the tests
 */
require_once 'factory_test.php';
require_once 'transactions_test.php';
require_once 'instance_test.php';
require_once 'handler_test.php';
require_once 'sqlabstraction/expression_test.php';
require_once 'sqlabstraction/query_test.php';
require_once 'sqlabstraction/query_select_test.php';
require_once 'sqlabstraction/query_subselect_test.php';
require_once 'sqlabstraction/query_select_test_impl.php';
require_once 'sqlabstraction/query_select_join_test.php';
require_once 'sqlabstraction/query_insert_test.php';
require_once 'sqlabstraction/query_update_test.php';
require_once 'sqlabstraction/query_delete_test.php';
require_once 'sqlabstraction/query_subselect_test_impl.php';

/**
 * @package Database
 * @subpackage Tests
 */
class ezcDatabaseSuite extends PHPUnit_Framework_TestSuite
{
	public function __construct()
	{
        parent::__construct();
        $this->setName( 'Database' );
        $this->addTest( ezcDatabaseFactoryTest::suite() );
        $this->addTest( ezcDatabaseTransactionsTest::suite() );
        $this->addTest( ezcDatabaseInstanceTest::suite() );
        $this->addTest( ezcDatabaseHandlerTest::suite() );
        $this->addTest( ezcQueryExpressionTest::suite() );
        $this->addTest( ezcQueryTest::suite() );
        $this->addTest( ezcQuerySelectTest::suite() );
        $this->addTest( ezcQuerySubSelectTest::suite() );        
        $this->addTest( ezcQuerySelectTestImpl::suite() );
        $this->addTest( ezcQuerySubSelectTestImpl::suite() );
        $this->addTest( ezcQuerySelectJoinTestImpl::suite() );
        $this->addTest( ezcQueryInsertTest::suite() );
        $this->addTest( ezcQueryUpdateTest::suite() );
        $this->addTest( ezcQueryDeleteTest::suite() );
	}

    public static function suite()
    {
        return new ezcDatabaseSuite();
    }
}
?>
