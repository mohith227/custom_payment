<?php

namespace Mohith\CustomPayment\Model;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Custom payment method model
 */
class PaymentMethod extends AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     **/
    protected $_code = 'custompayment';
}
