<?php

    namespace Scid\View\Helper;

    use Cake\View\View;
    use Cake\Core\Configure\Engine\PhpConfig;
    use Cake\Utility\Hash;
    use Cake\Utility\Inflector;
    use BootstrapUI\View\Helper\FormHelper as Helper;
    use InvalidArgumentException;

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
         * Generate an ID suitable for use in an ID attribute.
         *
         * @param string $value The value to convert into an ID.
         *
         * @return string The generated id.
         */
        public function domId($value) {
            return $this->_domId($value);
        }

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
                switch ($options['type']) {
                    case 'datepicker':
                        $options = $this->__bootstrapDatePicker($options);
                        break;
                    case 'datetimepicker':
                        $options = $this->__bootstrapDateTimePicker($options);
                        break;
                    case 'timepicker':
                        $options = $this->__bootstrapTimePicker($options);
                        break;
                    case 'daterangepicker':
                        $options = $this->__bootstrapDateRangePicker($options);
                        break;
                    case 'textarea':
                        if (isset($options['markdown'])) {
                            $options = $this->__bootstrapMarkdown($options);
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
                    case 'sortable-serialize':
                        $options = $this->__sortableSerialize($options);
                        break;
                    case 'sortable-post':
                        $options = $this->__sortablePost($options);
                        break;
                    case 'duration':
                        $options = $this->__durationPicker($options);
                        break;
                    case 'filepond':
                        $options = $this->__filepond($options);
                }
            }

            return parent::control($fieldName, $options); // TODO: Change the autogenerated stub
        }

        /**
         * @param $options
         *
         * @return mixed
         */
        private function __bootstrapMarkdown($options) {
            $id = $options['id'];
            $this->Html->useScript([
                                       'Scid.bootstrap-markdown', 'Scid.markdown',
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

            if (!empty($options['markdown']['snippets'])) {
                $btns = [];
                foreach ($options['markdown']['snippets'] as $name => $value) {
                    $btnName = Inflector::camelize($name);
                    $btns[] = <<<BTN
{
            name: "cmd{$btnName}",
            toggle: false, // this param only take effect if you load bootstrap.js
            title: "{$name}",
            btnText: '{$name}',
            callback: function(e){
              // Replace selection with some drinks

              chunk = "{$value} "

              // transform selection and set the cursor into chunked text
              e.replaceSelection(chunk)
              cursor = selected.start

              // Set the cursor
              e.setSelection(cursor,cursor+chunk.length)
            }
          }
BTN;
                }
                $btnString = implode(',', $btns);

                $script /** @lang JavaScript */ = <<<SCRIPT
$("#{$id}").markdown({
  additionalButtons: [
    [{
          name: "groupCustom",
          data: [
          {$btnString}
          ]
    }]
  ]
})
SCRIPT;
                $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM,]);
                unset($options['markdown']);
            }

            return $options;
        }

        /**
         * http://www.bootstrap-switch.org/
         * @param $fieldName
         * @param $options
         *
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
            }
            elseif (!empty($options['label']) && is_string($options['label'])) {
                $text = $options['label'];
            }
            if ($text === NULL) {
                if (strpos($fieldName, '.') !== FALSE) {
                    $fieldElements = explode('.', $fieldName);
                    $text = array_pop($fieldElements);
                }
                else {
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
            }
            elseif (!empty($options['label']) && is_string($options['label'])) {
                $text = $options['label'];
            }
            if ($text === NULL) {
                if (strpos($fieldName, '.') !== FALSE) {
                    $fieldElements = explode('.', $fieldName);
                    $text = array_pop($fieldElements);
                }
                else {
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
        private function __bootstrapDateRangePicker($options) {
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
         * @param $idForInput
         *
         * @return mixed
         */
        private function __bootstrapDateTimePicker($options) {
            $id = $options['id'];
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
         * @param $idForInput
         *
         * @return mixed
         */
        private function __bootstrapTimePicker($options) {
            $id = $options['id'];
            $this->Html->useScript(['Scid.bootstrap-timepicker.min',], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
            $this->Html->useCssFile('Scid.bootstrap-timepicker');
            $options['prepend'] = $this->Html->icon('clock-o');
            if (!empty($this->_inputDefaults['between'])) {
                $options['between'] = $this->_inputDefaults['between'];
                $options['between'] =
                    str_replace('input-group', 'input-group bootstrap-timepicker timepicker', $options['between']);
            }
            else {
                $options['between'] = "<div class=\"input-group bootstrap-timepicker timepicker\">";
            }
            $options['type'] = 'text';

            $this->Html->scriptBlock("$(document).ready(function() {
        $('#{$id}').timepicker();
    });", ['block' => HtmlHelper::SCRIPT_BOTTOM,]);

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
                }
                else {
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

        private function __bootstrapDatePicker($options) {
            $id = $options['id'];
            $this->Html->useScript([
                                       'Scid.moment.min', 'Scid.daterangepicker',
                                   ], ['block' => HtmlHelper::SCRIPT_BOTTOM]);
            $this->Html->useCssFile('Scid.daterangepicker');
            $options['prepend'] = $this->Html->icon('calendar');
            $options['type'] = 'text';
            $defaultRangeOptions = [
                'singleDatePicker' => TRUE,
                'timePicker'       => FALSE,
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
         * @param $rangeOptionString
         */
        private function __rangeOptions(&$options, $defaultOptions) {
            if (!empty($options['rangeOptions'])) {
                $rangeOptions = $options['rangeOptions'];
            }
            else {
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
            }
            else {
                $rangeOptionString = $this->__rangeOptionsString($rangeOptions);
            }

            return $rangeOptionString;
        }

        /**
         * @param $rangeOptions
         */
        private function __rangeOptionsString($rangeOptions) {
            $rangeOptionString = "{\n";
            foreach ($rangeOptions as $key => $value) {
                $key = '"' . $key . '"';
                if (is_bool($value)) {
                    $rangeOptionString .= $key . ': ' . ($value ? 'true' : 'false') . ",\n";
                }
                elseif (is_array($value)) {
                    $rangeOptionString .= $key . ": " . $this->__rangeOptionsString($value);
                }
                elseif (FALSE != strpos($key, 'Date')) {
                    $rangeOptionString .= $key . ': \'' . CakeTime::format($value, '%m/%d/%Y') . "',\n";
                }
                else {
                    $rangeOptionString .= $key . ': \'' . $value . "',\n";
                }
            }
            $rangeOptionString .= '}';

            return $rangeOptionString;
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

        private function __filepond($options) {


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
            }
            else {
                $options['type'] = 'hidden';
                $this->unlockField($options['name']);
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
            $this->Html->useCssFile(['Scid.select2.min', 'Scid.select2-bootstrap.min']);
            $this->Html->useScript('Scid.select2.min', ['block' => HtmlHelper::SCRIPT_BOTTOM]);
            $options['type'] = 'select';
            $script = "$(document).ready(function() {
    $('#{$id}').select2();
});";
            $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);

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

    $.ajax({
            data: jsonString,
            type: 'POST',
            url: '${url}'
        });
    _super(\$item, container);
  }
});";
            $this->Html->scriptBlock($script, ['block' => HtmlHelper::SCRIPT_BOTTOM]);
            if ($options['sortable-serialize']['test'] && $options['sortable-serialize']['test']) {
                $options['type'] = 'textarea';
                $options['label'] = 'Serialized ' . $group;
            }
            else {
                $options['type'] = 'hidden';
            }

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

    }
