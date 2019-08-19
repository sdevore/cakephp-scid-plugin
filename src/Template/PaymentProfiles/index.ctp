<?php
/**
 * @var \App\View\AppView $this
 * @var \Scid\Model\Entity\PaymentProfile[]|\Cake\Collection\CollectionInterface $paymentProfiles
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Payment Profile'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Customer Profiles'), ['controller' => 'CustomerProfiles', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Customer Profile'), ['controller' => 'CustomerProfiles', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="paymentProfiles index large-9 medium-8 columns content">
    <h3><?= __('Payment Profiles') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col"><?= $this->Paginator->sort('member_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('customer_profile_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('payment_profile_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('is_default') ?></th>
                <th scope="col"><?= $this->Paginator->sort('card_number') ?></th>
                <th scope="col"><?= $this->Paginator->sort('expiration_date') ?></th>
                <th scope="col"><?= $this->Paginator->sort('card_type') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paymentProfiles as $paymentProfile): ?>
            <tr>
                <td><?= h($paymentProfile->id) ?></td>
                <td><?= h($paymentProfile->created) ?></td>
                <td><?= h($paymentProfile->modified) ?></td>
                <td><?= $paymentProfile->has('member') ? $this->Html->link($paymentProfile->member->name, ['controller' => 'Members', 'action' => 'view', $paymentProfile->member->id]) : '' ?></td>
                <td><?= $paymentProfile->has('customer_profile') ? $this->Html->link($paymentProfile->customer_profile->id, ['controller' => 'CustomerProfiles', 'action' => 'view', $paymentProfile->customer_profile->id]) : '' ?></td>
                <td><?= h($paymentProfile->payment_profile_id) ?></td>
                <td><?= h($paymentProfile->is_default) ?></td>
                <td><?= h($paymentProfile->card_number) ?></td>
                <td><?= h($paymentProfile->expiration_date) ?></td>
                <td><?= h($paymentProfile->card_type) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $paymentProfile->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $paymentProfile->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $paymentProfile->id], ['confirm' => __('Are you sure you want to delete # {0}?', $paymentProfile->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
