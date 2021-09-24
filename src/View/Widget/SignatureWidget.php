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
                                'signatureFormGroup' => '<div class="signatureLabel">{{label}}</div><div class="signatureFormGroup {{class}}">{{input}}{{error}}{{help}}</div>',
                                'signatureContainer' => '<div id="{{id}}" class="signatureContainer {{containerClass}} {{type}}{{required}}">
      <canvas id="{{canvasId}}" class="signature-pad-canvas {{canvasClass}}"></canvas>
    {{content}}</div>',
                                'signature'          => '<input type="hidden" name="{{name}}"{{attrs}} signature/>',
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
