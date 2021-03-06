<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Blog\Block\Rss;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\Rss\DataProviderInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Tag extends \Magento\Framework\View\Element\AbstractBlock implements DataProviderInterface
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Model\Rss\Category
     */
    protected $rssModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Url
     */
    protected $rssUrlBuilder;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Ves\Blog\Model\Post
     */
    protected $_post;

    /**
     * @var \Ves\Blog\Model\Tag
     */
    protected $_tag;

    protected $_blogHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\Rss\Category $rssModel
     * @param \Magento\Framework\Url $rssUrlBuilder
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CategoryRepositoryInterface $categoryRepository
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\Rss\Category $rssModel,
        \Magento\Framework\Url $rssUrlBuilder,
        \Ves\Blog\Helper\Image $imageHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Ves\Blog\Model\Category $categoryRepository,
        \Ves\Blog\Model\Post $post,
        \Ves\Blog\Model\Tag $tag,
        \Ves\Blog\Helper\Data $blogHelper,
        array $data = []
    ) {
        $this->imageHelper = $imageHelper;
        $this->categoryFactory = $categoryFactory;
        $this->customerSession = $customerSession;
        $this->rssModel = $rssModel;
        $this->rssUrlBuilder = $rssUrlBuilder;
        $this->storeManager = $context->getStoreManager();
        $this->categoryRepository = $categoryRepository;
        $this->_post = $post;
        $this->_tag = $tag;
        $this->_blogHelper = $blogHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->setCacheKey(
            'rss_blog_tag_'
            . $this->getRequest()->getParam('key') . '_'
            . $this->getStoreId() . '_'
            . $this->customerSession->getId()
        );
        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getRssData()
    {
        $postIds = [];
        $storeId = $this->getStoreId();
        $store = $this->storeManager->getStore($storeId);
        try {
            $alias = $this->getRequest()->getParam('key');
            $tagCollection = $this->_tag->getCollection()
                ->addFieldToFilter("alias", $alias);
            $tag = $tagCollection->getFirstItem();
            foreach ($tagCollection as $k => $v) {
                $postIds[] = $v['post_id'];
            }
        } catch (NoSuchEntityException $e) {
            return [
                'title' => 'Posts Not Found',
                'description' => 'Posts Not Found',
                'link' => $this->getUrl(''),
                'charset' => 'UTF-8'
            ];
        }

        $tagrss = $this->_blogHelper->getConfig("general_settings/tagrss");
        if(empty($tag) || !$tagrss){
            return [
                'title' => 'Posts Not Found',
                'description' => 'Posts Not Found',
                'link' => $this->getUrl(''),
                'charset' => 'UTF-8'
            ];
        }
        $newUrl = $tag->getUrl();
        $title = $tag->getName();
        $data = ['title' => $title, 'description' => $title, 'link' => $newUrl, 'charset' => 'UTF-8'];

        $itemPerPage = $this->_blogHelper->getConfig("general_settings/rssposts_show");
        $orderby = $this->_blogHelper->getConfig('blog_page/orderby');
        $postCollection = $this->_post->getCollection()
        ->addFieldToFilter('main_table.is_active',1)
        ->addStoreFilter($store)
        ->setPageSize($itemPerPage)
        ->setCurPage(1);
        $postCollection->getSelect()
        ->where('main_table.post_id in (?)', $postIds)
        ->order("main_table.creation_time " . $orderby); 

        foreach ($postCollection as $_post) {
            $description = '
                    <table><tr>
                        <td><a href="%s"><img src="%s" border="0" align="left" height="100" width="100"></a></td>
                        <td  style="text-decoration:none;">%s</td>
                    </tr></table>
                ';
            $description = sprintf(
                $description,
                $_post->getUrl(),
                $this->imageHelper->resizeImage($_post->getThumbnail(), 100, 100),
                $this->_blogHelper->filter($_post->getShortContent())
            );
            $data['entries'][] = [
                'title' => $_post->getTitle(),
                'link' => $_post->getUrl(),
                'description' => $description,
            ];
        }
        return $data;
    }

    /**
     * Get rendered price html
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    protected function renderPriceHtml(\Magento\Catalog\Model\Product $product)
    {
        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        if (!$priceRender) {
            $priceRender = $this->getLayout()->createBlock(
                'Magento\Framework\Pricing\Render',
                'product.price.render.default',
                ['data' => ['price_render_handle' => 'catalog_product_prices']]
            );
        }

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'display_minimal_price'  => true,
                    'use_link_for_as_low_as' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                ]
            );
        }

        return $price;
    }

    /**
     * @return int
     */
    protected function getStoreId()
    {
        $storeId = (int)$this->getRequest()->getParam('store_id');
        if ($storeId == null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        return $storeId;
    }

    /**
     * @return int
     */
    public function getCacheLifetime()
    {
        return 600;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowed()
    {
        return $this->_scopeConfig->isSetFlag(
            'rss/catalog/category',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    public function getFeeds()
    {
        $result = [];
        if ($this->isAllowed()) {
            /** @var $category \Magento\Catalog\Model\Category */
            $category = $this->categoryFactory->create();
            $treeModel = $category->getTreeModel()->loadNode($this->storeManager->getStore()->getRootCategoryId());
            $nodes = $treeModel->loadChildren()->getChildren();

            $nodeIds = [];
            foreach ($nodes as $node) {
                $nodeIds[] = $node->getId();
            }

            /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
            $collection = $category->getResourceCollection();
            $collection->addIdFilter($nodeIds)
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('is_anchor')
                ->addAttributeToFilter('is_active', 1)
                ->addAttributeToSort('name')
                ->load();

            $feeds = [];
            foreach ($collection as $category) {
                $feeds[] = [
                    'label' => $category->getName(),
                    'link' => $this->rssUrlBuilder->getUrl("vesblog/feed/index", ['type' => 'category', 'cid' => $category->getId()]),
                ];
            }
            $result = ['group' => 'Categories', 'feeds' => $feeds];
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthRequired()
    {
        return false;
    }
}
