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
namespace Ves\Blog\Block\Product;

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
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

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
        \Magento\Framework\App\ResourceConnection $resource,
    	array $data = []
    ) {
        $this->_blogHelper   = $blogHelper;
        $this->_coreRegistry = $registry;
        $this->_postFactory  = $postFactory;
        $this->_resource     = $resource;
    	parent::__construct($context, $data);
    }

    public function getConfig($key, $default = '')
    {
    	if($this->hasData($key)){
    		return $this->getData($key);
    	}
    	$result = $this->_blogHelper->getConfig($key);
    	$c = explode("/", $key);
    	if(isset($c[1])){
    		if($this->hasData($c[1])){
    			return $this->getData($c[1]);
    		}
    		if($result == ""){
    			$this->setData($c[1], $default);
    			return $default;
    		}
    		$this->setData($c[1], $result);
    	}
    	return $result;
    }

    public function _construct()
    {
    	parent::_construct();
    }

    public function _toHtml(){
    	if(!$this->getConfig('general_settings/enable') || !$this->getConfig('product_page/enable_related_posts')){
    		return;
    	}
    	return parent::_toHtml();
    }

    public function getProduct(){
        return $this->_coreRegistry->registry('current_product');
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        return $this->_collection;
    }

    public function getCollection(){
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        if($this->getConfig('general_settings/enable') && $this->getConfig('product_page/enable_related_posts')){
            $product = $this->getProduct();
            $store = $this->_storeManager->getStore();
            $postCollection = $this->_postFactory->getCollection()
            ->addFieldToFilter('is_active',1)
            ->addStoreFilter($store)
            ->setCurPage(1);
            $postCollection->getSelect()->joinLeft(
                [
                'p' => $this->_resource->getTableName('ves_blog_post_products_related')],
                'p.post_id = main_table.post_id',
                [
                'post_id' => 'post_id',
                'position' => 'position'
                ]
                )->where('p.entity_id = (?)', $product->getId())->order('p.position ASC');
            $this->setCollection($postCollection);
        }
        return parent::_beforeToHtml();
    }
}
