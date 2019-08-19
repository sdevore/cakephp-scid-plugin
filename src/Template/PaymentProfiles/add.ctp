<?php
/**
 * @var \App\View\AppView $this
 * @var \Scid\Model\Entity\PaymentProfile $paymentProfile
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('List Payment Profiles'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Members'), ['controller' => 'Members', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Member'), ['controller' => 'Members', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Customer Profiles'), ['controller' => 'CustomerProfiles', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Customer Profile'), ['controller' => 'CustomerProfiles', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="paymentProfiles form large-9 medium-8 columns content">
    <?= $this->Form->create($paymentProfile) ?>
    <fieldset>
        <legend><?= __('Add Payment Profile') ?></legend>
        <?php
            echo $this->Form->control('member_id', ['options' => $members]);
            echo $this->Form->control('customer_profile_id', ['options' => $customerProfiles]);
            echo $this->Form->control('payment_profile_id');
            echo $this->Form->control('is_default');
            echo $this->Form->control('card_number');
            echo $this->Form->control('expiration_date');
            echo $this->Form->control('card_type');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
