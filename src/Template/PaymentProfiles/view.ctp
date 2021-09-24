<?php
/**
 * @var \App\View\AppView $this
 * @var \Scid\Model\Entity\PaymentProfile $paymentProfile
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Payment Profile'), ['action' => 'edit', $paymentProfile->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Payment Profile'), ['action' => 'delete', $paymentProfile->id], ['confirm' => __('Are you sure you want to delete # {0}?', $paymentProfile->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Payment Profiles'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Payment Profile'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Customer Profiles'), ['controller' => 'CustomerProfiles', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Customer Profile'), ['controller' => 'CustomerProfiles', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="paymentProfiles view large-9 medium-8 columns content">
    <h3><?= h($paymentProfile->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= h($paymentProfile->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Member') ?></th>
            <td><?= $paymentProfile->has('member') ? $this->Html->link($paymentProfile->member->name, ['controller' => 'Members', 'action' => 'view', $paymentProfile->member->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Customer Profile') ?></th>
            <td><?= $paymentProfile->has('customer_profile') ? $this->Html->link($paymentProfile->customer_profile->id, ['controller' => 'CustomerProfiles', 'action' => 'view', $paymentProfile->customer_profile->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Payment Profile Id') ?></th>
            <td><?= h($paymentProfile->payment_profile_id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Card Number') ?></th>
            <td><?= h($paymentProfile->card_number) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Expiration Date') ?></th>
            <td><?= h($paymentProfile->expiration_date) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Card Type') ?></th>
            <td><?= h($paymentProfile->card_type) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($paymentProfile->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($paymentProfile->modified) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Is Default') ?></th>
            <td><?= $paymentProfile->is_default ? __('Yes') : __('No'); ?></td>
        </tr>
    </table>
</div>
