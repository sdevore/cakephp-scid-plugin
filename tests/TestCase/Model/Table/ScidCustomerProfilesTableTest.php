<?php
namespace Scid\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Scid\Model\Table\ScidCustomerProfilesTable;

/**
 * Scid\Model\Table\ScidCustomerProfilesTable Test Case
 */
class ScidCustomerProfilesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\Model\Table\ScidCustomerProfilesTable
     */
    public $ScidCustomerProfiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.scid.scid_customer_profiles',
        'plugin.scid.members',
        'plugin.scid.profiles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ScidCustomerProfiles') ? [] : ['className' => ScidCustomerProfilesTable::class];
        $this->ScidCustomerProfiles = TableRegistry::getTableLocator()->get('ScidCustomerProfiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ScidCustomerProfiles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
