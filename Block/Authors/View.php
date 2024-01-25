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
namespace Ves\Blog\Block\Authors;

class View extends \Magento\Framework\View\Element\Template
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_blogHelper;

    /**
     * @var \Ves\Blog\Model\Author
     */
    protected $_authorFactory;

    protected $_collection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Ves\Blog\Model\Author
     * @param \Ves\Blog\Helper\Data
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Blog\Model\Author $authorFactory,
        \Ves\Blog\Helper\Data $blogHelper,
        array $data = []
    ) {
        $this->_blogHelper = $blogHelper;
        $this->_coreRegistry = $registry;
        $this->_authorFactory = $authorFactory;
        parent::__construct($context, $data);

    }

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key)){
            return $this->getData($key);
        }
        $result = $this->_blogHelper->getConfig($key);
        $c = explode("/", $key);
        if($this->hasData($c[1])){
            return $this->getData($c[1]);
        }
        if($result == ""){
            $this->setData($c[1], $default);
            return $default;
        }
        $this->setData($c[1], $result);
        return $result;
    }

    public function _toHtml(){
        if(!$this->getConfig('general_settings/enable') || !$this->getConfig('other_settings/enable_authors_list')){
            return;
        }
        return parent::_toHtml();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param \Magento\Cms\Model\Page $brand
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $brandRoute = $this->getConfig('general_settings/route');
        $show_breadcrumbs = $this->getConfig('blog_page/show_breadcrumbs');
        $latest_page_title = $this->getConfig('blog_latest_page/page_title');
        if($show_breadcrumbs && $breadcrumbsBlock){
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $baseUrl
                ]
             );

            $breadcrumbsBlock->addCrumb(
                'latest',
                [
                    'label' => $latest_page_title,
                    'title' => $latest_page_title,
                    'link' => $this->_blogHelper->getLatestPageUrl()
                ]
            );

            $breadcrumbsBlock->addCrumb(
                'vesblog',
                [
                    'label' => __("Authors"),
                    'title' => __("Authors"),
                    'link' => ''
                ]
             );
        }
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page_title = __('All Blog Authors');
        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('vesblog-page blogauthor-list');

        $meta_description = __('Blog view all authors');
        $meta_keywords = __("all blog authors");

        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);
        }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);
        }
        return parent::_prepareLayout();
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    public function getCollection(){
        return $this->_collection;
    }

    /**
     * Retrieve Toolbar block
     *
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function getToolbarBlock()
    {
        $block = $this->getLayout()->getBlock('vesblog_toolbar');
        if ($block) {
            return $block;
        }
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $show_toolbartop = $this->_blogHelper->getConfig("blog_page/show_toolbartop");
        $show_toolbarbottom = $this->_blogHelper->getConfig("blog_page/show_toolbartop");
        $layout_type = $this->_blogHelper->getConfig("blog_page/layout_type");
        $show_posts_count = $this->_blogHelper->getConfig("other_settings/show_posts_count");

        $this->setData('show_toolbartop', $show_toolbartop);
        $this->setData('show_toolbarbottom', $show_toolbarbottom);
        $this->setData('layout_type', $layout_type);

        $store = $this->_storeManager->getStore();
        $author_ = $this->getAuthor();
        $itemsperpage = (int)$this->getConfig('blog_page/item_per_page');
        $orderby = "nick_name";
        $authorCollection = $this->_authorFactory->getCollection()
        ->addFieldToFilter('main_table.is_view',1)
        ->addFieldToFilter('main_table.user_id', ['gt'=>0])
        ->addFieldToFilter('main_table.user_name', ['notnull'=>0])
        ->setPageSize($itemsperpage)
        ->setCurPage(1);

        if($show_posts_count) {
            $authorCollection->addJoinPosts($store);
        }

        $authorCollection->getSelect()->order($orderby);

        $this->setCollection($authorCollection);
        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if($toolbar){
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($authorCollection);
            $this->setChild('toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }
}
