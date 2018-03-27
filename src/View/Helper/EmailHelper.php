<?php
namespace Scid\View\Helper;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Utility\Hash;
use Cake\View\Helper;
use Cake\View\View;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Gourmet\Email\View\Helper\EmailHelper as GourmetEmailHelper;
use TijsVerkoyen\CssToInlineStyles\Exception;

/**
 * Email helper
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class EmailHelper extends GourmetEmailHelper
{

    public $helpers = ['Html', 'Url'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfigExt = [
        'tags' => [
            'ol'=>[
                'options'=>['tag'=>'ol','class'=>'ol text'],
                'itemOptions'=>['class'=>'li text']
            ],
            'ul'=>[
                'options'=>['tag'=>'ul','class'=>'ul text'],
                'itemOptions'=>['class'=>'li text']
            ],
            'hr' => ['html'=>'<div class="hr" style="width: 100%; margin: 0 auto;"> <!--[if mso | IE]>
                                  <table class="hr__table__ie" role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-right: auto; margin-left: auto;" width="100%" align="center">
                                    <tr>
                                      <td> <![endif]-->
                                        <table class="hr__table" role="presentation" border="0" align="center" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
                                          <tr class="hr__row">
                                            <td class="hr__cell" width="100%" align="left" valign="top" style="border-top: 1px solid #9A9A9A; border-color: #DDDDDD;">&nbsp;</td>
                                          </tr>
                                        </table> <!--[if mso | IE]> </td>
                                    </tr>
                                  </table> <![endif]--> </div>'],
            
        ]
    ];

    const LIST_INDENT = '    ';

    public function __construct(\Cake\View\View $View, array $config = []) {
        $this->_defaultConfig += $this->_defaultConfigExt;
        $defaultConfig = (array)Configure::read('Scid.EmailConfig');
        if ($defaultConfig) {
            $this->_defaultConfig = Hash::merge($this->_defaultConfig, $defaultConfig);
        }
        parent::__construct($View, $config);
    }


    /**
     * Build a nested list (UL/OL) out of an associative array.
     *
     * Options for $options:
     *
     * - `tag` - Type of list tag to use (ol/ul)
     *
     * Options for $itemOptions:
     *
     * - `even` - Class to use for even rows.
     * - `odd` - Class to use for odd rows.
     *
     * @param array $list Set of elements to list
     * @param array $options Options and additional HTML attributes of the list (ol/ul) tag.
     * @param array $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The nested list
     * @link https://book.cakephp.org/3.0/en/views/helpers/html.html#creating-nested-lists
     */
    public function nestedList(array $list, array $options = [], array $itemOptions = []) {
        if ('text' == $this->getType()) {
            $items = $this->_plainNestedList($list, $options, $itemOptions);
            $text = '';
            foreach ($items as $item) {
                $text .= self::LIST_INDENT . $item . $this->_eol();
            }
            return $this->_eol() .  $text . $this->_eol() ;
        }
        $tag = 'ul';
        if (!empty($options['tag'])) {
            $tag = $options['tag'];
        }
        if (empty($options) && !empty($this->_config['tags'][$tag]['options'])) {
            $options = $this->_config['tags'][$tag]['options'];
        }

        if (empty($itemOptions) && !empty($this->_config['tags'][$tag]['itemOptions'])) {
            $itemOptions = $this->_config['tags'][$tag]['itemOptions'];
        }
        return $this->Html->nestedList($list, $options, $itemOptions);
    }

    public function hr() {
        if ('text' == $this->getType()) {
            return  $this->_eol() . str_repeat('=', 60) . $this->_eol();
        }
        else {
            if (!empty($this->_config['tags']['hr'])) {
                if (!empty($this->_config['tags']['hr']['html'])) {
                    return $this->_config['tags']['hr']['html'];
                }
            }
        }
    }
    
    /**
     * Returns a formatted block tag, i.e DIV, SPAN, P.
     *
     * ### Options
     *
     * - `escape` Whether or not the contents should be html_entity escaped.
     *
     * @param string $name Tag name.
     * @param string|null $text String content that will appear inside the div element.
     *   If null, only a start tag will be printed
     * @param array $options Additional HTML attributes of the DIV tag, see above.
     * @return string The formatted tag element
     */
    public function tag($name, $text = null, array $options = []) {
        if ('text' == $this->getType()) {
            return  $this->_eol() . $text . $this->_eol();
        }
        if (empty($options) && !empty($this->_defaultConfig['options'][$name])) {
            $options = $this->_defaultConfig['options'][$name];
        }
        return $this->Html->tag($name, $text, $options);
    }

    public function fetch ($blockName, $cssFile = NULL) {
        $block = $this->_View->fetch($blockName);
        if ($this->getType() == 'text') {
            return $block;
        }
        else {
            $processed = '';
            $blocks = explode("\n", $block);
            $block = '';
            foreach ($blocks as $para) {
                $block .= $this->Html->para('p text', $para) . PHP_EOL;
            }
            $css = NULL;
            if (!empty($cssFile)) {
                $pathPrefix = Configure::read('App.cssBaseUrl');
                $ext = '.css';
                $cssPath = WWW_ROOT  . $pathPrefix . $cssFile . $ext;
                $file = new File($cssPath);
                $css = $file->read();
                if (!empty($block)) {
                    $cssToInlineStyles = new CssToInlineStyles();
                    if (!empty($css)) {
                        $cssToInlineStyles->setCSS($css);
                    }
                    $cssToInlineStyles->setUseInlineStylesBlock();
                    $cssToInlineStyles->setHTML($block);
                    try {
                        return $cssToInlineStyles->convert(

                        );
                    } catch (Exception $e) {
                        return $block;
                    }
                }
            }

            return $block;
        }
    }

    private function _plainNestedList($items, $options, $itemOptions) {
        $out = [];
        $tag = 'ol';
        if (!empty($options['tag'])) {
            $tag = $options['tag'];
        }
        $index = 1;
        foreach ($items as $key => $item) {
            $subItems = null;
            if (is_array($item)) {
                $subItems = $this->_plainNestedList($item, $options, $itemOptions);
                $item = $key;
            }
            if ($tag == 'ol') {
                $item = $index . '. ' .  $item;
            }
            else {
                $item = '* ' . $item;
            }
            $out[] = $item;
            if (!empty($subItems)) {
                $out = array_merge($out , $subItems);
            }
            $index++;
        }
        foreach ($out as $key=>$value) {
            $out[$key] = self::LIST_INDENT . $value;
        }
        return $out;
    }



}
