<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\TblmstaffTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\TblmstaffTable Test Case
 */
class TblmstaffTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\TblmstaffTable
     */
    public $Tblmstaff;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Tblmstaff',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Tblmstaff') ? [] : ['className' => TblmstaffTable::class];
        $this->Tblmstaff = TableRegistry::getTableLocator()->get('Tblmstaff', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Tblmstaff);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
