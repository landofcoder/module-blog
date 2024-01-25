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
namespace Ves\Blog\Block\Archive;

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
     * @var \Ves\Blog\Model\Post
     */
    protected $_postFactory;
    protected $_postsBlock;
    protected $_collection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $registry
     * @param \Ves\Blog\Model\Post                             $postFactory
     * @param \Ves\Blog\Helper\Data                            $blogHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Blog\Model\Post $postFactory,
        \Ves\Blog\Helper\Data $blogHelper,
        array $data = []
    ) {
        $this->_blogHelper = $blogHelper;
        $this->_coreRegistry = $registry;
        $this->_postFactory = $postFactory;
        parent::__construct($context, $data);

    }

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key)){
            return $this->getData($key);
        }
        $result = $this->_blogHelper->getConfig($key);
        $c = explode("/", $key);
        if(!$result && $this->hasData($c[1])){
            return $this->getData($c[1]);
        }
        if($result == ""){
            $this->setData($c[1], $default);
            return $default;
        }
        $this->setData($c[1], $result);
        return $result;
    }

    public function _construct()
    {
        parent::_construct();
    }

    public function _toHtml(){
        if(!$this->getConfig('general_settings/enable')){
            return;
        }
        return parent::_toHtml();
    }

    /**
     * Retrieve year by time
     * @param  int $time
     * @return string
     */
    public function getYear($time)
    {
        return date('Y', $time);
    }

    /**
     * Retrieve month by time
     * @param  int $time
     * @return string
     */
    public function getMonth($time)
    {
        return __(date('F', $time));
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
        $page_title = $this->getConfig('blog_page/page_title');
        $show_breadcrumbs = $this->getConfig('blog_page/show_breadcrumbs');
        $time = $this->getRequest()->getParam('date');
        $time = explode('-', $time);
        $month = "";

        //die($time[0] . '-' . $time[1] . '-01 00:00:00');
        /*
        echo '<pre>';
        print_r($time);
        echo '</pre>';
        //die('test');

        echo $this->formatDate($time[0] . '-' . $time[1] . '-01 00:00:00', \IntlDateFormatter::LONG) . '|999';

        echo '<pre>';
        print_r(get_class_methods($this));
        echo '</pre>';
        die('test');
         */
        if(isset($time[1]) && $time[1]) {
            $month = date("F", mktime(0, 0, 0, $time[1], 1, $time[0]));
        }


        $year = $time[0];
        $latest_page_title = $this->getConfig('blog_latest_page/page_title');
        if($show_breadcrumbs && $breadcrumbsBlock){
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link'  => $baseUrl
                ]
                );
            $breadcrumbsBlock->addCrumb(
                'latest',
                [
                    'label' => $latest_page_title,
                    'title' => $latest_page_title,
                    'link'  => $this->_blogHelper->getLatestPageUrl()
                ]
            );
            if($month) {
                $breadcrumbsBlock->addCrumb(
                'vesblog',
                [
                    'label' => __('Monthly Archives: %1', $month . ' ' . $year),
                    'title' => __('Monthly Archives: %1', $month . ' ' . $year),
                    'link'  => ''
                ]
                );
            } else {
                $breadcrumbsBlock->addCrumb(
                'vesblog',
                [
                    'label' => __('Yearly Archives: %1', $year),
                    'title' => __('Yearly Archives: %1', $year),
                    'link'  => ''
                ]
                );
            }

        }
    }



    /**
     * Set brand collection
     * @param \Ves\Blog\Model\Post
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    public function getCollection(){
        return $this->_collection;
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $time = $this->getRequest()->getParam('date');
        $time = explode('-', $time);
        $month = "";
        if(isset($time[1]) && $time[1]){
            $month = date("F", mktime(0, 0, 0, $time[1], 1, $time[0]));
            $month = __($month);
        }
        $year = $time[0];
        if($month){
            $page_title = __('Monthly Archives: %1', $month . ' ' . $year);
        } else {
            $page_title = __('Yearly Archives: %1', $year);
        }

        $this->_addBreadcrumbs();
        $this->pageConfig->addBodyClass('vesblog-page');
        $this->pageConfig->addBodyClass('ves-archiveblog');
        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);
        }
        return parent::_prepareLayout();
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

    public function getPostsBlock()
    {
        $collection = $this->getCollection();
        $block = $this->_postsBlock;
        $block->setData($this->getData())->setCollection($collection);
        $html = $block->toHtml();
        if ($html) {
            return $html;
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
        $this->setData('show_toolbartop', $show_toolbartop);
        $this->setData('show_toolbarbottom', $show_toolbarbottom);
        $this->setData('layout_type', $layout_type);

        $postsStyles = $this->getConfig('blog_page/posts_styles');
        $postsStyles = $postsStyles?$postsStyles:'style1';
        $postsBlock  = $this->getLayout()->getBlock('blog.posts.list');
        $postsBlock->setTemplate('Ves_Blog::post/style/' . $postsStyles . '.phtml');
        $this->_postsBlock = $postsBlock;
        $data = $postsBlock->getData();
        unset($data['type']);
        $this->addData($data);

        $time = $this->getRequest()->getParam('date');
        $time = explode('-', $time);
        $month = isset($time[1])?$time[1]:0;
        $year = $time[0];

        $store = $this->_storeManager->getStore();
        $itemsperpage = (int)$this->getConfig('blog_page/item_per_page');
        $orderby = $this->getConfig('blog_page/orderby');
        $postCollection = $this->_postFactory->getCollection()
        ->addFieldToFilter('is_active',1)
        ->setPageSize($itemsperpage)
        ->addStoreFilter($store)
        ->setCurPage(1);
        $postCollection->getSelect()
        ->where('YEAR(creation_time) = ?', (int)$year);
        if($month) {
            $postCollection->getSelect()->where('MONTH(creation_time) = ?', (int)$month);
        }

        $postCollection->getSelect()->order("post_id " . $orderby);
        $this->setCollection($postCollection);
        $toolbar = $this->getToolbarBlock();

        // set collection to toolbar and apply sort
        if($toolbar){
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($postCollection);
            $this->setChild('toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
    }
}
