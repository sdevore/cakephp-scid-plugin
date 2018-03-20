<?php
namespace Scid\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use Scid\View\Helper\MarkdownHelper;

/**
 * Scid\View\Helper\MarkdownHelper Test Case
 */
class MarkdownHelperTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \Scid\View\Helper\MarkdownHelper
     */
    public $Markdown;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $view = new View();
        $this->Markdown = new MarkdownHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Markdown);

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
