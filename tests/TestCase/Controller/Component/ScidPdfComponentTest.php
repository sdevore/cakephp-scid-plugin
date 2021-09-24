<?php
namespace Scid\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use Scid\Controller\Component\ScidPdfComponent;

/**
 * Scid\Controller\Component\ScidPdfComponent Test Case
 */
class ScidPdfComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\Controller\Component\ScidPdfComponent
     */
    public $ScidPdf;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->ScidPdf = new ScidPdfComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ScidPdf);

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
