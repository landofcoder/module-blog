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
namespace Ves\Blog\Block\Post;

class NextPrevPosts extends \Magento\Framework\View\Element\Template
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
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Ves\Blog\Model\Post
     * @param \Ves\Blog\Helper\Data
     * @param array
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
        $c = explode("/", $key);
        if(count($c)==2){
            if($this->hasData($c[1])){
                return $this->getData($c[1]);
            }
        }
        if($this->hasData($key)){
            return $this->getData($key);
        }
        return $default;
    }

    public function _construct()
    {
    	parent::_construct();
    }

    public function _toHtml(){
        $post = $this->getPost();
        if(!$this->_blogHelper->getConfig('general_settings/enable') || !$post->getIsActive()) return;
        if(!$this->_blogHelper->getConfig('post_page/enable_nextprev')) return;
        return parent::_toHtml();
    }

    public function getPost(){
        $post = $this->_coreRegistry->registry('current_post');
        return $post;
    }


    /**
     * Set post collection
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
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $store = $this->_storeManager->getStore();
        $post = $this->getPost();
        $creation_time = $post->getCreationTime();
        $update_time = $post->getUpdateTime();
        $filter_field = "creation_time";
        if($filter_field == "creation_time") {
            $filter_time = $creation_time;
        } else {
            $filter_time = $update_time;
        }

        //prev post collection
        $postPrevCollection = $this->_postFactory->getCollection()
                            ->addFieldToFilter('is_active',1)
                            ->addStoreFilter($store);

        $postPrevCollection->getSelect()
                        ->where($filter_field.' < ?', $filter_time)
                        ->where('main_table.post_id != ?', $post->getId())
                        ->order($filter_field.' DESC')
                        ->limit(1);

        //next post collection
        $postNextCollection = $this->_postFactory->getCollection()
                            ->addFieldToFilter('is_active',1)
                            ->addStoreFilter($store);

        $postNextCollection->getSelect()
                            ->where($filter_field.' > ?', $filter_time)
                            ->where('main_table.post_id != ?', $post->getId())
                            ->order($filter_field.' ASC')
                            ->limit(1);


        $this->setPrevCollection($postPrevCollection);
        $this->setNextCollection($postNextCollection);
        return parent::_beforeToHtml();
    }
}
