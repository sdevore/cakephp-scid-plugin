<?php

    namespace Scid\View\Helper;

    use Cake\ORM\Entity;
    use Cake\Core\Configure;
    use BootstrapUI\View\Helper\HtmlHelper as Helper;

    use Picqer\Barcode\BarcodeGeneratorHTML;
    use Picqer\Barcode\BarcodeGeneratorSVG;
    use Picqer\Barcode\BarcodeGeneratorPNG;
    use Picqer\Barcode\BarcodeGeneratorJPG;

    /**
     * Html helper
     *
     * @property \Cake\View\Helper\UrlHelper $Url
     */
    class HtmlHelper extends Helper
    {

        protected $_didEnablePopovers = FALSE;
        protected $_icons = [
            'add'                => 'plus',
            'add-user'   => 'user-plus',
            'add-photographer'   => 'user-plus',
            'add-contractor'   => 'user-plus',
            'add-assignment'     => [
                'icon'   => 'calendar-plus',
                'weight' => 'regular',
            ],
            'accept-assignment'  => [
                'icon'   => 'calendar-plus',
                'weight' => 'light',
            ],
            'delete'             => 'times',
            'delete-contractor'  => 'user-times',
            'delete-user'  => 'user-times',
            'delete-assignment'  => [
                'icon'   => 'calendar-times',
                'weight' => 'regular',
            ],
            'decline-assignment' => [
                'icon'   => 'calendar-minus',
                'weight' => 'light',
            ],
            'calendar'           => 'calendar',
            'assignment'         => 'server',
            'assignments'        => 'th-list',
            'view'               => 'eye',
            'invite'             => 'envelope',
            'invites'            => [
                'icon'   => 'envelope',
                'weight' => 'regular',
            ],
            'invitations'        => 'list-alt',
            'opened'             => [
                'icon'   => 'envelope-open',
                'weight' => 'regular',
            ],
            'help'               => 'question-circle',
            'news'               => [
                'icon'   => 'newspaper',
                'weight' => 'regular',
            ],
            'email'              => 'envelope-square',
            'cell'               => 'mobile',
            'remind'             => 'retweet',
            'contractor'         => 'user',
            'contractors'        => 'users',
            'companies'          => 'building',
            'users'              => 'users',
            'roles'              => 'wrench',
            'skills'             => 'industry',
            'states'             => 'cogs',
            'regions'            => 'globe',
            'types'              => 'book',

        ];
        const SCID_CSS_PATHS = 'Scid.css.paths';
        const SCID_SCRIPT_URLS = 'Scid.script.urls';
        const SCRIPT_BOTTOM = 'scriptBottom';

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

        /**
         * A mapping of aliases for button styles.
         *
         * @var array
         */
        public $buttonAttrAliases = [
            'sm'    => 'btn-sm',
            'xs'    => 'btn-xs',
            'lg'    => 'btn-lg',
            'block' => 'btn-block',
        ];

        /**
         * add files to a list of less files to be expanded later
         *
         * @param $less
         *
         * @return void
         */
        public function addLess($less) {
            $lessArray = $this->getLess();
            if (!is_array($less)) {
                $less = [$less];
            }
            foreach ($less as $item) {
                if (empty($lessArray) || !in_array($item, $lessArray)) {
                    $lessArray[] = $item;
                }
            }
            $this->_View->set('lessArray', $lessArray);
        }

        /**
         * get the less files
         *
         * @return array|mixed
         */
        public function getLess() {
            $lessArray = $this->_View->get('lessArray');

            if (empty($lessArray)) {
                $lessArray = [];
            }

            return $lessArray;
        }

        public function barcode() {
            $generatorSVG = new BarcodeGeneratorSVG();

            $generatorPNG = new BarcodeGeneratorPNG();
            $generatorJPG = new BarcodeGeneratorJPG();
            $generatorHTML = new BarcodeGeneratorHTML();
        }

        /**
         * @param       $email
         * @param array $options 'label' becomes the title if you don't want to use the $email, 'subject','body' added
         *                       to the mailto url
         *
         * @return string
         */
        public function emailButton($email, $options = []) {
            $title = $email;
            if (isset($options['label'])) {
                $title = $options['label'];
                unset($options['label']);
            }

            if (!isset($options['icon'])) {
                $options['icon'] = 'email';
            }
            $url = 'mailto:' . $email;
            $query = [];
            if (!empty($options['subject'])) {
                $query[] = 'subject=' . $$options['subject'];
                unset($options['subject']);
            }
            if (!empty($options['body'])) {
                $query[] = 'body=' . $$options['subject'];
                unset($options['body']);
            }
            if (!empty($query)) {
                $url .= '?' . join('&', $query);
            }

            return $this->button($title, $url, $options);
        }

        /**
         * @param       $phone
         * @param array $options 'label' becomes the title if you don't want to use the $email, 'subject','body' added
         *                       to the mailto url
         *
         * @return string
         */
        public function phoneButton($phone, $options = []) {
            if (isset($options['label'])) {
                $title = $options['label'];
                unset($options['label']);
            }
            else {
                $title = $this->phone($phone);
            }
            if (!isset($options['icon'])) {
                if (!empty($phone->type) && $phone->type->name == 'Cell') {
                    $options['icon'] = 'cell';
                }
                else {
                    $options['icon'] = 'phone';
                }
            }

            $url = 'tel:' . $phone;

            return $this->button($title, $url, $options);
        }

        /**
         * make a nice looking button with some icon support and extra classes
         *
         * @param       $title
         * @param null  $url
         * @param array $options
         *
         * @return string
         */
        public function button($title, $url = NULL, array $options = []) {
            $options = $this->applyButtonClasses($options);
            $options = $this->renameClasses($this->buttonAttrAliases, $options);

            return $this->link($title, $url, $options);
        }

        /**
         * @param string|\Cake\ORM\Entity $path
         * @param array                   $options
         *
         * @return null|string
         */
        public function image($path, array $options = []) {
            $classes = [];
            if (!is_string($path) && $path instanceof Entity) {
                $entity = $path;
                $retinaPrefix = NULL;
                $field = 'photo';

                if (!empty($options['field'])) {
                    $field = $options['field'];
                    unset($options['field']);
                }

                if (method_exists($entity, 'getImagePath')) {
                    $path = $entity->getImagePath($field, $options);
                }
                if (method_exists($entity, 'getRetinaImagePath')) {
                    $retinaPath = $entity->getRetinaImagePath($field, $options);
                }
            }
            if (!empty($options['retina'])) {
                $retinaPath = $options['retina'];
                unset($options['retina']);
            }
            if (!empty($retinaPath)) {
                $options['data-rjs'] = $retinaPath;
            }
            if (isset($options['responsive'])) {
                $classes[] = 'img-responsive';
                unset($options['responsive']);
            }
            if (!empty($options['shape'])) {
                $classes[] = 'img-' . $options['shape'];
                unset($options['shape']);
            }
            if (isset($options['full'])) {
                unset($options['full']);
            }
            $options = $this->injectClasses($classes, $options);

            return parent::image($path, $options);
        }

        /**
         * Returns Bootstrap icon markup. By default, uses `<I>` and `fa`.
         *
         * @param string $name    Name of icon (i.e. search, leaf, etc.).
         * @param array  $options Additional HTML attributes.
         * @return string HTML icon markup.
         */
        public function icon($name, array $options = []) {
            // TODO: one could be more judicious in only loading the styles requested
            $this->useScript('Scid.fontawesome-all');
            $options += [
                'tag'     => 'i',
                'iconSet' => 'fa',
                'class'   => NULL,
            ];

            if (is_string($name) && key_exists($name, $this->_icons)) {
                $name = $this->_icons[$name];
            }
            $classes = [];
            if ('fa' == $options['iconSet']) {
                if (is_array($name)) {
                    $iconName = $name['icon'];
                    if (is_string($iconName) && key_exists($iconName, $this->_icons)) {
                        $iconName = $this->_icons[$iconName];
                        if (is_array($iconName)) {
                            $name += $iconName;
                            $iconName = $iconName['icon'];
                        }
                    }

                    $classes = [$options['iconSet'] . '-' . $iconName];

                    if (!isset($name['weight'])) {
                        array_unshift($classes, $options['iconSet']);
                    }
                    else {
                        switch ($name['weight']) {
                            case 'regular':
                            case 'r':
                                array_unshift($classes, $options['iconSet'] . 'r');
                                break;
                            case 'light':
                            case 'l':
                                array_unshift($classes, $options['iconSet'] . 'l');
                                break;
                            case 'solid':
                            case 's':
                                array_unshift($classes, $options['iconSet'] . 's');
                                break;
                            default:
                                array_unshift($classes, $options['iconSet']);
                                break;
                        }
                    }
                    if (!empty($name['border'])) {
                        $classes[] = 'fa-border';
                    }
                    if (!empty($name['size']) && is_numeric($name['size'])) {
                        $classes[] = $options['iconSet'] . '-' . $name['size'] . 'x';
                    }
                    if (!isset($name['fixed']) || TRUE == $name['fixed']) {
                        $classes[] = 'fa-fw';
                    }
                }
                else {
                    $classes = [$options['iconSet'], $options['iconSet'] . '-' . $name, 'fa-fw'];
                }
            }

            $option = $this->injectClasses($classes, $options);

            return $this->formatTemplate('tag', [
                'tag'   => $options['tag'],
                'attrs' => $this->templater()->formatAttributes($option, ['tag', 'iconSet']),
            ]);
        }

        /**
         * make a nicely formatted link (support for icons)
         *
         * @param array|string $title
         * @param null         $url
         * @param array        $options
         *
         * @return string
         */
        public function link($title, $url = NULL, array $options = []) {
            $title = $this->titleFromOptions($title, $options);

            return parent::link($title, $url, $options);
        }

        /**
         * @param   string $title
         * @param array    $options
         * @return string
         */
        public function titleFromOptions($title, array &$options) {
            if (!empty($options['icon'])) {
                if (is_string($options['icon'])) {
                    $icon = ['icon' => $options['icon']];
                }
                else {
                    $icon = $options['icon'];
                }
                $icon += [
                    'icon-class'  => 'hidden-md',
                    'title-class' => 'hidden-xs hidden-sm',
                ];

                // add icon to left of title
                $title =
                    $this->icon($options['icon'], ['class' => $icon['icon-class']]) . '<span class="' . $icon['title-class'] . '"> ' . $title . '</span>';
                unset($options['icon']);
                $options['escape'] = FALSE;
            }

            return $title;
        }

        /**
         * @param       $class
         * @param array $options {
         *                       byRow: true,
         *                       property: 'height',
         *                       target: null,
         *                       remove: false
         *                       }
         * @return void
         */
        public function matchHeight($class, $options = []) {
            $this->useScript('Scid.jquery.matchHeight-min', ['block' => self::SCRIPT_BOTTOM]);
            $optionsJson = '';
            if (!empty($options)) {
                $optionsJson = json_encode($options);
            }
            $this->scriptBlock("$(function() {
                $('.${class}').matchHeight(${optionsJson});
            });", ['block' => self::SCRIPT_BOTTOM,]);
        }

        public function tooltip() {

            $enableToolTip = <<<ENABLETOOLTIP
 $(function () {
        $('[data-toggle="tooltip"]').tooltip()
})
ENABLETOOLTIP;
        }

        /**
         * add support for bootstrap popovers
         *
         * @param       $linkTitle
         * @param       $title
         * @param null  $content
         * @param array $options
         * @param array $popoverOptions
         *
         * @return string
         */
        public function popover($linkTitle, $title, $content = NULL, $options = [], $popoverOptions = []) {
            $options['data-toggle'] = "popover";
            if (empty($options['tag'])) {
                $options['tag'] = 'span';
            }
            $tag = $options['tag'];
            unset($options['tag']);
            $options['data-title'] = $title;
            if (empty($popoverOptions['trigger'])) {
                $popoverOptions['trigger'] = 'hover';
            }
            if (!empty($content)) {
                $options['data-content'] = $content;
            }
            foreach ($popoverOptions as $key => $popoverOption) {
                $options['data-' . $key] = $popoverOption;
            }
            $this->enablePopovers();

            return $this->tag($tag, $linkTitle, $options);
        }

        protected function enablePopovers() {
            if (!$this->_didEnablePopovers) {
                $enablePopover = <<<ENABLEPOPOVER
$(function () {
        $('[data-toggle="popover"]').popover()
})
ENABLEPOPOVER;
                $this->scriptBlock($enablePopover, ['block' => self::SCRIPT_BOTTOM]);
                $this->_didEnablePopovers = TRUE;
            }
        }

        public function enableMasonry($selector = '.grid', $options = []) {
            $this->useScript(['Scid.masonry.pkgd.min', 'Scid.imagesloaded.pkgd.min'], ['block' => self::SCRIPT_BOTTOM]);
            $_options = [
                'itemSelector'    => '.grid-item',
                'columnWidth'     => '.grid-sizer',
                'percentPosition' => TRUE,
            ];
            $options = array_merge($options, $_options);
            $optionString = [];
            foreach ($options as $key => $value) {
                if (is_bool($value)) {
                    $optionString[] = $key . ':' . ($value ? 'true' : 'false');
                }
                else {
                    $optionString[] = $key . ':' . "'$value'";
                }
            }
            $optionString = '{' . implode(',', $optionString) . '}';
            $varName = uniqid('$masonry');
            $enableMasonry = <<<ENABLEMASONRY
            var ${varName} = $('${selector}').masonry({$optionString});
            // layout Masonry after each image loads
            ${varName}.imagesLoaded().progress( function() {
                 ${varName}.masonry('layout');
            });
ENABLEMASONRY;
            $this->scriptBlock($enableMasonry, ['block' => self::SCRIPT_BOTTOM]);
        }

        private function buildJSArray($array = []) {
            $json = json_encode($array, JSON_PRETTY_PRINT, 2);

            return $json;
        }

        public function enableIsotope($selector = '.grid', $options = []) {
            $this->useScript(['Scid.isotope.pkgd.min', 'Scid.imagesloaded.pkgd.min'], ['block' => self::SCRIPT_BOTTOM]);
            $_options = [
                'itemSelector'    => '.grid-item',
                'percentPosition' => TRUE,
            ];

            $options = array_merge($options, $_options);
            if (!empty($options['masonry']['sizeClass'])) {
                $sizeClass = $options['masonry']['sizeClass'];
                unset($options['masonry']['sizeClass']);
            }
            if (!empty($options['masonry']['columnWidth'])) {
                $widthClass = $options['masonry']['columnWidth'];
                if (strpos($widthClass, '.') !== false  && strpos($widthClass, '.') == 0 ) {
                    $widthClass = ltrim($widthClass,'.');
                }
                else {
                    $options['masonry']['columnWidth'] = '.' . $widthClass;
                }

            }
            $optionString = json_encode($options, JSON_PRETTY_PRINT, 4);

            $varName = uniqid('$isotope');
            $enableIsotope = <<<ENABLEISOTOPE
            var ${varName} = $('${selector}').isotope({$optionString});
            // layout Masonry after each image loads
            ${varName}.imagesLoaded().progress( function() {
                 ${varName}.isotope('layout');
            });
          
ENABLEISOTOPE;
            $this->scriptBlock($enableIsotope, ['block' => self::SCRIPT_BOTTOM]);
            if (!empty($sizeClass) && !empty($widthClass)) {

                return $this->tag('div', '', ['class' => [$widthClass, $sizeClass]]);
            }
            else return '';
        }

        /**
         * format a phone number
         *
         * @param       $phone
         *
         * @return mixed
         */
        public function phone($phone) {
            $phone = preg_replace("/[^0-9]/", "", $phone);

            if (strlen($phone) == 7) {
                return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
            }
            elseif (strlen($phone) == 10) {
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
            }
            else {
                return $phone;
            }
        }

        /**
         * adds path for for CSS stylesheets to include in the layout based on needs of the plugin and other uses.
         *
         * ### Usage
         *
         * Include one CSS file:
         *
         * ```
         * echo $this->Html->css('styles.css');
         * ```
         *
         * Include multiple CSS files:
         *
         * ```
         * echo $this->Html->css(['one.css', 'two.css']);
         * ```
         *
         * Add the stylesheet to view block "css":
         *
         * ```
         * $this->Html->css('styles.css', ['block' => true]);
         * ```
         *
         * Add the stylesheet to a custom block:
         *
         * ```
         * $this->Html->css('styles.css', ['block' => 'layoutCss']);
         * ```
         *
         * ### Options
         *
         * - `block` Set to true to append output to view block "css" or provide
         *   custom block name.
         * - `once` Whether or not the css file should be checked for uniqueness. If true css
         *   files  will only be included once, use false to allow the same
         *   css to be included more than once per request.
         * - `plugin` False value will prevent parsing path as a plugin
         * - `rel` Defaults to 'stylesheet'. If equal to 'import' the stylesheet will be imported.
         * - `fullBase` If true the URL will get a full address for the css file.
         *
         * @param string|array $paths   The name of a CSS style sheet or an array containing names of
         *                              CSS stylesheets. If `$paths` is prefixed with '/', the path will be relative to
         *                              the webroot of your application. Otherwise, the path will be relative to your
         *                              CSS path, usually webroot/css.
         * @param array        $options Array of options and HTML arguments.
         * @return string|null CSS `<link />` or `<style />` tag, depending on the type of link.
         * @link https://book.cakephp.org/3.0/en/views/helpers/html.html#linking-to-css-files
         */
        public function useCssFile($paths, array $options = []) {
            $existingPaths = Configure::read(self::SCID_CSS_PATHS);
            if (empty($existingPaths)) {
                $existingPaths = [];
            }
            if (is_string($paths)) {
                $paths = [$paths];
            }
            $options['block'] = TRUE;
            foreach ($paths as $path) {
                if (empty($existingPaths[$path])) {
                    $existingPaths[$path] = $options;
                    $this->css($path, $options);
                }
            }
            Configure::write(self::SCID_CSS_PATHS, $existingPaths);
        }

        /**
         * adds one or many scripts to be returned as links with scriptFiles() depending on the number of scripts given.
         *
         * If the filename is prefixed with "/", the path will be relative to the base path of your
         * application. Otherwise, the path will be relative to your JavaScript path, usually webroot/js.
         *
         * ### Usage
         *
         * Include one script file:
         *
         * ```
         * echo $this->Html->script('styles.js');
         * ```
         *
         * Include multiple script files:
         *
         * ```
         * echo $this->Html->script(['one.js', 'two.js']);
         * ```
         *
         * Add the script file to a custom block:
         *
         * ```
         * $this->Html->script('styles.js', ['block' => 'bodyScript']);
         * ```
         *
         * ### Options
         *
         * - `block` Set to true to append output to view block "script" or provide
         *   custom block name.
         * - `once` Whether or not the script should be checked for uniqueness. If true scripts will only be
         *   included once, use false to allow the same script to be included more than once per request.
         * - `plugin` False value will prevent parsing path as a plugin
         * - `fullBase` If true the url will get a full address for the script file.
         *
         * @param string|array $url     String or array of javascript files to include
         * @param array        $options Array of options, and html attributes see above.
         * @return string|null String of `<script />` tags or null if block is specified in options
         *                              or if $once is true and the file has been included before.
         * @link https://book.cakephp.org/3.0/en/views/helpers/html.html#linking-to-javascript-files
         */

        public function useScript($urls, array $options = []) {
            $existingUrls = Configure::read(self::SCID_SCRIPT_URLS);
            if (empty($existingUrls)) {
                $existingUrls = [];
            }
            if (is_string($urls)) {
                $urls = [$urls];
            }
            if (empty($options['block'])) {
                $options['block'] = TRUE;
            }

            foreach ($urls as $url) {
                if (empty($existingUrls[$url])) {

                    $existingUrls[$url] = $options;
                    $this->script($url, $options);
                }
            }
            Configure::write(self::SCID_SCRIPT_URLS, $existingUrls);
        }
    }

