<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 6/6/18
     * Time: 5:20 PM
     */
    namespace Scid\View;

    use Cake\View\View;
    use Cake\Datasource\EntityInterface;
    use Cake\Event\EventManager;
    use Cake\Network\Request;
    use Cake\Network\Response;
    use Cake\Utility\Hash;
    use Exception;

    class SpreadsheetViewView extends View
    {

        public function __construct(
            Request $request = null,
            Response $response = null,
            EventManager $eventManager = null,
            array $viewOptions = []
        ) {
            parent::__construct($request, $response, $eventManager, $viewOptions);

            if ($response && $response instanceof Response) {
                $response->type('csv');
            }
        }


        public function render($view = null, $layout = null)
        {
            // Custom logic here.
        }
    }
