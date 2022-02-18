<?php

    namespace Scid\Utility;

    use App\Model\Entity\Payment;
    use Cake\Chronos\Date;
    use Cake\Core\Configure;
    use Cake\Datasource\EntityInterface;
    use Cake\Filesystem\File;
    use Cake\I18n\FrozenDate;
    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;
    use Scid\Model\Entity\CustomerProfile;
    use Scid\Model\Entity\PaymentProfile;


    interface ScidPaymentsInterface
    {

        const TRANSACTION_ID_PREFIX_KEY = 'id-prefix';
        const TRANSACTION_TRANSACTION_KEY_KEY = 'transaction-key';
        const TRANSACTION_TYPE_KEY = 'transactionType';
        const TRANSACTION_TYPE_AUTHORIZE = 'authOnlyTransaction';
        const TRANSACTION_TYPE_AUTH_CAPTURE = 'authCaptureTransaction';
        const TRANSACTION_TYPE_CAPTURE = 'priorAuthCaptureTransaction';
        const TRANSACTION_TYPE_VOID = 'voidTransaction';
        const TRANSACTION_TYPE_REFUND = 'refundTransaction';

        const STATE_PENDING = 'Pending';
        const STATE_APPROVED = 'Approved';
        const STATE_FAILED = 'Failed';
        const STATE_CAPTURED = 'Captured';
        const STATE_SETTLED = 'Settled';
        const STATE_VOIDED = 'Voided';
        const STATE_REFUNDED = 'Refunded';

    }
