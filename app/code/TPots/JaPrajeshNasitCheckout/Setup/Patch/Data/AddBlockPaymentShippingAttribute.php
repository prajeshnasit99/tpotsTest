<?php
/**
 * Data Patch to create product attribute:
 * block_payment_shipping
 * TPots_JaPrajeshNasitCheckout – blocked shipping and payment methods
 */


namespace TPots\JaPrajeshNasitCheckout\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/* Class AddBlockPaymentShippingAttribute */
class AddBlockPaymentShippingAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private EavSetupFactory $eavSetupFactory;

    /**
     * Constructor
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Apply Data Patch
     *
     * @return void
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create([
            'setup' => $this->moduleDataSetup
        ]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'block_payment_shipping',
            [
                'type' => 'int',
                'label' => 'Block Payment and Shipping',
                'input' => 'boolean',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'default' => 0,
                'required' => false,
                'visible' => true,
                'user_defined' => true,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General'
            ]
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Get dependencies
     *
     * @return array
     */
    public static function getDependencies(): array { return []; }

    /**
     * Get aliases
     *
     * @return array
     */
    public function getAliases(): array { return []; }
}