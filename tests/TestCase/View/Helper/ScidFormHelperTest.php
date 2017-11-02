<?php
namespace Scid\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Scid\View\Helper\ScidFormHelper;

/**
 * Scid\View\Helper\ScidFormHelper Test Case
 */
class ScidFormHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\View\Helper\ScidFormHelper
     */
    public $ScidForm;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->ScidForm = new ScidFormHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ScidForm);

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
