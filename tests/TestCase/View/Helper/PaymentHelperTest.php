<?php
namespace Scid\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Scid\View\Helper\PaymentHelper;

/**
 * Scid\View\Helper\PaymentHelper Test Case
 */
class PaymentHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\View\Helper\PaymentHelper
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
        $view = new View();
        $this->Payment = new PaymentHelper($view);
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
