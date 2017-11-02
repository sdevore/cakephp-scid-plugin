<?php
namespace Scid\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Scid\Controller\Component\FakerComponent;

/**
 * Scid\Controller\Component\FakerComponent Test Case
 */
class FakerComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\Controller\Component\FakerComponent
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
        $registry = new ComponentRegistry();
        $this->Faker = new FakerComponent($registry);
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
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
