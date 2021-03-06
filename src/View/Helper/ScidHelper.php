<?php
namespace Scid\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Scid\Utility\ScidUtils;

/**
 * Scid helper
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class ScidHelper extends Helper
{

    public $helpers = ['Html'];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * @param       $boolean
     * @param array $options
     */
    public function boolean($boolean, $options = array('type'=>'icon')) {
        if (empty($options['type'])) {
            $options['type'] = 'icon';
        }
        $true = 'true';
        $false = 'false';
        switch ($options['type']) {
            case 'icon':
                $true = $this->Html->icon('check');
                $false = '';
                break;
            case 'checkbox':
                $true = $this->Html->icon('check-square');
                $false = $this->Html->icon('square');
                break;
            case 'yes-no':
                $true = 'Yes';
                $true = 'No';
                break;
        }

        return $boolean?$true:$false;

    }
}
