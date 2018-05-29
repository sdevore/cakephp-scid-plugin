<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/28/18
     * Time: 8:17 PM
     */

    namespace Scid\View\Widget;

    use Cake\View\Form\ContextInterface;
    use Cake\View\StringTemplate;
    use Cake\View\Widget\WidgetInterface;

    class SignatureWidget implements WidgetInterface
    {

        protected $_templates;

        public function __construct(StringTemplate $templates) {
            $templates->add([
                                'signatureFormGroup' => '{{label}}<div class="signatureFormGroup bg-danger %s">{{input}}{{error}}{{help}}</div>',
                                'signatureContainer' => '<div id="signature-container" class="bg-danger"></div><div class="signatureContainer {{type}}{{required}}">{{content}}</div>',
                                'signature'          => '<input type="{{type}}" name="{{name}}"{{attrs}}/>',
                            ]);

            $this->_templates = $templates;
        }

        public function render(array $data, ContextInterface $context) {
            $data += [
                'name' => '',
            ];

            return $this->_templates->format('signature', [
                'name'  => $data['name'],
                'attrs' => $this->_templates->formatAttributes($data, ['name']),
            ]);
        }

        public function secureFields(array $data) {
            return [$data['name']];
        }
    }
