<?php
/**
 * Source model to fetch active shipping carriers
 *
 * @package  TPots_JaPrajeshNasitCheckout
 */

namespace TPots\JaPrajeshNasitCheckout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Shipping\Model\Config as ShippingConfig;

/* Class ShippingMethods */
class ShippingMethods implements OptionSourceInterface
{
    /**
     * @var ShippingConfig
     */
    private ShippingConfig $shippingConfig;

    /**
     * Constructor
     *
     * @param ShippingConfig $shippingConfig
     */
    public function __construct(
        ShippingConfig $shippingConfig
    ) {
        $this->shippingConfig = $shippingConfig;
    }

    /**
     * Return active shipping carriers
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];

        try {
            $carriers = $this->shippingConfig->getActiveCarriers();

            foreach ($carriers as $carrierCode => $carrierModel) {

                $title = $carrierModel->getConfigData('title');

                if (!$title) {
                    continue;
                }

                $options[] = [
                    'value' => $carrierCode,
                    'label' => $title
                ];
            }
        } catch (\Exception $e) {
            // Prevent admin config
            return [];
        }

        return $options;
    }
}