<?php

namespace Mohith\CustomPayment\Model\Adminhtml\System\Config\Source\Customer;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

class Group implements OptionSourceInterface
{

    protected $_options;
    /**
     * @var CollectionFactory
     */
    protected $_groupCollectionFactory;

    /**
     * @param CollectionFactory $groupCollectionFactory
     */
    public function __construct
    (
        CollectionFactory $groupCollectionFactory
    )
    {
        $this->_groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupCollectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
