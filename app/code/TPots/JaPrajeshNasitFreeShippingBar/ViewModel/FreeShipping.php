<?php

namespace TPots\JaPrajeshNasitFreeShippingBar\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/* Class FreeShipping
 */
class FreeShipping implements ArgumentInterface
{
    private const XML_PATH_FREE_SHIPPING_MIN_AMOUNT =
        'carriers/freeshipping/free_shipping_subtotal';

    /**
     * @var checkoutSession
     */
    private CheckoutSession $checkoutSession;

    /**
     * @var PriceHelper
     */
    private PriceHelper $priceHelper;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * Constructor
     *
     * @param CheckoutSession $checkoutSession
     * @param PriceHelper $priceHelper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        PriceHelper $priceHelper,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->priceHelper = $priceHelper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get cart subtotal
     */
    public function getSubtotal(): float
    {
        $quote = $this->checkoutSession->getQuote();
        return (float)$quote->getSubtotal();
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

    /**
     * Get Free Shipping Message based on cart subtotal and free shipping threshold
     */
    public function getFreeShippingMessage(): string
    {
        $subtotal  = $this->getSubtotal();
        $threshold = $this->getFreeShippingThreshold();

        if ($threshold <= 0) {
            return '';
        }

        if ($subtotal <= 0) {
            return __('Free Shipping on orders above %1',
                $this->priceHelper->currency($threshold, true, false)
            );
        }

        if ($subtotal < $threshold) {
            $difference = $threshold - $subtotal;

            return __('You’re %1 away from Free Shipping',
                $this->priceHelper->currency($difference, true, false)
            );
        }

        return __('Free shipping is applied to your order');
    }
}