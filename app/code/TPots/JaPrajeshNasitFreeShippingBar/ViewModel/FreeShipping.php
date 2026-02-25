<?php

namespace TPots\JaPrajeshNasitFreeShippingBar\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/* Class FreeShipping
 */
class FreeShipping implements ArgumentInterface
{
    private const XML_PATH_FREE_SHIPPING_MIN_AMOUNT =
        'carriers/freeshipping/free_shipping_subtotal';

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Constructor
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get Free Shipping Minimum Amount from Admin Config
     */
    public function getFreeShippingThreshold(): float
    {
        return (float)$this->scopeConfig->getValue(
            self::XML_PATH_FREE_SHIPPING_MIN_AMOUNT,
            ScopeInterface::SCOPE_STORE
        );
    }
}