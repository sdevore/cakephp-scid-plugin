<?php
namespace Scid\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;
use Scid\Model\Behavior\PaymentBehavior;

/**
 * Scid\Model\Behavior\PaymentBehavior Test Case
 */
class PaymentBehaviorTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\Model\Behavior\PaymentBehavior
     */
    public $Payment;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->Payment = new PaymentBehavior();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Payment);

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
