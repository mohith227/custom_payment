<?php

namespace Mohith\CustomPayment\Preference\CustomerData;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Checkout\CustomerData\AbstractItem;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Msrp\Helper\Data;

/**
 * Default cart item
 */
class DefaultItem extends AbstractItem
{
    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * @var Data
     */
    protected $msrpHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ConfigurationPool
     */
    protected $configurationPool;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $checkoutHelper;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var ItemResolverInterface
     */
    private $itemResolver;

    /**
     * @param Image $imageHelper
     * @param Data $msrpHelper
     * @param UrlInterface $urlBuilder
     * @param ConfigurationPool $configurationPool
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param Escaper|null $escaper
     * @param ItemResolverInterface|null $itemResolver
     * @codeCoverageIgnore
     */
    public function __construct(
        Image $imageHelper,
        Data $msrpHelper,
        UrlInterface $urlBuilder,
        ConfigurationPool $configurationPool,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        Escaper $escaper = null,
        ItemResolverInterface $itemResolver = null
    ) {
        $this->configurationPool = $configurationPool;
        $this->imageHelper = $imageHelper;
        $this->msrpHelper = $msrpHelper;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutHelper = $checkoutHelper;
        $this->escaper = $escaper ?: ObjectManager::getInstance()->get(Escaper::class);
        $this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(ItemResolverInterface::class);
    }

    /**
     * @inheritdoc
     */
    protected function doGetItemData()
    {
        $imageHelper = $this->imageHelper->init($this->getProductForThumbnail(), 'mini_cart_product_thumbnail');
        $productName = $this->escaper->escapeHtml($this->item->getProduct()->getName());
        $optionId = $this->item->getProduct()->getData('net');
        $attr = $this->item->getProduct()->getResource()->getAttribute('net');
        if ($attr->usesSource()) {
            $optionText = $attr->getSource()->getOptionText($optionId);
        }
        return [
            'options' => $this->getOptionList(),
            'qty' => $this->item->getQty() * 1,
            'item_id' => $this->item->getId(),
            'configure_url' => $this->getConfigureUrl(),
            'is_visible_in_site_visibility' => $this->item->getProduct()->isVisibleInSiteVisibility(),
            'product_id' => $this->item->getProduct()->getId(),
            'product_name' => $productName,
            'product_sku' => $this->item->getProduct()->getSku(),
            'product_url' => $this->getProductUrl(),
            'product_has_url' => $this->hasProductUrl(),
            'product_price' => $this->checkoutHelper->formatPrice($this->item->getCalculationPrice()),
            'product_price_value' => $this->item->getCalculationPrice(),
            'net' => $optionText,
            'product_image' => [
                'src' => $imageHelper->getUrl(),
                'alt' => $imageHelper->getLabel(),
                'width' => $imageHelper->getWidth(),
                'height' => $imageHelper->getHeight(),
            ],
            'canApplyMsrp' => $this->msrpHelper->isShowBeforeOrderConfirm($this->item->getProduct())
                && $this->msrpHelper->isMinimalPriceLessMsrp($this->item->getProduct()),
            'message' => $this->item->getMessage(),
        ];
    }

    /**
     * Get list of all options for product
     *
     * @return array
     * @codeCoverageIgnore
     */
    protected function getOptionList()
    {
        return $this->configurationPool->getByProductType($this->item->getProductType())->getOptions($this->item);
    }

    /**
     * Returns product for thumbnail.
     *
     * @return Product
     * @codeCoverageIgnore
     */
    protected function getProductForThumbnail()
    {
        return $this->itemResolver->getFinalProduct($this->item);
    }

    /**
     * Returns product.
     *
     * @return Product
     * @codeCoverageIgnore
     */
    protected function getProduct()
    {
        return $this->item->getProduct();
    }

    /**
     * Get item configure url
     *
     * @return string
     */
    protected function getConfigureUrl()
    {
        return $this->urlBuilder->getUrl(
            'checkout/cart/configure',
            ['id' => $this->item->getId(), 'product_id' => $this->item->getProduct()->getId()]
        );
    }

    /**
     * Check Product has URL
     *
     * @return bool
     */
    protected function hasProductUrl()
    {
        if ($this->item->getRedirectUrl()) {
            return true;
        }

        $product = $this->item->getProduct();
        $option = $this->item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        if ($product->isVisibleInSiteVisibility()) {
            return true;
        } else {
            if ($product->hasUrlDataObject()) {
                $data = $product->getUrlDataObject();
                if (in_array($data->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    protected function getProductUrl()
    {
        if ($this->item->getRedirectUrl()) {
            return $this->item->getRedirectUrl();
        }

        $product = $this->item->getProduct();
        $option = $this->item->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        return $product->getUrlModel()->getUrl($product);
    }
}
