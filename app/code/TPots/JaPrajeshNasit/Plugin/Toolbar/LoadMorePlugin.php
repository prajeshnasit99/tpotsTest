<?php
/**
 * TPots_JaPrajeshNasit – Load More PLP module
 */

declare(strict_types=1);

namespace TPots\JaPrajeshNasit\Plugin\Toolbar;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use TPots\JaPrajeshNasit\Helper\Data;

/* Class LoadMorePlugin */
class LoadMorePlugin
{
    /**
     * @var Data
     */
    private Data $helper;

    /**
     * Constructor
     *
     * @param Data $helper
     */
    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * When Load More is enabled and we are on the bottom toolbar,
     * suppress the default pager HTML so pagination links disappear.
     *
     * @param Toolbar  $subject
     * @param callable $proceed
     * @return string
     */
    public function aroundGetPagerHtml(Toolbar $subject, callable $proceed): string
    {
        if ($this->helper->isLoadMoreEnabled() && $subject->getIsBottom()) {
            return '';
        }

        return $proceed();
    }
}
