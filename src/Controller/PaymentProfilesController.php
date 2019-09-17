<?php
namespace Scid\Controller;

use Scid\Controller\AppController;

/**
 * PaymentProfiles Controller
 *
 * @property \Scid\Model\Table\PaymentProfilesTable $PaymentProfiles
 *
 * @method \Scid\Model\Entity\PaymentProfile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PaymentProfilesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Members', 'CustomerProfiles']
        ];
        $paymentProfiles = $this->paginate($this->PaymentProfiles);

        $this->set(compact('paymentProfiles'));
    }

    /**
     * View method
     *
     * @param string|null $id Payment Profile id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $paymentProfile = $this->PaymentProfiles->get($id, [
            'contain' => ['Members', 'CustomerProfiles']
        ]);

        $this->set('paymentProfile', $paymentProfile);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $paymentProfile = $this->PaymentProfiles->newEntity();
        if ($this->request->is('post')) {
            $paymentProfile = $this->PaymentProfiles->patchEntity($paymentProfile, $this->request->getData());
            if ($this->PaymentProfiles->save($paymentProfile)) {
                $this->Flash->success(__('The payment profile has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payment profile could not be saved. Please, try again.'));
        }
        $members = $this->PaymentProfiles->Members->find('list', ['limit' => 200]);
        $customerProfiles = $this->PaymentProfiles->CustomerProfiles->find('list', ['limit' => 200]);
        $this->set(compact('paymentProfile', 'members', 'customerProfiles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Payment Profile id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $paymentProfile = $this->PaymentProfiles->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $paymentProfile = $this->PaymentProfiles->patchEntity($paymentProfile, $this->request->getData());
            if ($this->PaymentProfiles->save($paymentProfile)) {
                $this->Flash->success(__('The payment profile has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payment profile could not be saved. Please, try again.'));
        }
        $members = $this->PaymentProfiles->Members->find('list', ['limit' => 200]);
        $customerProfiles = $this->PaymentProfiles->CustomerProfiles->find('list', ['limit' => 200]);
        $this->set(compact('paymentProfile', 'members', 'customerProfiles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Payment Profile id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $paymentProfile = $this->PaymentProfiles->get($id);
        if ($this->PaymentProfiles->delete($paymentProfile)) {
            $this->Flash->success(__('The payment profile has been deleted.'));
        } else {
            $this->Flash->error(__('The payment profile could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
