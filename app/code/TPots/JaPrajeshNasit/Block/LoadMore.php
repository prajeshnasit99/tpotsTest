<?php
/**
 * TPots_JaPrajeshNasit – Load More PLP module
 */

declare(strict_types=1);

namespace TPots\JaPrajeshNasit\Block;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use TPots\JaPrajeshNasit\Helper\Data;

/* Class LoadMore */
class LoadMore extends Template
{
    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @var Toolbar
     */
    private Toolbar $toolbar;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Data $helper 
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * Get the Toolbar block from the parent ListProduct block.
     *
     * @return Toolbar|null
     */
    private function getToolbarBlock()
    {
        $parent = $this->getParentBlock();
        if ($parent && $parent instanceof \Magento\Catalog\Block\Product\ListProduct) {
            return $parent->getToolbarBlock();
        }
        
        // Fallback: Try to find the toolbar block in the layout if not a direct child
        return $this->getLayout()->getBlock('product_list_toolbar');
    }

    /**
     * Whether Load More is enabled in admin config.
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->helper->isLoadMoreEnabled();
    }

    /**
     * Current page number from the toolbar collection.
     * Returns 1 if toolbar or collection is not available (e.g. on cache warmup).
     */
    public function getCurrentPage(): int
    {
        $toolbar = $this->getToolbarBlock();
        $collection = $toolbar ? $toolbar->getCollection() : null;
        if ($collection) {
            return (int) $collection->getCurPage();
        }
        return 1;
    }

    /**
     * Total pages based on current per-page limit.
     * Returns 1 if toolbar or collection is not available (e.g. on cache warmup).
     */
    public function getTotalPages(): int
    {
        $toolbar = $this->getToolbarBlock();
        $collection = $toolbar ? $toolbar->getCollection() : null;
        if ($collection) {
            $size  = (int) $collection->getSize();
            $limit = (int) $toolbar->getLimit();
            if ($limit > 0) {
                return (int) ceil($size / $limit);
            }
        }
        return 1;
    }

    /**
     * Current per-page limit.
     *  Returns 12 if toolbar is not available (e.g. on cache warmup) – this is also the default limit in Magento, so it’s a safe fallback.
     */
    public function getPageLimit(): int
    {
        $toolbar = $this->getToolbarBlock();
        return $toolbar ? (int) $toolbar->getLimit() : 12;
    }

    /**
     * AJAX endpoint URL base (without page/limit params – those are appended by JS).
     */
    public function getLoadMoreUrl(): string
    {
        return $this->getUrl('japrajesh/catalog/loadmore');
    }
}
