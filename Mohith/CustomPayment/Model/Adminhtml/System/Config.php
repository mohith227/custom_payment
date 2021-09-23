<?php

namespace Mohith\CustomPayment\Model\Adminhtml\System;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const XML_GROUP = "custompayment";

    const XML_SECTION = "payment";

    const XML_FIELD = "active";

    /**
     * ScopeConfigInterface
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Array
     *
     * @var array
     */
    private $data;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $data = []
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->data = $data;
    }

    /**
     * Get CustomerGroupList
     *
     * @return false|mixed
     */
    public function getCustomerGrouplist()
    {
        return $this->getValue("customer_group_list");
    }
    /**
     * GetValue
     *
     * @param $field
     * @param string $group
     * @param string $section
     * @param string $scope
     * @param bool $validateIsActive
     * @return false|mixed
     */
    public function getValue(
        $field,
        $group = self::XML_GROUP,
        $section = self::XML_SECTION,
        $scope = ScopeInterface::SCOPE_STORE,
        $validateIsActive = true
    )
    {
        $path = $section . '/' . $group . '/' . $field;
        if (!array_key_exists($path . $scope, $this->data)) {
            $this->data[$path . $scope] = $validateIsActive &&
            !$this->getIsActive() ? false : $this->scopeConfig
                ->getValue($path, $scope);
        }

        return $this->data[$path . $scope];
    }

    /**
     * Is Active
     *
     * @return bool
     */
    public function getIsActive()
    {
        return (bool)$this->getValue(
            self::XML_FIELD,
            self::XML_GROUP,
            self::XML_SECTION,
            ScopeInterface::SCOPE_STORE,
            false
        );
    }
}
