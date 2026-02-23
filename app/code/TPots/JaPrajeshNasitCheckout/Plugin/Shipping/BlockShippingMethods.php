<?php
/**
 * Plugin to remove configured shipping carrier
 * if cart contains restricted product
 * TPots_JaPrajeshNasitCheckout – blocked shipping methods
 */

namespace TPots\JaPrajeshNasitCheckout\Plugin\Shipping;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/* Class BlockShippingMethods */
class BlockShippingMethods
{
    /**
     * @var Session
     */
    private Session $checkoutSession;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * Constructor
     *
     * @param Session $checkoutSession
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Session $checkoutSession,
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
    }

    /**
     * Return available shipping methods, remove blocked carrier if quote has restricted product
     *
     * @return array
     */
    public function afterCollectRates(
        \Magento\Shipping\Model\Shipping $subject,
        $result
    ) {
        $quote = $this->checkoutSession->getQuote();

        if (!$this->hasBlockedProduct($quote)) {
            return $result;
        }

        $blockedCarrier = $this->scopeConfig->getValue(
            'tpots_checkout/general/shipping_method',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $quote->getStoreId()
        );

        $rateResult = $subject->getResult();

        if (!$rateResult) {
            return $result;
        }

        $allowedRates = [];

        foreach ($rateResult->getAllRates() as $rate) {
            if ($rate->getCarrier() !== $blockedCarrier) {
                $allowedRates[] = $rate;
            }
        }

        // Clear all existing rates
        $rateResult->reset();

        // Add back allowed ones
        foreach ($allowedRates as $rate) {
            $rateResult->append($rate);
        }

        return $result;
    }

    /**
     * Return true if quote has product with block_payment_shipping attribute set
     *
     * @return bool
     */
    private function hasBlockedProduct($quote): bool
    {
        foreach ($quote->getAllVisibleItems() as $item) {

            $product = $this->productRepository->getById(
                $item->getProductId(),
                false,
                $quote->getStoreId()
            );

            if ($product->getData('block_payment_shipping')) {
                return true;
            }
        }
        return false;
    }
}