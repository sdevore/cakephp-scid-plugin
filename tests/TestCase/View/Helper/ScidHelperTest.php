<?php
namespace Scid\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Scid\View\Helper\ScidHelper;

/**
 * Scid\View\Helper\ScidHelper Test Case
 */
class ScidHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\View\Helper\ScidHelper
     */
    public $Scid;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Scid = new ScidHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Scid);

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
