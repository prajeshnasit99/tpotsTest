<?php
/**
 * TPots_JaPrajeshNasit – Load More PLP module
 */

declare(strict_types=1);

namespace TPots\JaPrajeshNasit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/* Class Data */
class Data extends AbstractHelper
{
    public const XML_PATH_ENABLE = 'japrajesh_loadmore/general/enable';

    /** Constructor */
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Check if Load More is enabled for the given store scope.
     *
     * @param int|string|null $storeId
     * @return bool
     */
    public function isLoadMoreEnabled($storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
