<?php
namespace Scid\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Scid\Model\Table\PaymentProfilesTable;

/**
 * Scid\Model\Table\ScidPaymentProfilesTable Test Case
 */
class ScidPaymentProfilesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\Model\Table\PaymentProfilesTable
     */
    public $ScidPaymentProfiles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.scid.scid_payment_profiles',
        'plugin.scid.members',
        'plugin.scid.customer_profiles',
        'plugin.scid.payment_profiles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('ScidPaymentProfiles') ? [] : ['className' => PaymentProfilesTable::class];
        $this->ScidPaymentProfiles = TableRegistry::getTableLocator()->get('ScidPaymentProfiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ScidPaymentProfiles);

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
