<?php

namespace Scid\View\Helper;

use Cake\I18n\Date;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use Cake\Utility\Text;
use Cake\View\View;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use BootstrapUI\View\Helper\FormHelper as Helper;
use InvalidArgumentException;
use PHP_CodeSniffer\Generators\HTML;

/**
 * Form helper
 *
 * @property \Cake\View\Helper\UrlHelper  $Url
 * @property \Scid\View\Helper\HtmlHelper $Html
 */
class FormHelper extends Helper
{

    public $helpers = ['Url', 'Scid.Html'];
    /**
     * A list of allowed styles for buttons.
     *
     * @var array
     */
    public $buttonClasses = [
        'default', 'btn-default',
        'success', 'btn-success',
        'warning', 'btn-warning',
        'danger', 'btn-danger',
        'info', 'btn-info',
        'primary', 'btn-primary',
        'hotlist', 'btn-hotlist',
        'company', 'btn-company',
        'busy', 'btn-busy',
        'available', 'btn-available',
        'company-accepted', 'btn-company-accepted',
        'pending', 'btn-pending',
        'opened', 'btn-opened',
        'expired', 'btn-expired',
        'accepted', 'btn-accepted',
        'accepted-pending', 'btn-accepted-pending',
        'accepted-no-block', 'btn-accepted-no-block',
        'declined', 'btn-declined',

    ];
    /**
     * A mapping of aliases for button styles.
     *
     * @var array
     */
    public $buttonClassAliases = [
        'default'           => 'btn-default',
        'success'           => 'btn-success',
        'warning'           => 'btn-warning',
        'danger'            => 'btn-danger',
        'info'              => 'btn-info',
        'primary'           => 'btn-primary',
        'hotlist'           => 'btn-hotlist',
        'company'           => 'btn-company',
        'busy'              => 'btn-busy',
        'available'         => 'btn-available',
        'company-accepted'  => 'btn-company-accepted',
        'pending'           => 'btn-pending',
        'opened'            => 'btn-opened',
        'expired'           => 'btn-expired',
        'accepted'          => 'btn-accepted',
        'accepted-pending'  => 'btn-accepted-pending',
        'accepted-no-block' => 'btn-accepted-no-block',
        'declined'          => 'btn-declined',
    ];
    protected $hasExpandedAdded = FALSE;
    protected $_hasSelect2Added = FALSE;
    protected $_scidType = NULL;
    protected $_dataMaskType = [];
    protected $_filepondPlugins = [
        'FilePondPluginFileEncode'           => ['script' => 'file-encode'],
        'FilePondPluginFileValidateType'     => ['script' => 'file-validate-type'],
        'FilePondPluginFileValidateSize'     => ['script' => 'file-validate-size'],
        'FilePondPluginImageExifOrientation' => ['script' => 'image-exif-orientation'],
        'FilePondPluginImagePreview'         => [
            'script' => 'image-preview',
            'css'    => 'image-preview',
        ],
        'FilePondPluginImageCrop'            => ['script' => 'image-crop'],
        'FilePondPluginImageResize'          => ['script' => 'image-resize'],
        'FilePondPluginImageTransform'       => ['script' => 'image-transform'],

    ];
    protected $_filepondRequiredPlugins = [
        'FilePondPluginFileEncode',
        'FilePondPluginFileValidateType',
        'FilePondPluginFileValidateSize',
        'FilePondPluginImageExifOrientation',
    ];

    protected $_filepondPluginCollections = [
        'imageCropAspectRatio'         => [
            'FilePondPluginImageTransform', 'FilePondPluginImageCrop',
            'FilePondPluginImageResize',
        ],
        'imagePreviewHeight'           => [
            'FilePondPluginImagePreview', 'FilePondPluginImageTransform',
        ],
        'imagePreviewMinHeight'        => [
            'FilePondPluginImageTransform', 'FilePondPluginImageResize',
        ],
        'imagePreviewMaxHeight'        => [
            'FilePondPluginImageTransform', 'FilePondPluginImageResize',
        ],
        'imagePreviewMaxFileSize'      => [
            'FilePondPluginImageTransform', 'FilePondPluginImageResize',
        ],
        'imageResizeTargetWidth'       => [
            'FilePondPluginImageTransform', 'FilePondPluginImageResize',
        ],
        'imageTransformOutputQuality'  => ['FilePondPluginImageTransform'],
        'imageResizeMode'              => [
            'FilePondPluginImageTransform', 'FilePondPluginImageResize',
        ],
        'imageTransformOutputMimeType' => [
            'FilePondPluginImageTransform',
        ],

    ];
    protected $_filePondPluginsRegistered = [];

    /**
     * Creates a `<button>` tag.
     *
     * The type attribute defaults to `type="submit"`
     * You can change it to a different value by using `$options['type']`.
     *
     * ### Options:
     *
     * - `escape` - HTML entity encode the $title of the button. Defaults to false.
     * - `confirm` - Confirm message to show. Form execution will only continue if confirmed then.
     *
     * @param string $title   The button's caption. Not automatically HTML encoded
     * @param array  $options Array of options and HTML attributes.
     *
     * @return string A HTML button tag.
     * @link https://book.cakephp.org/3.0/en/views/helpers/form.html#creating-button-elements
     */
    public function button($title, array $options = []) {
        $title = $this->Html->titleFromOptions($title, $options);

        return parent::button($title, $options);
    }

    public function toggleAllButton($title, $context, array $options = []) {

        if (empty($options['id'])) {
            $options['id'] = uniqid('checkall');
        }
        $id = $options['id'];
        $script = <<<CHECK_ALL

  $(function ()  {
    $('#{$id}').click(
        function() {
            $('{$context}').prop('checked', !$('{$context}').prop('checked'));
        }
    );
});
CHECK_ALL;

        $options['type'] = 'button';
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        return $this->button($title, $options);
    }

    public function submit($caption = NULL, array $options = []) {
        $title = $this->Html->titleFromOptions($caption, $options);
        return parent::submit($caption, $options); // TODO: Change the autogenerated stub
    }

    public function postButton($title, $url, array $options = []) {
        if (!empty($options['icon'])) {

            $options['escape'] = FALSE;
            $title = $this->Html->titleFromOptions($title, $options);
        }
        $options = $this->applyButtonClasses($options);
        $options = $this->renameClasses($this->Html->buttonAttrAliases, $options);

        return $this->postLink($title, $url, $options); // TODO: Change the autogenerated stub
    }

    public function postLink($title, $url = NULL, array $options = []) {
        if (!empty($options['button'])) {
            $style = $options['button'];
            unset($options['button']);
            $options = $this->injectClasses([$style], $options);
            $options = $this->applyButtonClasses($options);
        }
        $title = $this->Html->titleFromOptions($title, $options);

        return parent::postLink($title, $url, $options); // TODO: Change the autogenerated stub
    }

    /**
     * http://rmariuzzo.github.io/checkboxes.js/#limit-max-number-of-checked-checkboxes
     * @param $context
     * @param $max
     *
     * @return void
     */
    public function checkboxesLimit($context, $max) {
        $this->Html->useScript('/assets/npm-asset/checkboxes.js/dist/jquery.checkboxes-1.2.2.min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $script = <<<CHECKBOX_LIMIT
jQuery(function($) {
    $('{$context}').checkboxes('max', {$max});
});
CHECKBOX_LIMIT;
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
    }

    /**
     * http://rmariuzzo.github.io/checkboxes.js/#range-selection-of-checkboxes
     * @param $context string .class or #id
     *
     * @return void
     */
    public function checkboxesRange($context) {
        $this->Html->useScript('/assets/npm-asset/checkboxes.js/dist/jquery.checkboxes-1.2.2.min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $script = <<<CHECKBOX_LIMIT
jQuery(function($) {
    $('{$context}').checkboxes('range', true);
});
CHECKBOX_LIMIT;
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
    }

    /**
     * Generates a form input element complete with label and wrapper div.
     *
     * Adds extra tyoes besides the ones supported by parent class method:
     * - `datepicker`,'datetimepicker','timepicker','daterangepicker'
     * Adds markdown options for textarea
     *
     * @param string $fieldName This should be "Modelname.fieldname".
     * @param array  $options   Each type of input takes different options.
     *
     * @return string Completed form widget.
     * @deprecated Use control() instead.
     */
    public function input($fieldName, array $options = []) {
        return $this->control($fieldName, $options); // TODO: Change the autogenerated stub
    }

    /**
     * Generates a form input element complete with label and wrapper div.
     *
     * Adds extra tyoes besides the ones supported by parent class method:
     * - `datepicker`,'datetimepicker','timepicker','daterangepicker'
     * Adds markdown options for textarea
     *
     * @param string $fieldName This should be "Modelname.fieldname".
     * @param array  $options   Each type of input takes different options.
     *
     * @return string Completed form widget.
     */
    public function control($fieldName, array $options = []) {
        if (!empty($options['type'])) {
            if (!isset($options['id'])) {
                $options['id'] = TRUE;
            }
            $options = $this->_initInputField($fieldName, $options);
            if (!empty($options['data-mask'])) {
                $options = $this->__datamask($options);
            }
            switch ($options['type']) {
                case 'datepicker':
                    $options = $this->__bootstrapDatePicker($options);
                    break;
                case 'datetimepicker':
                    $options = $this->__bootstrapDateTimePicker($options);
                    break;
                case 'timepicker':
                    $options = $this->__timePicker($options);
                    break;
                case 'timepicker2':
                    $options = $this->__timePicker2($options);
                    break;
                case 'timerange':
                    $options = $this->__timerange($options);
                    break;
                case 'daterange':
                    $options = $this->__dateRange($options);
                    break;
                case 'textarea':
                    if (isset($options['markdown'])) {
                        $options = $this->__markdown($options);
                    }
                    if (isset($options['expand'])) {
                        $options = $this->__expand($options);
                    }
                    break;
                case 'select':
                    if (isset($options['select-visible-div'])) {
                        $options = $this->__selectExtraOptions($options);
                    }
                    break;
                case 'select2':
                    $options = $this->__select2($options);
                    break;
                case 'select2sortable':
                    $options = $this->__select2Sortable($options);
                    break;
                case 'sortable-serialize':
                    $options = $this->__sortableSerialize($options);
                    break;
                case 'sortable-post':
                    $options = $this->__sortablePost($options);
                    break;
                case 'duration':
                    $options = $this->__durationPicker($options);
                    $options['scid-type'] = 'duration';
                    break;
                case 'filepond':
                    $options = $this->__filepond($fieldName, $options);
                    break;
                case 'countries':
                    $options = $this->__countries($options);
                    break;
                case 'toggle':
                    $options = $this->__bootstrapToggle($fieldName, $options);
                    break;
                case 'signature':
                    $options = $this->__signature($fieldName, $options);
                    break;
            }
        }
        if (!empty($options['data-mask'])) {
            if (!isset($options['id'])) {
                $options['id'] = TRUE;
            }
            $options = $this->_initInputField($fieldName, $options);
            $options = $this->__datamask($options);
        }

        return parent::control($fieldName, $options); // TODO: Change the autogenerated stub
    }

    /**
     * @param $options
     *
     * @return mixed
     */
    private function __datamask($options) {
        $this->Html->useScript('/assets/npm-asset/jquery-mask-plugin/dist/jquery.mask.min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        // could add options and build out the script rather then using attribute later
        if (is_array($options['data-mask'])) {
            $mask = $options['data-mask']['mask'];
            $maskOptions = ',' . json_encode($options['data-mask']['options']);
        } else {
            $maskOptions = '';
            $mask = $options['data-mask'];
        }
        unset($options['data-mask']);
        switch ($mask) {
            case 'phone':
                $options = $this->injectClasses('phone', $options);
                if (!in_array('phone', $this->_dataMaskType)) {
                    $script = "$('.phone').mask('(000) 000-0000');";
                    $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
                    $this->_dataMaskType[] = 'phone';


                }
                return $options;
        }

        $id = $options['id'];
        $script = "$('#{$id}').mask('{$mask}' {$maskOptions});";
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);

        return $options;
    }

    private function __bootstrapDatePicker($options) {
        $id = $options['id'];
        if (!empty($options['val'])) {
            $options['val'] = $this->formatValue($options['val'], 'm/d/Y');
        }
        $this->Html->useScript([
                                   'Scid.moment.min', 'Scid.daterangepicker',
                               ], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $this->Html->useCssFile('Scid.daterangepicker');
        $options['prepend'] = $this->Html->icon('calendar');
        $options['type'] = 'text';
        $defaultRangeOptions = [
            'singleDatePicker' => TRUE,
            'timePicker'       => FALSE,
            'locale'           => ['format' => 'MM/DD/YYYY'],
        ];
        $rangeOptionString = $this->__rangeOptions($options, $defaultRangeOptions);

        $this->Html->scriptBlock("$(document).ready(function() {
        $('#{$id}').daterangepicker({$rangeOptionString});
    });", ['block' => HtmlHelper::SCRIPT_BOTTOM]);

        return $options;
    }

    public function formatValue($sourceValue, $format = 'm/d/Y g:i a') {
        if (!empty($format) &&
            (
                $sourceValue instanceof Date ||
                $sourceValue instanceof Time ||
                $sourceValue instanceof FrozenTime ||
                $sourceValue instanceof FrozenDate)) {
            $sourceValue = $sourceValue->format($format);
        }

        return $sourceValue;
    }

    /**
     * @url http://www.daterangepicker.com
     * @param $options
     * @param $rangeOptionString
     */
    private function __rangeOptions(&$options, $defaultOptions) {
        if (!empty($options['rangeOptions'])) {
            $rangeOptions = $options['rangeOptions'];
        } else {
            $rangeOptions = [];
        }
        foreach ($defaultOptions as $key => $option) {
            if (!isset($rangeOptions[$key])) {
                $rangeOptions[$key] = $option;
            }
        }
        $rangeOptionDateKeys = ['startDate', 'endDate', 'minDate', 'maxDate'];
        if (!empty($options['startDate']) && !empty($options['endDate'])) {
            $options['value'] =
                CakeTime::format($options['startDate'], '%m/%d/%Y') . ' - ' . CakeTime::format($options['endDate'], '%m/%d/%Y');
        }
        foreach ($rangeOptionDateKeys as $key) {
            if (!empty($options[$key])) {
                $rangeOptions[$key] = $options[$key];
                unset($options[$key]);
            }
        }
        if (empty($rangeOptions)) {
            $rangeOptionString = '';
        } else {
            $rangeOptionString = $this->__rangeOptionsString($rangeOptions);
        }

        return $rangeOptionString;
    }

    /**
     * @param $rangeOptions
     */
    private function __rangeOptionsString($rangeOptions) {
        $result = json_encode($rangeOptions);
        $result = str_replace('\/', '/', $result);
        return $result;
    }

    /**
     * @param $options
     * @param $idForInput
     *
     * @return mixed
     */
    private function __bootstrapDateTimePicker($options) {
        $id = $options['id'];
        if (!empty($options['val'])) {
            $options['val'] = $this->formatValue($options['val']);
        }
        $this->Html->useScript([
                                   'Scid.moment.min', 'Scid.daterangepicker',
                               ], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $this->Html->useCssFile('Scid.daterangepicker');
        $options['prepend'] = $this->Html->icon('calendar');
        $options['type'] = 'text';
        $defaultRangeOptions = [
            'singleDatePicker' => TRUE,
            'timePicker'       => TRUE,
            'locale'           => ['format' => 'MM/DD/YYYY h:mm A'],
        ];
        $rangeOptionString = $this->__rangeOptions($options, $defaultRangeOptions);

        $this->Html->scriptBlock("$(document).ready(function() {
        $('#{$id}').daterangepicker({$rangeOptionString});
    });", ['block' => HtmlHelper::SCRIPT_BOTTOM]);

        return $options;
    }

    /**
     * @param $options
     * @return array
     */
    private function __timePicker2($options) {
        $id = $options['id'];
        $this->Html->useScript(['Scid.jquery.timepicker.min',], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $this->Html->useCssFile('Scid.jquery.timepicker');
        $options['prepend'] = $this->Html->icon('clock');
        $options['type'] = 'text';
        if (empty($options['width'])) {
            $options['width'] = 8;
        }
        if (empty($options['timepicker'])) {
            $options['timepicker'] = [];
        }
        $defaultOptions = ['timeFormat' => 'g:ia', 'showDuration' => TRUE];
        $timepickerOptions = $defaultOptions + $options['timepicker'];
        $timeKeys = ['minTime', 'maxTime', 'durationTime'];
        if (empty($timepickerOptions['noneOption']) && !empty($options['empty'])) {
            $timepickerOptions['noneOption'] = $options['empty'];
            unset($options['empty']);
        }
        foreach ($timeKeys as $timeKey) {
            if (isset($timepickerOptions[$timeKey]) && ($timepickerOptions[$timeKey] instanceof FrozenTime || $timepickerOptions[$timeKey] instanceof FrozenTime)) {
                /** @var FrozenTime $time */
                $time = $timepickerOptions[$timeKey];
                $timepickerOptions[$timeKey] = $time->format('h:ia');
            }
        }

        $jsonOptions = json_encode($timepickerOptions);
        unset($options['timepicker']);
        $script = /** @lang JavaScript */
            <<<TIMEPICKER
$('#{$id}').timepicker({$jsonOptions});
TIMEPICKER;
        $this->Html->scriptBlock($script);
        return $options;
    }

    /**
     * @param $options
     * @return array
     */
    private function __timerange($options) {
        $id = $options['id'];
        $this->Html->useScript(['Scid.jquery.timepicker.min',], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $this->Html->useScript(['Scid.timerange.min',], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $this->Html->useCssFile('Scid.jquery.timepicker');
        $options['prepend'] = $this->Html->icon('clock');
        $options['type'] = 'text';
        if (empty($options['width'])) {
            $options['width'] = 8;
        }
        if (empty($options['timepicker'])) {
            $options['timepicker'] = [];
        }
        $defaultOptions = ['timeFormat' => 'g:ia', 'showDuration' => TRUE];
        $timepickerOptions = $defaultOptions + $options['timepicker'];
        $timeKeys = ['minTime', 'maxTime', 'durationTime'];
        if (empty($timepickerOptions['noneOption']) && !empty($options['empty'])) {
            $timepickerOptions['noneOption'] = $options['empty'];
            unset($options['empty']);
        }
        foreach ($timeKeys as $timeKey) {
            if (isset($timepickerOptions[$timeKey]) && ($timepickerOptions[$timeKey] instanceof FrozenTime || $timepickerOptions[$timeKey] instanceof FrozenTime)) {
                /** @var FrozenTime $time */
                $time = $timepickerOptions[$timeKey];
                $timepickerOptions[$timeKey] = $time->format('h:ia');
            }
        }

        $jsonOptions = json_encode($timepickerOptions);
        unset($options['timepicker']);
        $script = /** @lang JavaScript */
            <<<TIMEPICKER
$('#{$id}').timepicker({$jsonOptions});
TIMEPICKER;
        $this->Html->scriptBlock($script);
        return $options;
    }

    /**
     * @param $options
     * @param $idForInput
     *
     * @return mixed
     */
    private function __timePicker($options) {
        $id = $options['id'];
        if (!empty($options['val'])) {
            $options['val'] = $this->formatValue($options['val'], 'g:i a');
        }
        $this->Html->useScript(['Scid.bootstrap-timepicker.min',], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $this->Html->useCssFile('Scid.bootstrap-timepicker');
        $options['prepend'] = $this->Html->icon('clock');
        if (!empty($this->_inputDefaults['between'])) {
            $options['between'] = $this->_inputDefaults['between'];
            $options['between'] =
                str_replace('input-group', 'input-group bootstrap-timepicker timepicker', $options['between']);
        } else {
            $options['between'] = "<div class=\"input-group bootstrap-timepicker timepicker\">";
        }
        $options['type'] = 'text';

        $json =
            [
                'icons' => [
                    'up'   => 'far fa-chevron-up',
                    'down' => 'far fa-chevron-down',
                ],
            ];
        if (!empty($options['timepicker'])) {
            $json = $json + $options['timepicker'];
            unset($options['timepicker']);
        }
        $json = json_encode($json);
        $this->Html->scriptBlock("$(document).ready(function() {
        $('#{$id}').timepicker({$json});
    });", ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

        return $options;
    }

    /**
     * @param $options
     *                                      $options can have a sub array 'rangeOptions' with the keys and values below
     *                                      showDropdowns: (boolean) Show year and month select boxes above calendars to jump to a specific month and year
     *                                      showWeekNumbers: (boolean) Show week numbers at the start of each week on the calendars
     *                                      timePicker: (boolean) Allow selection of dates with times, not just dates
     *                                      timePickerIncrement: (number) Increment of the minutes selection list for times (i.e. 30 to allow only selection of times ending in 0 or 30)
     *                                      timePicker24Hour: (boolean) Use 24-hour instead of 12-hour times, removing the AM/PM selection
     *                                      timePickerSeconds: (boolean) Show seconds in the timePicker
     *                                      ranges: (object) Set predefined date ranges the user can select from.
     *                                      Each key is the label for the range, and its value an array with two dates representing the bounds of the range
     *                                      opens: (string: 'left'/'right'/'center') Whether the picker appears aligned to the left, to the right, or centered under the HTML element it's attached to
     *                                      drops: (string: 'down' or 'up') Whether the picker appears below (default) or above the HTML element it's attached to
     *                                      buttonClasses: (array) CSS class names that will be added to all buttons in the picker
     *                                      applyClass: (string) CSS class string that will be added to the apply button
     *                                      cancelClass: (string) CSS class string that will be added to the cancel button
     *                                      locale: (object) Allows you to provide localized strings for buttons and labels,
     *                                      customize the date display format, and change the first day of week for the calendars
     *                                      singleDatePicker: (boolean) Show only a single calendar to choose one date,
     *                                      instead of a range picker with two calendars;
     *                                      the start and end dates provided to your callback will be the same single date chosen
     *                                      autoApply: (boolean) Hide the apply and cancel buttons, and automatically apply a new date range as soon as two dates or a predefined range is selected
     *                                      parentEl: (string) jQuery selector of the parent element that the date range picker will be added to, if not provided this will be 'body'
     * @param $idForInput
     *
     * @return mixed
     */
    private function __dateRange($options) {
        $id = $options['id'];
        $this->Html->useScript([
                                   'Scid.moment.min', 'Scid.daterangepicker',
                               ], ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        $this->Html->useCssFile('Scid.daterangepicker');
        $options['prepend'] = $this->Html->icon('calendar');
        $options['type'] = 'text';
        $defaultRangeOptions = ['autoApply' => TRUE];
        $rangeOptionString = $this->__rangeOptions($options, $defaultRangeOptions);

        $this->Html->scriptBlock("$(document).ready(function() {
        $('#{$id}').daterangepicker({$rangeOptionString});
    });", ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

        return $options;
    }

    /**
     * @param $options
     *
     * @return mixed
     */
    private function __markdown($options) {
        $id = $options['id'];
        // note we use a slightly modified from the upstream bootstrap-markdown
        // to fix icon for picture
        $this->Html->useScript([
                                   'Scid.bootstrap-markdown.scid', 'Scid.markdown',
                                   'Scid.to-markdown',
                               ], ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        if (!isset($options['rows'])) {
            $options['row'] = 15;
        }
        if (!isset($options['data-width'])) {
            $options['data-width'] = 'inherit';
        }
        $options['data-provide'] = 'markdown';
        $options['data-iconlibrary'] = "fa";
        $options['type'] = 'textarea';
        $markdownOptions = [
            'fullscreen' => ['enable' => FALSE],
        ];
        if (!empty($options['markdown']['snippets'])) {
            $markdownOptions['addionalButtons'] = [];
            foreach ($options['markdown']['snippets'] as $name => $value) {
                $btnName = Inflector::camelize($name);
                $btn = [
                    'name'     => "cmd{$btnName}",
                    'toggle'   => FALSE,
                    'title'    => $name,
                    'btnText'  => $name,
                    'callback' => "function(e){
              // Replace selection with some drinks

              chunk = \"{$value} \"

              // transform selection and set the cursor into chunked text
              e.replaceSelection(chunk)
              cursor = selected.start

              // Set the cursor
              e.setSelection(cursor,cursor+chunk.length)
            }",
                ];
            }
        }
        $markdownOptionsJson = json_encode($markdownOptions);
        $script /** @lang JavaScript */ = <<<SCRIPT
$("#{$id}").markdown({$markdownOptionsJson})
SCRIPT;
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        unset($options['markdown']);

        return $options;
    }

    private function __expand($options) {

        $options = $this->injectClasses(['autoExpand'], $options);
        if (!$this->hasExpandedAdded) {
            $script = <<<EXPAND
$(document)
    .one('focus.autoExpand', 'textarea.autoExpand', function(){
        var savedValue = this.value;
        this.value = '';
        this.baseScrollHeight = this.scrollHeight;
        this.value = savedValue;
    })
    .on('input.autoExpand', 'textarea.autoExpand', function(){
        var minRows = this.getAttribute('data-min-rows')|0, rows;
        this.rows = minRows;
        rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 16);
        this.rows = minRows + rows;
    });
EXPAND;
            $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
            $this->hasExpandedAdded = TRUE;
        }
        return $options;

    }

    /**
     * @param null|array $options
     *
     * @return null|array
     */
    private function __selectExtraOptions($options = NULL) {
        if (!empty($options['select-visible-div'])) {
            $groupClass = $options['select-visible-div'];
            unset($options['select-visible-div']);
            $id = $options['id'];
            if (!empty($options['id'])) {
                $id = $options['id'];
            }
            $script = "$(document).ready(function(){
                        $('select#${id}').change(function(){
                            $(this).find('option:selected').each(function(){
                                var optionValue = $(this).attr('value');
                                if(optionValue){
                                    $('.${groupClass}').not('.' + optionValue).hide();
                                    $('.' + optionValue).show();
                                } else{
                                    $('.${groupClass}').hide();
                                }
                            });
                        }).change();
                    });";
            $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        }

        return $options;
    }

    private function __select2($options) {
        /**
         * https://github.com/select2/select2
         * and
         * https://github.com/select2/select2-bootstrap-theme
         * <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
         * <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
         */
        $this->Html->useCssFile(['/node_modules/select2/dist/css/select2.min', 'Scid.select2-bootstrap.min']);
        $this->Html->useScript('/node_modules/select2/dist/js/select2.min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $options['type'] = 'select';
        $id = $options['id'];

        $useClass = FALSE;
        if (empty($options['select2'])) {
            $selectOptions = ['minimumResultsForSearch' => 10];
            $options = $this->Html->injectClasses('select2', $options);
            $useClass = TRUE;
        } else {
            $selectOptions = $options['select2'];
            unset($options['select2']);
        }
        $selectOptions = json_encode($selectOptions);
        if ($useClass && !$this->_hasSelect2Added) {
            $script = "$(document).ready(function() {
    $('.select2').select2({$selectOptions});
});";
            $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
            $this->_hasSelect2Added = TRUE;
        } else {

            $script = "$(document).ready(function() {
    $('#{$id}').select2({$selectOptions});
});";
            $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        }
        $options = $this->__selectMultipleButtons($options, $id);
        return $options;
    }

    private function __select2Sortable($options) {
        $this->Html->useCssFile(['/node_modules/select2/dist/css/select2.min', 'Scid.select2-bootstrap.min']);
        $this->Html->useScript(['/node_modules/select2/dist/js/select2.min', 'Scid.jquery-ui.min'], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $options['type'] = 'select';
        $id = $options['id'];

        $useClass = FALSE;
        if (empty($options['select2'])) {
            $selectOptions = ['minimumResultsForSearch' => 10];
            $options = $this->Html->injectClasses('select2', $options);
            $useClass = TRUE;
        } else {
            $selectOptions = $options['select2'];
            unset($options['select2']);
        }
        $selectOptions = json_encode($selectOptions);
        if ($useClass && !$this->_hasSelect2Added) {
            $script = <<<SELECT_2_SORTABLE_USE
$(document).ready(function() {
    $('#{$id}').select2({$selectOptions});
    
 
  function select2_sortable(\$select2){
        var ul = \$select2.next('.select2-container').first('ul.select2-selection__rendered');
        ul.sortable({
            placeholder : 'ui-state-highlight',
            forcePlaceholderSize: true,
            items       : 'li:not(.select2-search__field)',
            tolerance   : 'pointer',
            stop: function() {
                $($(ul).find('.select2-selection__choice').get().reverse()).each(function() {
                    var title = $(this).data('title');
                    function hasTitle(title, value) {
  return $(this).html() == title;
}
                    var option = $('#test option').filter(function () { 
                    return $(this).html() == title;
                     }).val();
                    \$select2.prepend(option);
                });
                
            }
        });
    }
    
    select2_sortable($("#{$id}"));
});
SELECT_2_SORTABLE_USE;
            $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
            $this->_hasSelect2Added = TRUE;
        } else {
            $script = "$(document).ready(function() {
    $('#{$id}').select2({$selectOptions});
});";
            $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        }
        return $options;
    }

    /**
     * @param null|array $options
     *
     * @return null|array
     */
    private function __sortableSerialize($options) {
        $this->Html->useScript('Scid.jquery-sortable-min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $id = $options['id'];
        $group = 'serialization';
        if (!empty($options['sortable-serialize']['group'])) {
            $group = $options['sortable-serialize']['group'];
        }
        $tag = 'ul';
        if (!empty($options['sortable-serialize']['tag'])) {
            $tag = $options['sortable-serialize']['tag'];
        }
        $script = "var group = $('${tag}.${group}').sortable({
  group: '${group}',
  delay: 500,
  onDrop: function (\$item, container, _super) {
    var data = group.sortable('serialize').get();

    var jsonString = JSON.stringify(data, null, ' ');

    $('#${id}').val(jsonString);
    _super(\$item, container);
  }
});";
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        if ($options['sortable-serialize']['test'] && $options['sortable-serialize']['test']) {
            $options['type'] = 'textarea';
            $options['label'] = 'Serialized ' . $group;
        } else {
            $options['type'] = 'hidden';
            $this->unlockField($options['name']);
        }

        return $options;
    }

    /**
     * @param null|array $options
     *
     * @return null|array
     */
    private function __sortablePost($options) {
        $this->Html->useScript('Scid.jquery-sortable-min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $id = $options['id'];
        $url = $options['url'];
        if (is_array($url)) {
            $url = $this->Url->build($url);
        }
        unset($options['url']);
        $group = 'serialization';
        if (!empty($options['sortable-serialize']['group'])) {
            $group = $options['sortable-serialize']['group'];
        }
        $tag = 'ul';
        if (!empty($options['sortable-serialize']['tag'])) {
            $tag = $options['sortable-serialize']['tag'];
        }
        if (!empty($options['sortable-serialize']['target_id'])) {
            $target_id = $options['sortable-serialize']['target_id'];
        }
        $script = <<<TAG
var group = $('${tag}.${group}').sortable({
    group: '${group}',
    delay: 500,
    onDrop: function (\$item, container, _super
)
{
    var data = group.sortable('serialize').get();
var token = $('[name="_csrfToken"]').val();
    var jsonString = JSON.stringify(data, null, ' ');
    $('#${id}').val(jsonString);
    
    $.ajax({
        beforeSend: function (xhr) { // Add this line
            xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
        },
        data: jsonString,
        type: 'POST',
        url: '${url}',
        dataType: "html",
        success: function(data, textStatus) {
        $("#{$target_id}").html(data);    
    },
    });
    _super(\$item, container
)
    ;
}
})
;
TAG;
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        if ($options['sortable-serialize']['test'] && $options['sortable-serialize']['test']) {
            $options['type'] = 'textarea';
            $options['label'] = 'Serialized ' . $group;
        } else {
            $options['type'] = 'hidden';
        }

        return $options;
    }

    /**
     * @param null|array $options
     *
     * @return null|array
     */
    private function __durationPicker($options) {
        $id = $options['id'];
        $options['type'] = 'text';
        $this->Html->useScript(['Scid.bootstrap-duration-picker',], ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        $showSeconds = 'false';
        if (!empty($options['duration']['showSeconds'])) {
            if ($options['duration']['showSeconds']) {
                $showSeconds = 'true';
            }
        }
        $showDays = 'true';
        if (isset($options['duration']['showDays'])) {
            if ($options['duration']['showDays']) {
                $showDays = 'true';
            } else {
                $showDays = 'false';
            }
        }
        $onChanged = '';
        if (!empty($options['duration']['onChanged'])) {
            $onChanged = ',
                onChanged: function (value) {
                ' . $options['duration']['onChanged'] . '
                }';
        }
        $script = "$('#${id}').durationPicker({
            // defines whether to show seconds or not
            showSeconds: ${showSeconds},
            // defines whether to show days or not
            showDays: ${showDays}
            ${onChanged}
});";
        unset($options['duration']);
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

        return $options;
    }

    /**
     * https://pqina.nl/filepond/
     *
     * @param $fieldName
     * @param $options
     *
     * @return array
     */
    private function __filepond($fieldName, $options) {


        $filepondOptions = [
            'lableIdle' =>
                'Drag & Drop your picture or <span class="filepond--label-action">Browse</span>',
        ];

        if (!empty($options['filepond'])) {
            $filepondOptions = $options['filepond'];
            unset($options['filepond']);
        }
        $this->Html->useScript('Scid.filepond.min');
        $this->Html->useCssFile('Scid.filepond.min');
        $options = $this->Html->injectClasses('filepond', $options);

        foreach ($this->_filepondRequiredPlugins as $name) {
            $this->_registerFilepondPlugin($name);
        }
        foreach ($filepondOptions as $option => $value) {
            if (!empty($this->_filepondPluginCollections[$option])) {
                $pluginCollection = $this->_filepondPluginCollections[$option];
                foreach ($pluginCollection as $name) {
                    $this->_registerFilepondPlugin($name);
                }
            }
        }
        $filepondOptions = json_encode($filepondOptions);
        $options['type'] = 'file';
        $id = $options['id'];
        $script = "FilePond.create(
  document.querySelector('#${id}'),
  ${filepondOptions}
);";
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        $this->unlockField($fieldName . '.name');
        $this->unlockField($fieldName . '.type');
        $this->unlockField($fieldName . 'tmp_name');
        $this->unlockField($fieldName . 'error');
        $this->unlockField($fieldName . 'size');
        $this->unlockField($fieldName);

        return $options;
    }

    /**
     * @param $name
     *
     * @return void
     */
    private function _registerFilepondPlugin($name) {
        if (empty($this->_filePondPluginsRegistered[$name])) {
            $value = $this->_filepondPlugins[$name];
            $this->Html->useScript('Scid.filepond-plugin-' . $value['script'] . '.min');
            if (!empty($value['css'])) {
                $this->Html->useCssFile('Scid.filepond-plugin-' . $value['css'] . '.min');
            }
            $this->Html->scriptBlock("FilePond.registerPlugin($name);",
                                     ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
            $this->_filePondPluginsRegistered[$name] = TRUE;
        }
    }

    /**
     *
     * from https://www.cssscript.com/generic-country-state-dropdown-list-countries-js/#comments
     *
     * @param $options
     *
     * @return mixed
     */
    private function __countries($options) {
        $this->Html->useScript('Scid.countries');
        if (empty($options['state_id'])) {
            $statesId = 'state';
        } else {
            $statesId = $options['state_id'];
            unset($options['state_id']);
        }
        $countriesId = $options['id'];
        $options['type'] = 'select';
        $script = "populateCountries(\"${countriesId}\", \"${statesId}\");";
        $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);

        return $options;
    }

    /**
     * https://github.com/minhur/bootstrap-toggle
     * @param $fieldName
     * @param $options
     *
     * @return string
     */
    private function __bootstrapToggle($fieldName, $options) {
        $idForInput = $this->domId($fieldName);
        $options['type'] = 'checkbox';
        $options['label'] = FALSE;
        $this->Html->useScript(['Scid.bootstrap-toggle.min',], ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        $this->Html->useCssFile([
                                    'Scid.bootstrap-toggle.min',
                                    'Scid.bootstrap-toggle-bootstrap4fix',
                                ]);
        if (!empty($options['value']) && $options['value']) {
            $options['checked'] = 'checked';
        }
        $options['data-toggle'] = 'toggle';
        $onSwitch = '';
        if (!empty($options['url'])) {
            $url = $options['url'];
            if (is_array($url)) {
                $url = $this->Url->build($url);
            }
            $token = $this->request->getParam('_csrfToken');
            $onSwitch = /** @lang jquery */
                <<<SWITCH
                $(function() {
    $('#{$idForInput}').change(function() {
    var state=$(this).prop('checked');
      $.ajax({
  type: "GET",
  url: "{$url}&new_state=" + state,
  data: state,
  cache: false,
  beforeSend: function(xhr) {
            xhr.setRequestHeader('X-CSRF-Token', "{$token}");
        },
  success: function(data, textStatus,jqXHR ){
     console.log(textStatus);
     console.log(data);
     console.log(jqXHR);
  },
  error: function(jqXHR, textStatus, errorThrown){
     console.log(textStatus);
     console.log(errorThrown);
     console.log(jqXHR);
     $("#{$idForInput}").bootstrapToggle({
     onstyle:'danger',offstyle:'danger'});
     
     alert(textStatus + ': problem updating database');
  }
});
    })
  })
  $("#{$idForInput}").on('switchChange.bootstrapSwitch', function(event, state) {
            console.log(this); // DOM element
            console.log(event); // jQuery event
            console.log(state); // true | false
            $.ajax({
  type: "GET",
  url: "{$url}/new_state:" + state,
  data: state,
  cache: false,
  success: function(data, textStatus,jqXHR ){
     console.log(textStatus);
     console.log(data);
     console.log(jqXHR);
  },
  error: function(jqXHR, textStatus, errorThrown){
     console.log(textStatus);
     console.log(errorThrown);
     console.log(jqXHR);
     $("#{$idForInput}").bootstrapSwitch('state', !state, true);
     alert(textStatus + ':' + errorThrown);
  }
});
        });
SWITCH;
        }

        $this->Html->scriptBlock($onSwitch, ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

        return $options;
    }

    /**
     * Generate an ID suitable for use in an ID attribute.
     *
     * @param string $value The value to convert into an ID.
     *
     * @return string The generated id.
     */
    public function domId($value) {
        return $this->_domId($value);
    }

    private function __signature($fieldName, $options) {
        $id = $options['id'];
        $wrapper_id = 'signature-pad-' . $id;
        $options['templateVars']['id'] = $wrapper_id;
        if (empty($options['templateVars']['canvasClass'])) {
            $options['templateVars']['canvasClass'] = 'signature-pad-canvas';
            $options['templateVars']['canvasId'] = 'signature-pad-canvas-' . $id;
        }
        if (!empty($options['signature'])) {
            if (!empty($options['signature']['mime-type'])) {
                $mimeType = $options['signature']['mime-type'];
            } else {
                $mimeType = 'image/svg+xml';
            }
            if (!empty($options['signature']['clear_id'])) {
                $clear_id = $options['signature']['clear_id'];
                $clear = "document.getElementById('{$clear_id}').addEventListener('click', function () {
                        signaturePad.clear();
                    });";
                unset($options['signature']['clear']);
            }
            if (!empty($options['signature']['undo_id'])) {
                $undo_id = $options['signature']['undo_id'];
                $undo = "document.getElementById('{$undo_id}').addEventListener('click', function () {
                        signaturePad.undo();
                    });";
                unset($options['signature']['undo_id']);
            }
            if (!empty($options['signature']['options'])) {
                $signatureOptions = $options['signature']['options'];
            }
            unset($options['signature']);
        }
        if (empty($signatureOptions)) {
            $signatureOptions = ["backgroundColor" => 'rgb(255, 255, 255)'];
        }
        $signatureOptions = json_encode($signatureOptions);
        $signatureOptions = substr($signatureOptions, 0, -1);
        $signatureOptions .= ",\n onEnd: function (event) {
        var dataURL = signaturePad.toDataURL('{$mimeType}');
        document.getElementById('{$id}').value = dataURL;
    }
            }";

        $this->addWidget('signature',
                         ['Scid.Signature', 'text', 'label']);
        $this->Html->useScript('/assets/npm-asset/signature_pad/dist/signature_pad.min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $signatureBlock = /** @lang JavaScript 1.8 */
            <<<SIGNATURE_SCRIPT
var wrapper = document.getElementById("{$wrapper_id}");
var canvas = wrapper.querySelector("canvas");
var signaturePad = new SignaturePad(canvas, 
    // It's Necessary to use an opaque color when saving image as JPEG;
    // this option can be omitted if only saving as PNG or SVG
    {$signatureOptions}
);

// Adjust canvas coordinate space taking into account pixel ratio,
// to make it look crisp on mobile devices.
// This also causes canvas to be cleared.
function resizeCanvas() {
    // When zoomed out to less than 100%, for some very strange reason,
    // some browsers report devicePixelRatio as less than 1
    // and only part of the canvas is cleared then.
    var ratio = Math.max(window.devicePixelRatio || 1, 1);

    // This part causes the canvas to be cleared
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext("2d").scale(ratio, ratio);

    // This library does not listen for canvas changes, so after the canvas is automatically
    // cleared by the browser, SignaturePad#isEmpty might still return false, even though the
    // canvas looks empty, because the internal data of this library wasn't cleared. To make sure
    // that the state of this library is consistent with visual state of the canvas, you
    // have to clear it manually.
    signaturePad.clear();
}

// On mobile devices it might make more sense to listen to orientation change,
// rather than window resize events.
window.onresize = resizeCanvas;
resizeCanvas();
SIGNATURE_SCRIPT;
        $this->Html->scriptBlock($signatureBlock, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        if (!empty($clear)) {
            $this->Html->scriptBlock($clear, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        }
        if (!empty($undo)) {
            $this->Html->scriptBlock($undo, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        }
        // $this->Html->fontCursor('#'.$wrapper_id, 'pencil',['hotspot'=>'bottom left','color'=>'brown','outline'=>'rbg(1,1,1)','size'=>32]);
        return $options;
    }

    /**
     * http://www.bootstrap-switch.org/ maynot support bootstrap 4 try bootstrapToggle
     *
     * @param $fieldName
     * @param $options
     *
     * @deprecated use boostrapToggle instead
     * @return string
     */
    private function __bootstrapSwitch($fieldName, $options) {
        $idForInput = $this->domId($fieldName);
        $this->Html->useScript(['Scid.bootstrap-switch.min',], ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        $this->Html->useCssFile('Scid.bootstrap-switch.min');
        if (!empty($options['value']) && $options['value']) {
            $options['checked'] = 'checked';
        }
        $script = <<<SCRIPT
$("#{$idForInput}").bootstrapSwitch();
SCRIPT;
        $onSwitch = '';
        if (!empty($options['url'])) {
            $url = $options['url'];
            if (is_array($url)) {
                $url = Router::url($url);
            }

            $onSwitch = <<<SWITCH
  $("#{$idForInput}").on('switchChange.bootstrapSwitch', function(event, state) {
            console.log(this); // DOM element
            console.log(event); // jQuery event
            console.log(state); // true | false
            $.ajax({
  type: "GET",
  url: "{$url}/new_state:" + state,
  data: state,
  cache: false,
  success: function(data, textStatus,jqXHR ){
     console.log(textStatus);
     console.log(data);
     console.log(jqXHR);
  },
  error: function(jqXHR, textStatus, errorThrown){
     console.log(textStatus);
     console.log(errorThrown);
     console.log(jqXHR);
     $("#{$idForInput}").bootstrapSwitch('state', !state, true);
     alert(textStatus + ':' + errorThrown);
  }
});
        });
SWITCH;
        }

        $this->Html->scriptBlock($script . $onSwitch, ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

        return $this->checkbox($fieldName, $options);
    }

    /**
     * @param $fieldName
     * @param $options
     *
     * @return string
     */
    private function __bootstrapCheckbox($fieldName, $options) {
        $this->Html->useScript('Scid.jquery.checkboxes-1.0.5.min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
        $text = NULL;
        $idForInput = $this->domId($fieldName);

        if (!empty($options['label']['text']) && is_array($options['label'])) {
            $text = $options['label']['text'];
        } else if (!empty($options['label']) && is_string($options['label'])) {
            $text = $options['label'];
        }
        if ($text === NULL) {
            if (strpos($fieldName, '.') !== FALSE) {
                $fieldElements = explode('.', $fieldName);
                $text = array_pop($fieldElements);
            } else {
                $text = $fieldName;
            }
            if (substr($text, -3) === '_id') {
                $text = substr($text, 0, -3);
            }
            $text = __(Inflector::humanize(Inflector::underscore($text)));
        }
        $errorClass = '';
        $error = $this->_extractOption('error', $options, NULL);
        if ($error !== FALSE) {
            $errMsg = $this->error($fieldName, $error);
            if ($errMsg) {
                $errorClass = ' error';
            }
        }
        $checkAllClass = 'check';
        if (!empty($options['checkall-class'])) {
            $checkAllClass = $options['checkall-class'];
            unset($options['checkall-class']);
        }
        $class = [$checkAllClass];
        if (!empty($options['class'])) {
            $class = array_merge($class, explode(' ', $options['class']));
        }
        $options['class'] = implode(' ', $class);
        $html = parent::label($fieldName,
                              parent::checkbox($fieldName,
                                               $options) . ' ' . $text, $options);

        if (!empty($errMsg)) {
            $html .= '<br />' . $errMsg;
        }
        $this->__addHelpBlock($options, $idForInput);
        if (empty($options['before'])) {

            $html = $this->Html->div('col-sm-offset-3 col-sm-6' . $errorClass, $html);
        }
        if (empty($options['div'])) {
            $html = $this->Html->div('form-group' . $errorClass, $html);
        }

        return $html;
    }

    /**
     * @param $fieldName
     * @param $options
     *
     * @return string
     */
    private function __bootstrapCheckAll($fieldName, $options) {
        $text = NULL;
        $idForInput = $this->domId($fieldName);

        if (!empty($options['label']['text']) && is_array($options['label'])) {
            $text = $options['label']['text'];
        } else if (!empty($options['label']) && is_string($options['label'])) {
            $text = $options['label'];
        }
        if ($text === NULL) {
            if (strpos($fieldName, '.') !== FALSE) {
                $fieldElements = explode('.', $fieldName);
                $text = array_pop($fieldElements);
            } else {
                $text = $fieldName;
            }
            if (substr($text, -3) === '_id') {
                $text = substr($text, 0, -3);
            }
            $text = __(Inflector::humanize(Inflector::underscore($text)));
        }
        $checkAllClass = 'check';
        if (!empty($options['checkall-class'])) {
            $checkAllClass = $options['checkall-class'];
            unset($options['checkall-class']);
        }

        $errorClass = '';
        $error = $this->_extractOption('error', $options, NULL);
        if ($error !== FALSE) {
            $errMsg = $this->error($fieldName, $error);
            if ($errMsg) {
                $errorClass = ' error';
            }
        }
        $html = parent::label($fieldName,
                              parent::checkbox($fieldName,
                                               $options) . ' ' . $text, $options);

        if (!empty($errMsg)) {
            $html .= '<br />' . $errMsg;
        }
        $this->__addHelpBlock($options, $idForInput);
        if (empty($options['before'])) {

            $html = $this->Html->div('col-sm-offset-3 col-sm-6' . $errorClass, $html);
        }
        if (empty($options['div'])) {
            $html = $this->Html->div('form-group' . $errorClass, $html);
        }
        $script = <<<CHECK_ALL_SCRIPT
$("#{$idForInput}").click(function () {
    $(".{$checkAllClass}").prop('checked', $(this).prop('checked'));
});
CHECK_ALL_SCRIPT;
        $this->Html->scriptBlock($script, ['inline' => FALSE,]);

        return $html;
    }

    /**
     * @param $options
     * @param $idForInput
     *
     * @return mixed
     */
    private function __bootstrapDateTimePickerOld($options, $idForInput) {
        $this->Html->useScript([
                                   'Scid.moment.min',
                                   'Scid.bootstrap-datetimepicker.min',
                               ], ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        $this->Html->css('Scid.bootstrap-datetimepicker.min', 'stylesheet', ['block' => TRUE,]);
        if (!empty($this->_inputDefaults['after'])) {
            $options['after'] = $this->_inputDefaults['after'];
        }
        $options['after'] =
            '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>' . $options['after'];
        $options['type'] = 'text';
        $this->Html->scriptBlock("$(document).ready(function() {
        $('#{$idForInput}').datetimepicker({
        pickTime: true
        });
    });", ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

        return $options;
    }

    /**
     * @param $options
     * @param $idForInput
     *
     * @return mixed
     */
    private function __bootstrapMonthPicker($options, $idForInput) {
        $this->Html->useScript([
                                   'Scid.moment.min',
                                   'Scid.bootstrap-datetimepicker.min',
                               ], ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        $this->Html->css('Scid.bootstrap-datetimepicker.min', 'stylesheet', ['block' => TRUE,]);
        if (!empty($this->_inputDefaults['after'])) {
            $options['after'] = $this->_inputDefaults['after'];
        }
        $options['after'] =
            '<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>' . $options['after'];
        $options['type'] = 'text';
        $this->Html->scriptBlock("$(document).ready(function() {
        $('#{$idForInput}').datetimepicker({
        pickTime: false,
        format: 'MM/YYYY'
        });
    });", ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

        return $options;
    }

    /**
     * @param $options
     * @param $idForInput
     *
     * @return mixed
     */
    private function __bootstrapTimePickerOld($options, $idForInput) {
        $this->Html->useScript([
                                   'Scid.moment.min',
                                   'Scid.bootstrap-datetimepicker.min',
                               ], ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
        $this->Html->css('Scid.bootstrap-datetimepicker.min', 'stylesheet', ['block' => TRUE,]);
        if (!empty($this->_inputDefaults['after'])) {
            $options['after'] = $this->_inputDefaults['after'];
        }
        $options['after'] =
            '<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>' . $options['after'];
        $options['type'] = 'text';
        $this->Html->scriptBlock("$(document).ready(function() {
        $('#{$idForInput}').datetimepicker({
                    pickDate: false,
                    minuteStepping:15,
                    useSeconds: false
                });
    });", ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

        return $options;
    }

    /**
     * @param $options
     * @param $id
     *
     * @return mixed
     */
    private function __selectMultipleButtons($options, $id) {
        if (!empty($options['select-buttons']) && !empty($options['multiple']) && $options['select-buttons'] && $options['multiple']) {
            $var_id = str_replace('-', '_', $id);
            $label = '';
            if (!empty($options['label'])) {
                $label = $options['label'];
            }
            $buttons = ['<span class = "btn-group btn-group-sm">'];

            $script = ['var ' . $var_id . ' = $("#' . $id . '").select2();'];
            $buttonOptions = $options['options'];
            $specifiesButtons = FALSE;
            if (is_array($options['select-buttons'])) {
                $buttonOptions = $options['select-buttons'];
                $specifiesButtons = TRUE;
            }
            foreach ($buttonOptions as $level => $group) {
                $buttonID = uniqid('select-buttons-');
                if (!$specifiesButtons) {
                    // use abbreviation of levels
                    $words = explode(" ", $level);
                    $level = "";
                    foreach ($words as $w) {
                        $level .= $w[0];
                    }
                }

                $buttons[] = $this->Html->button(h($level), '#', ['class' => ['info', 'btn-truncate-text'], 'id' => $buttonID]);
                if ($specifiesButtons) {
                    $values = json_encode($group);
                } else {
                    $values = json_encode(array_keys($group));
                }


                $script[] = /** @lang JavaScript */
                    <<<SELECT_BUTTONS
$('#${buttonID}').on('click', function () {
    var selected = {$var_id}.select2('data');
    var result = selected.map(a => a.id);

    result = result.concat(${values});
    {$var_id}.val(result).trigger('change');
return false;
});
                            
SELECT_BUTTONS;
            }
            $script = implode("\r", $script);

            $this->Html->scriptBlock($script, ['block' => \Scid\View\Helper\HtmlHelper::SCRIPT_BOTTOM]);
            $buttons[] = '</span>';
            $options['label'] = ['text' => $label . ' ' . implode("\r", $buttons), 'escape' => FALSE];
        }
        unset($options['select-buttons']);
        return $options;
    }


}
