<?php
/**
 * TPots_JaPrajeshNasitCheckout – blocked shipping and payment methods
 */

namespace TPots\JaPrajeshNasitCheckout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Helper\Data;

/* Class PaymentMethods */
class PaymentMethods implements OptionSourceInterface
{
    /**
     * @var Data
     */
    protected Data $paymentHelper;

    /**
     * Constructor
     *
     * @param Data $paymentHelper
     */
    public function __construct(
        Data $paymentHelper
    ) {
        $this->paymentHelper = $paymentHelper;
    }

    /**
     * Return payment methods as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $methods = $this->paymentHelper->getPaymentMethods();

        foreach ($methods as $code => $method) {

            if (!isset($method['title'])) {
                continue;
            }

            $options[] = [
                'value' => $code,
                'label' => $method['title']
            ];
        }

        return $options;
    }
}