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
namespace Ves\Blog\Block\Widget;

class Posts extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
	/**
	 * @var \Ves\Blog\Helper\Data
	 */
	protected $_blogHelper;

	/**
	 * @var \Ves\Blog\Model\Post
	 */
	protected $_post;

	/**
	 * @var \Ves\Blog\Model\Category
	 */
	protected $_cat;

	/**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
	protected $_storeManager;

	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $_resource;

	/**
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Ves\Blog\Helper\Data  $blogHelper
     * @param \Ves\Blog\Model\Post  $post
     * @param \Ves\Blog\Model\Category $cat
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Cms\Model\Block $blockModel
     * @param array $data
     */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Ves\Blog\Helper\Data $blogHelper,
		\Ves\Blog\Model\Post $post,
		\Ves\Blog\Model\Category $cat,
		\Magento\Framework\App\ResourceConnection $resource,
		\Magento\Cms\Model\Block $blockModel,
		array $data = []
	) {
		$this->_blogHelper = $blogHelper;
		$this->_post       = $post;
		$this->_cat        = $cat;
		$this->_blockModel = $blockModel;
		$this->_resource   = $resource;
		parent::__construct($context, $data);
	}

	public function getCmsBlockModel(){
		return $this->_blockModel;
	}

	public function _toHtml(){
		$widgetStyle = $this->getConfig('widget_style');
		$this->setTemplate("Ves_Blog::widget/" . $widgetStyle . ".phtml");
		$template = $this->getConfig('block_template');
		if ($template) {
			$this->setTemplate($template);
		}
		$itemPerPage = $this->getConfig('number_post', 6);
		$categories = $this->getConfig('categories');
		$categories = $categories?explode(",", $categories):[];

		$store = $this->_storeManager->getStore();
		$collection = $this->_post
		->getCollection()
		->addFieldToFilter("is_active", 1)
		->setPagesize($itemPerPage)
		->addStoreFilter($store)
		->setCurpage(1);
		if ($categories) {
			$collection->getSelect()
			->joinLeft(
				[
				'cat' => $this->_resource->getTableName('ves_blog_post_category')],
				'cat.post_id = main_table.post_id',
				[
				'post_id' => 'post_id',
				'position' => 'position'
				]
				)
			->where('cat.category_id in (?)', $categories);
		}
		$collection->getSelect()->limit($itemPerPage)
		->group('main_table.post_id');
		$orderBy = $this->getConfig("orderby");
		if($orderBy == 1){
			$collection->getSelect()->order("main_table.creation_time DESC");
		}else if($orderBy == 2){
			$collection->getSelect()->order("main_table.creation_time ASC");
		}else if($orderBy == 3){
			$collection->getSelect()->order("main_table.hits DESC");
		}else if($orderBy == 4){
			$collection->getSelect()->order("main_table.hits ASC");
		}else if($orderBy == 4){
			$collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
		}
		$this->setCollection($collection);
		return parent::_toHtml();
	}


	public function getPostCollection($catId){
		$store = $this->_storeManager->getStore();
		$itemsperpage = $this->getConfig('number_post', 6);
		$categories = $this->getConfig('categories');
		$categories = explode(",", $categories);
		$postCollection = $this->_post->getCollection()
		->addFieldToFilter('is_active',1)
		->setPageSize($itemsperpage)
		->addStoreFilter($store)
		->setCurPage(1);
		$postCollection->getSelect()
		->joinLeft(
			[
			'cat' => $this->_resource->getTableName('ves_blog_post_category')],
			'cat.post_id = main_table.post_id',
			[
			'post_id' => 'post_id',
			'position' => 'position'
			]
			)
		->where('cat.category_id = (?)', $catId)
		->order('position ASC');
		return $postCollection;
	}
	public function getBlogTypeCollection($type) {

		$store = $this->_storeManager->getStore();
		$itemsperpage = $this->getConfig('number_post', 6);
		$categories = $this->getConfig('categories');
		$categories = explode(",", $categories);
		$typeCollection = $this->_post->getCollection()
		->addFieldToFilter('is_active',1)
		->setPageSize($itemsperpage)
		->addStoreFilter($store)
		->setCurPage(1);
		$typeCollection->getSelect()
		->joinLeft(
			[
			'cat' => $this->_resource->getTableName('ves_blog_post_category')],
			'cat.post_id = main_table.post_id',
			[
			'post_id' => 'post_id',
			'position' => 'position'
			]
			)
		->where('cat.category_id in (?)', $categories);
		if($type == 'popular') {
			$typeCollection->getSelect()->order("main_table.hits DESC");
		}elseif($type == 'random') {
			$typeCollection->getSelect()->order('RAND()');
		}elseif($type == 'recents') {
			$typeCollection->getSelect()->order("main_table.creation_time DESC");
		}elseif ($type == 'rate') {
			$typeCollection->getSelect()->order("main_table.like DESC");
		}
		return $typeCollection;
	}
	public function getCategoryCollection(){
		$store = $this->_storeManager->getStore();
		$collection = $this->_cat->getCollection();
		$collection = $this->_cat->getCollection()
		->addFieldToFilter('is_active', 1)
		->addStoreFilter($store);
		$collection->getSelect()->where('main_table.category_id IN (' . $this->getConfig('categories') . ')');
		return $collection;
	}

	/**
     * @param AbstractCollection $collection
     * @return $this
     */
	public function setCollection($collection)
	{
		$this->_postCollection = $collection;
		return $this;
	}

	public function getCollection()
	{
		return $this->_postCollection;
	}

	public function getConfig($key, $default = '')
	{
		if($this->hasData($key)){
			return $this->getData($key);
		}
		return $default;
	}
}
