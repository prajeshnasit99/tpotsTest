<?php
/**
 * TPots_JaPrajeshNasit – Load More PLP module
 */

declare(strict_types=1);

namespace TPots\JaPrajeshNasit\Controller\Catalog;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use TPots\JaPrajeshNasit\Helper\Data;

/* Class LoadMore */
class LoadMore implements HttpGetActionInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var JsonFactory
     */
    private JsonFactory $jsonFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    private CategoryRepositoryInterface $categoryRepository;

    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @var PageFactory
     */
    private PageFactory $resultPageFactory;

    /**
     * Constructor
     *
     * @param RequestInterface $request
     * @param JsonFactory $jsonFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $helper
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        RequestInterface $request,
        JsonFactory $jsonFactory,
        CategoryRepositoryInterface $categoryRepository,
        Data $helper,
        Registry $registry,
        PageFactory $resultPageFactory
    ) {
        $this->request            = $request;
        $this->jsonFactory        = $jsonFactory;
        $this->categoryRepository = $categoryRepository;
        $this->helper             = $helper;
        $this->registry           = $registry;
        $this->resultPageFactory  = $resultPageFactory;
    }

    /**
     * Execute AJAX request and return rendered product items HTML + pagination meta.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();

        if (!$this->helper->isLoadMoreEnabled()) {
            return $result->setData(['error' => 'Load More is disabled.']);
        }

        $categoryId = (int)$this->request->getParam('cat_id');
        $searchQuery = $this->request->getParam('q');
        $page = max(1, (int)$this->request->getParam('p', 1));
        $limit = (int)$this->request->getParam('limit', 12);
        $isSearch = !empty($searchQuery);

        try {

            if ($categoryId && !$isSearch) {
                $category = $this->categoryRepository->get($categoryId);
                $this->registry->unregister('current_category');
                $this->registry->register('current_category', $category);
            }

            $resultPage = $this->resultPageFactory->create();

            if ($isSearch) {
                $resultPage->addHandle('catalogsearch_result_index');
            } else {
                $resultPage->addHandle('catalog_category_view');
                $resultPage->addHandle('catalog_category_view_type_default');
                $resultPage->addHandle('catalog_category_view_type_layered');
            }

            $layout = $resultPage->getLayout();

            $listBlock = $layout->getBlock(
                $isSearch ? 'search_result_list' : 'category.products.list'
            );

            if (!$listBlock instanceof ListProduct) {
                return $result->setData([
                    'error' => 'Product list block not found.'
                ]);
            }

            $collection = $listBlock->getLoadedProductCollection();
            $collection->setCurPage($page);
            $collection->setPageSize($limit);

            $listBlock->setTemplate(
                'TPots_JaPrajeshNasit::catalog/product/list/ajax_items.phtml'
            );

            return $result->setData([
                'html'     => $listBlock->toHtml(),
                'hasMore'  => $page < $collection->getLastPageNumber(),
                'page'     => $page,
                'total'    => (int)$collection->getLastPageNumber(),
                'count'    => $collection->count()
            ]);

        } catch (\Exception $e) {
            return $result->setData([
                'error'   => 'Something went wrong.',
                'message' => $e->getMessage()
            ]);
        }
    }
}