<?php
namespace Scid\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Scid\View\Helper\FakerHelper;

/**
 * Scid\View\Helper\FakerHelper Test Case
 */
class FakerHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\View\Helper\FakerHelper
     */
    public $Faker;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Faker = new FakerHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Faker);

        parent::tearDown();
    }

    /**
     * Test createFake method
     *
     * @return void
     */
    public function testCreateFake()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test populate method
     *
     * @return void
     */
    public function testPopulate()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test value method
     *
     * @return void
     */
    public function testValue()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
