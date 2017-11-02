<?php
namespace Scid\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Scid\Controller\Component\FlashComponent;

/**
 * Scid\Controller\Component\FlashComponent Test Case
 */
class FlashComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\Controller\Component\FlashComponent
     */
    public $Flash;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Flash = new FlashComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Flash);

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
