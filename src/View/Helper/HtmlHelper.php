<?php

    namespace Scid\View\Helper;

    use Cake\ORM\Entity;
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
            'add-photographer'   => 'user-plus',
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
            'invites'     => [
                'icon'   => 'envelope',
                'weight' => 'regular',
            ],
            'invitations' => 'list-alt',
            'opened'      => [
                'icon'   => 'envelope-open',
                'weight' => 'regular',
            ],
            'help'        => 'question-circle',
            'news'        => [
                'icon'   => 'newspaper',
                'weight' => 'regular',
            ],
            'email'       => 'envelope-square',
            'cell'        => 'mobile',
            'remind'      => 'retweet',
            'contractor'  => 'user',
            'contractors' => 'users',
            'companies'   => 'building',
            'users'       => 'users',
            'roles'       => 'wrench',
            'skills'      => 'industry',
            'states'      => 'cogs',
            'regions'     => 'globe',
            'types'       => 'book',

        ];

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
            $optionsJson = '';
            if (!empty($options)) {
                $optionsJson = json_encode($options);
            }
            $this->scriptBlock("$(function() {
                $('.${class}').matchHeight(${optionsJson});
            });", ['block' => TRUE,]);
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
                $this->scriptBlock($enablePopover, ['block' => TRUE]);
                $this->_didEnablePopovers = TRUE;
            }
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
    }
