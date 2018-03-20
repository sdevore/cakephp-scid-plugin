<?php
namespace Scid\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Scid\View\Helper\EmailHelper;

/**
 * Scid\View\Helper\EmailHelper Test Case
 */
class EmailHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\View\Helper\EmailHelper
     */
    public $Email;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Email = new EmailHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Email);

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
