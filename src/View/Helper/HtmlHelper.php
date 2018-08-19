<?php

namespace Scid\View\Helper;

use Cake\ORM\Entity;
use Cake\Core\Configure;
use BootstrapUI\View\Helper\HtmlHelper as Helper;

use Cake\View\View;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorJPG;

/**
 * Html helper bootstrap 4 cmpati
 *
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class HtmlHelper extends Helper
{

    const SCID_CSS_PATHS = 'Scid.css.paths';
    const SCID_SCRIPT_URLS = 'Scid.script.urls';
    const SCRIPT_BOTTOM = 'scriptBottom';
    const SCRIPT_TOP = 'scriptTop';
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
    protected $_mapConfig;
    protected $_didEnablePopovers = FALSE;
    protected $_icons = [
        'add'         => 'plus',
        'add-user'    => 'user-plus',
        'delete'      => 'times',
        'delete-user' => 'user-times',
        'calendar'    => 'calendar',
        'view'        => 'eye',
        'help'        => 'question-circle',
        'news'        => [
            'icon'   => 'newspaper',
            'weight' => 'regular',
        ],
        'email'       => 'envelope-square',
        'cell'        => 'mobile',
        'remind'      => 'retweet',
        'users'       => 'users',

    ];
    protected $_mimes = [];

    protected $_isCell = FALSE;

    /**
     * HtmlHelper constructor.
     *
     * @param \Cake\View\View $View
     * @param array           $config
     */
    public function __construct(\Cake\View\View $View, array $config = []) {
        $this->_icons = (array)Configure::read('Scid.HtmlHelper.icons') + $this->_icons;
        $this->_mimes = (array)Configure::read('Scid.HtmlHelper.mime') + $this->_mimes;
        if (empty($View->layout)) {
            $this->_isCell = TRUE;
        }
        parent::__construct($View, $config);
    }

    function GetCenterFromDegrees($data) {
        if (!is_array($data)) return FALSE;

        $num_coords = count($data);

        $X = 0.0;
        $Y = 0.0;
        $Z = 0.0;

        foreach ($data as $coord) {
            $lat = $coord[0] * pi() / 180;
            $lon = $coord[1] * pi() / 180;

            $a = cos($lat) * cos($lon);
            $b = cos($lat) * sin($lon);
            $c = sin($lat);

            $X += $a;
            $Y += $b;
            $Z += $c;
        }

        $X /= $num_coords;
        $Y /= $num_coords;
        $Z /= $num_coords;

        $lon = atan2($Y, $X);
        $hyp = sqrt($X * $X + $Y * $Y);
        $lat = atan2($Z, $hyp);

        return [$lat * 180 / pi(), $lon * 180 / pi()];
    }

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

    public function animatedScrollTo($id, $offset = 0) {
        $scriptBlock =
            /** @lang JavaScript 1.8 */
            <<<ANIMATED_SCROLL_TO
$(document).ready(function () {
    // Handler for .ready() called.
    $('html, body').animate({
        scrollTop: $('#{$id}').offset().top + {$offset}
    }, 'slow');
});
ANIMATED_SCROLL_TO;

        $this->scriptBlock($scriptBlock, ['block' => self::SCRIPT_BOTTOM]);
    }

    public function barcode() {
        $generatorSVG = new BarcodeGeneratorSVG();

        $generatorPNG = new BarcodeGeneratorPNG();
        $generatorJPG = new BarcodeGeneratorJPG();
        $generatorHTML = new BarcodeGeneratorHTML();
    }

    public function bootstrapColTest() {
        if (!Configure::read('Scid.viewDebug')) {
            return '';
        }
        $return = '';
        $sizes = [
            'xs' => ['badge', 'badge-light', 'd-inline', 'd-sm-none'],
            'sm' => ['badge', 'badge-success', 'd-none', 'd-sm-inline', 'd-md-none'],
            'md' => ['badge', 'badge-warning', 'd-none', 'd-md-inline', 'd-lg-none'],
            'lg' => ['badge', 'badge-info', 'd-none', 'd-lg-inline', 'd-xl-none'],
            'xl' => ['badge', 'badge-danger', 'd-none', 'd-xl-inline'],
        ];
        foreach ($sizes as $label => $class) {
            $return .= $this->tag('span', $label, ['class' => $class]);
        }

        return $return;
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

    public function driverTourButton($title, $options, $tour = []) {
        if (empty($tour)) {
            return '';
        }
        $this->useCssFile('Scid.driver.min');
        $this->useScript('Scid.driver.min');
        $jsonTour = json_encode($tour);
        $driverName = uniqid('driver');
        $scriptBlock = <<<DRIVER_TOUR_BLOCK
const ${driverName} = new Driver();
// Define the steps for introduction
${driverName}.defineSteps(${jsonTour});
DRIVER_TOUR_BLOCK;

        $this->scriptBlock($scriptBlock, ['block' => self::SCRIPT_BOTTOM]);
        $options['onclick'] = $driverName . '.start()';

        return $this->button($title, '#', $options);
    }

    public function dropdown($title, $url = '', array $links = [], array $options = []) {
        if (isset($options['div'])) {
            $divOptions = $options['div'];
            unset($options['div']);
        } else {
            $divOptions = [];
        }
        $divOptions = $this->injectClasses('btn-group', $divOptions);
        if (empty($url)) {

            $dropOptions = $this->injectClasses(['dropdown-toggle'], $options);
            $dropOptions['data-toggle'] = 'dropdown';
            $dropOptions['aria-haspopup'] = 'true';
            $dropOptions['aria-expanded'] = 'false';
            $dropOptions['id'] = uniqid('dropdownMenuButton');
            $dropdown = $this->button($title, '#', $dropOptions);
        } else {
            $dropdown = $this->button($title, $url, $options);
            $dropOptions =
                $this->injectClasses(['dropdown-toggle', 'dropdown-toggle-split'], $options);
            $dropOptions['data-toggle'] = 'dropdown';
            $dropOptions['aria-haspopup'] = 'true';
            $dropOptions['aria-expanded'] = 'false';
            $dropOptions['escape'] = FALSE;
            $dropOptions['id'] = uniqid('dropdownMenuButton');
            $dropdown .=
                $this->button('<span class="sr-only">Toggle Dropdown</span>', '#', $dropOptions);
        }
        $link = [];
        foreach ($links as $value) {
            if (isset($value['separator'])) {
                $link[] = '<div class="dropdown-divider"></div>';
            } else {
                if (empty($value['options'])) {
                    $value['options'] = [];
                }
                $value['options'] = $this->injectClasses(['dropdown-item'], $value['options']);
                $link[] =
                    $this->link($value['title'], $value['url'], $value['options']);
            }
        }
        $link = join("\r", $link);
        $menu = $this->tag('div', $link, [
            'class' => 'dropdown-menu', 'aria-labelledby' => $dropOptions['id'],
        ]);

        return $this->tag('div', $dropdown . $menu, $divOptions);
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

    public
    function enableIsotope($selector = '.grid', $options = []) {
        $this->useScript([
                             'Scid.isotope.pkgd.min', 'Scid.imagesloaded.pkgd.min',
                         ], ['block' => self::SCRIPT_BOTTOM]);
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
            if (strpos($widthClass, '.') !== FALSE && strpos($widthClass, '.') == 0) {
                $widthClass = ltrim($widthClass, '.');
            } else {
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
        } else return '';
    }

    public
    function enableMasonry($selector = '.grid', $options = []) {
        $this->useScript([
                             'Scid.masonry.pkgd.min', 'Scid.imagesloaded.pkgd.min',
                         ], ['block' => self::SCRIPT_BOTTOM]);
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
            } else {
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

    public function fontCursor($selector, $icon, $options = []) {

        $this->useScript('/assets/npm-asset/jquery-awesome-cursor/dist/jquery.awesome-cursor.min', ['block' => self::SCRIPT_BOTTOM]);
        if (!empty($options)) {
            $options = ',' . json_encode($options);
        } else {
            $options = '';
        }
        $parameters = "'{$icon}'{$options}";
        $fontCursor = /** @lang JavaScript 1.8 */
            "$(document).ready(function() {
                $('{$selector}').awesomeCursor({$parameters});
});";
        $fontCursor = "$(function() {
    var canvas = document.createElement(\"canvas\");
    canvas.width = 24;
    canvas.height = 24;
    //document.body.appendChild(canvas);
    var ctx = canvas.getContext(\"2d\");
    ctx.fillStyle = \"#000000\";
    ctx.font = \"24px FontAwesome\";
    ctx.textAlign = \"center\";
    ctx.textBaseline = \"middle\";
    ctx.fillText(\"\uf002\", 12, 12);
    var dataURL = canvas.toDataURL('image/png')
    $('body').css('cursor', 'url('+dataURL+'), auto');
});";
        $this->scriptBlock($fontCursor, ['block' => self::SCRIPT_BOTTOM]);
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

    /**
     * returns an icon name for mime types
     *
     * @param $mimeType
     *
     * @return mixed
     */
    public function iconForMimeType($mimeType) {
        list($mediaType, $subType) = explode('/', $mimeType);
        $icon = $this->_mimes['default'];
        if (!empty($mediaType) && !empty($this->_mimes[$mediaType])) {
            $icon = $this->_mimes[$mediaType]['default'];
            if (!empty($subType) && !empty($this->_mimes[$mediaType][$subType])) {
                $icon = $this->_mimes[$mediaType][$subType];
            }
        }
        return $icon;
    }

    /**
     * Returns Bootstrap icon markup. By default, uses `<I>` and `fa`.
     *
     * @param string $name    Name of icon (i.e. search, leaf, etc.).
     * @param array  $options Additional HTML attributes.
     *
     * @return string HTML icon markup.
     */
    public function icon($name, array $options = []) {
        // TODO: one could be more judicious in only loading the styles requested
        $this->useScript('Scid.all', ['block' => self::SCRIPT_BOTTOM]);
        $options += [
            'tag'     => 'i',
            'iconSet' => 'fa',
            'class'   => NULL,
        ];
        if (!empty($name['counter'])) {
            $counter = $name['counter'];
            unset($name['counter']);
            if (!empty($name['size'])) {
                $size = $name['size'];
                unset($name['size']);
            }
        }
        if (is_string($name) && key_exists($name, $this->_icons)) {
            $name = $this->_icons[$name];
        }
        $classes = [];
        if ('fa' == $options['iconSet']) {
            if (is_array($name)) {
                if (!empty($name['layers'])) {
                    $layers = $name['layers'];
                    $contents = [];
                    foreach ($layers as $layer) {
                        $contents[] = $this->icon($layer);
                    }
                    $options = ['class' => ['fa-layers', 'fa-fw'], 'escape' => FALSE];
                    $icon = $this->tag('span', implode("\r",$contents) , $options);
                    if (!empty($size)) {
                        if (is_numeric($size)) {
                            $size .= 'x';
                        }
                        $icon = $this->tag('span', $icon, ['class' => 'fa-' . $size, 'escape' => FALSE]);
                    }
                    return $icon;
                } else {
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
                    } else {
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
                    if (!empty($name['inverse'])) {
                        $classes[] = 'fa-inverse';
                    }
                }
            } else {
                $classes = [$options['iconSet'], $options['iconSet'] . '-' . $name, 'fa-fw'];
            }
        }

        $option = $this->injectClasses($classes, $options);
        if (!empty($name['transform'])) {
            $option['data-fa-transform'] = $name['transform'];
        }
        if (!empty($name['mask'])) {
            $option['data-fa-mask'] = 'fas fa-' . $name['mask'];
        }
        if (empty($counter)) {
            return $this->formatTemplate('tag', [
                'tag'   => $options['tag'],
                'attrs' => $this->templater()->formatAttributes($option, ['tag', 'iconSet']),
            ]);
        } else {
            if (is_array($counter)) {
                $count = $counter['count'];
                unset($counter['count']);
                $counter += ['class' => 'fa-layers-counter',
                             'style' => 'background:green'];

            } else {
                $count = $counter;
                $counter = ['class' => 'fa-layers-counter',
                            'style' => 'background:green'];
            }
            $counter = $this->tag('span', $count, $counter);
            $icon = $this->formatTemplate('tag', [
                'tag'   => $options['tag'],
                'attrs' => $this->templater()->formatAttributes($option, ['tag', 'iconSet']),
            ]);
            $options = ['class' => ['fa-layers', 'fa-fw'], 'escape' => FALSE];
            $icon = $this->tag('span', $icon . $counter, $options);
            if (!empty($size)) {
                if (is_numeric($size)) {
                    $size .= 'x';
                }
                $icon = $this->tag('span', $icon, ['class' => 'fa-' . $size, 'escape' => FALSE]);
            }
            return $icon;

        }

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
            $this->useScript('Scid.retina.min', ['block' => self::SCRIPT_BOTTOM]);
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
     * make a nicely formatted link (support for icons)
     *
     * @param array|string $title
     * @param null         $url
     * @param array        $options
     *
     * @return string
     */
    public
    function link($title, $url = NULL, array $options = []) {
        $title = $this->titleFromOptions($title, $options);

        return parent::link($title, $url, $options);
    }

    public function map($options) {
        if (empty($this->_mapConfig)) {
            $this->_mapConfig = Configure::read('Scid.map');
        }
        if (empty($this->_mapConfig) || empty($this->_mapConfig['showMaps']) || $this->_mapConfig['showMaps'] === FALSE) {
            return '<!-- maps are disabled -->';
        }
        $this->useCssFile(
            "https://unpkg.com/leaflet@1.3.1/dist/leaflet.css",
            [
                'integrity'   => "sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ==",
                'crossorigin' => "",
            ]);
        $this->useScript(
            "https://unpkg.com/leaflet@1.3.1/dist/leaflet.js",
            [
                'integrity'   => "sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw==",
                'crossorigin' => "",
            ]);
        $center = [
            'lat' => 0,
            'lng' => 0,
        ];
        if (!empty($options['center'])) {
            $center = $options['center'];
        }
        $mapID = uniqid('map');
        if (empty($this->_mapConfig)) {
            $this->_mapConfig = Configure::read('Scid.map');
        }
        $access_token = $this->_mapConfig['accessToken'];
        $tileType = $this->_mapConfig['tileType'];
        $script = <<<MAP
var $mapID = L.map('$mapID').setView([{$center['lat']}, {$center['lng']}], 3);
            L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors, <a href=\"http://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, Imagery © <a href=\"http://mapbox.com\">Mapbox</a>',
    maxZoom: 18,
    id: '$tileType',
    accessToken: '${access_token}'
}).addTo($mapID);


MAP;
        $script2 = '';
        if (!empty($options['data'])) {
            foreach ($options['data'] as $data) {
                $title = $data['title'];
                $lat = $data['lat'];
                $lng = $data['lng'];
                $script2 =
                    $script2 . "\n" . "L.marker([$lat, $lng]).addTo($mapID).bindPopup('$title');";
            }
        }
        $this->scriptBlock($script . $script2, ['block' => self::SCRIPT_BOTTOM]);

        return $this->div('map', '', ['id' => $mapID]);
    }

    /**
     * @param       $class
     * @param array $options {
     *                       byRow: true,
     *                       property: 'height',
     *                       target: null,
     *                       remove: false
     *                       }
     *
     * @return void
     */
    public
    function matchHeight($class, $options = []) {
        $this->useScript('Scid.jquery.matchHeight-min', ['block' => self::SCRIPT_BOTTOM]);
        $optionsJson = '';
        if (!empty($options)) {
            $optionsJson = json_encode($options);
        }
        $this->scriptBlock("$(function() {
                $('.${class}').matchHeight(${optionsJson});
            });", ['block' => self::SCRIPT_BOTTOM,]);
    }

    /**
     * format a phone number
     *
     * @param       $phone
     *
     * @return mixed
     */
    public
    function phone($phone) {
        $phone = preg_replace("/[^0-9]/", "", $phone);

        if (strlen($phone) == 7) {
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        } else if (strlen($phone) == 10) {
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
        } else {
            return $phone;
        }
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
        } else {
            $title = $this->phone($phone);
        }
        if (!isset($options['icon'])) {
            if (!empty($phone->type) && $phone->type->name == 'Cell') {
                $options['icon'] = 'cell';
            } else {
                $options['icon'] = 'phone';
            }
        }

        $url = 'tel:' . $phone;

        return $this->button($title, $url, $options);
    }

    public function breadcrumbOptions($brand) {
        $breadcrumbClass = ['breadcrumb-item'];
        if (isset($brand['link-color'])) {
            $breadcrumbClass[] = $brand['link-color'];
        } else if (isset($brand['text-color'])) {
            $breadcrumbClass[] = $brand['text-color'];
        }
        $innerAttributes = [];
        if (isset($brand['link-color'])) {
            $innerAttributes['class'][] = $brand['link-color'];
        } else if (isset($brand['text-color'])) {
            $innerAttributes['class'][] = $brand['text-color'];
        }
        $options = ['class' => $breadcrumbClass];
        if (!empty($innerAttributes)) {
            $options['innerAttrs'] = $innerAttributes;
        }

        return $options;
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
    public
    function popover($linkTitle, $title, $content = NULL, $options = [], $popoverOptions = []) {
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
        $linkTitle = $this->titleFromOptions($linkTitle, $options);

        return $this->tag($tag, $linkTitle, $options);
    }

    /**
     * @param   string $title
     * @param array    $options
     *
     * @return string
     */
    public
    function titleFromOptions($title, array &$options) {
        if (!empty($options['icon'])) {
            if (is_string($options['icon'])) {
                $icon = ['icon' => $options['icon']];
            } else {
                $icon = $options['icon'];
            }
            $icon += [
                'icon-class'  => 'd-md-none d-lg-inline',
                'title-class' => 'd-none d-md-inline',
            ];

            // add icon to left of title
            $title =
                $this->icon($options['icon'], ['class' => $icon['icon-class']]) . '<span class="' . $icon['title-class'] . '"> ' . $title . '</span>';
            unset($options['icon']);
            $options['escape'] = FALSE;
        }

        return $title;
    }

    public
    function tooltip() {

        $enableToolTip = <<<ENABLETOOLTIP
 $(function () {
        $('[data-toggle="tooltip"]').tooltip()
})
ENABLETOOLTIP;
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
     *
     * @return string|null CSS `<link />` or `<style />` tag, depending on the type of link.
     * @link https://book.cakephp.org/3.0/en/views/helpers/html.html#linking-to-css-files
     */
    public
    function useCssFile($paths, array $options = []) {
        if ($this->_isCell) {
            $SCID_CSS_PATHS = 'Cell.' . self::SCID_CSS_PATHS;
        } else {
            $SCID_CSS_PATHS = self::SCID_CSS_PATHS;
        }

        $existingPaths = Configure::read($SCID_CSS_PATHS);
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
        Configure::write($SCID_CSS_PATHS, $existingPaths);
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
     *
     * @return string|null String of `<script />` tags or null if block is specified in options
     *                              or if $once is true and the file has been included before.
     * @link https://book.cakephp.org/3.0/en/views/helpers/html.html#linking-to-javascript-files
     */

    public
    function useScript($urls, array $options = []) {
        if ($this->_isCell) {
            $SCID_SCRIPT_URLS = 'Cell.' . self::SCID_SCRIPT_URLS;
        } else {
            $SCID_SCRIPT_URLS = self::SCID_SCRIPT_URLS;
        }

        $existingUrls = Configure::read($SCID_SCRIPT_URLS);
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
        Configure::write($SCID_SCRIPT_URLS, $existingUrls);
    }

    protected
    function enablePopovers() {
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

    private
    function buildJSArray($array = []) {
        $json = json_encode($array, JSON_PRETTY_PRINT, 2);

        return $json;
    }
}

