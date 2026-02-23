<?php
/**
 * Plugin to remove configured payment method
 * if cart contains restricted product
 * TPots_JaPrajeshNasitCheckout – blocked payment methods
 */

namespace TPots\JaPrajeshNasitCheckout\Plugin\Payment;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/* Class BlockPaymentMethods */
class BlockPaymentMethods
{
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
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ProductRepositoryInterface $productRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
    }

    /**
     * Return available payment methods, remove blocked method if quote has restricted product
     *
     * @return array
     */
    public function afterGetAvailableMethods(
        \Magento\Payment\Model\MethodList $subject,
        array $result,
        CartInterface $quote = null
    ): array {
        if (!$quote || !$this->hasBlockedProduct($quote)) {
            return $result;
        }

        $blockedMethod = $this->scopeConfig->getValue(
            'tpots_checkout/general/payment_method',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $quote->getStoreId()
        );

        foreach ($result as $key => $method) {
            if ($method->getCode() === $blockedMethod) {
                unset($result[$key]);
            }
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