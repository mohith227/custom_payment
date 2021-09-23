<?php

namespace Mohith\CustomPayment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mohith\CustomPayment\Model\Adminhtml\System\Config;
use Psr\Log\LoggerInterface;

class EnablePaymentMethodByCustomerGroup implements ObserverInterface
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config          $config,
        LoggerInterface $logger
    )
    {
        $this->config = $config;
        $this->_logger = $logger;
    }

    /**
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        try {
            if ($this->config->getIsActive()) {
                $result = $observer->getEvent()->getResult();
                $method_instance = $observer->getEvent()->getMethodInstance();
                $quote = $observer->getEvent()->getQuote();
                $customerGroups = explode(",", $this->config->getCustomerGrouplist());
                if (null !== $quote) {
                    $customerGroupId = $quote->getCustomerGroupId();
                    /* If Customer group is doesn't match then we will disable payment gateway */
                    if (!in_array($customerGroupId, $customerGroups) && $method_instance->getCode() == 'custompayment') {
                        /* Disable payment gateway */
                        $result->setData('is_available', false);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }

    }
}
