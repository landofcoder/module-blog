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

use Magento\Customer\Api\CustomerRepositoryInterface;

class Comment extends \Magento\Framework\View\Element\Template
{

    /**
     * @var AbstractCollection
     */
    protected $_postCollection;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

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
     * @var \Ves\Blog\Model\Comment
     */
    protected $_comment;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Customer\Helper\Session\CurrentCustomer
     * @param CustomerRepositoryInterface
     * @param \Magento\Framework\Registry
     * @param \Ves\Blog\Helper\Data
     * @param \Ves\Blog\Model\Comment
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Registry $registry,
        \Ves\Blog\Helper\Data $blogHelper,
        \Ves\Blog\Model\Comment $comment,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_blogHelper = $blogHelper;
        $this->_coreRegistry = $registry;
        $this->_comment = $comment;
        $this->currentCustomer = $currentCustomer;
        $this->customerRepository = $customerRepository;
        $this->_resource     = $resource;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addData([
            'cache_lifetime' => 0,
            'cache_tags' => ['ves_blog_post_comment']]);
    }

    public function _toHtml(){
        if(!$this->getConfig('general_settings/enable') || !$this->getConfig('post_page/enable_commentblock')) return;
        if($this->getConfig('general_settings/disable_comment')) return;

        return parent::_toHtml();
    }

    public function getCommentsByParentId($parent_id = 0){
        $store = $this->_storeManager->getStore();
        $post = $this->getPost();
        $collection = $this->_comment->getCollection();
        $collection->addFieldToFilter("main_table.is_active", 1)
        ->addFieldToFilter("main_table.post_id", $post->getPostId())
        ->addFieldToFilter("main_table.parent_id", $parent_id)
        ->addStoreFilter($store)
        ->setOrder("main_table.creation_time", "DESC");

        return $collection;
    }
    protected function _beforeToHtml()
    {
        $itemsperpage = $this->getConfig("post_page/numbercomment_perpage");
        $collection = $this->getCommentsByParentId();
        $this->setCollection($collection);

        $toolbar = $this->getToolbarBlock();
        // set collection to toolbar and apply sort
        if($toolbar){
            $toolbar->setData('_current_limit',$itemsperpage)->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        return parent::_beforeToHtml();
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

    public function getConfig($key, $default = '')
    {
        if($this->hasData($key)){
            return $this->getData($key);
        }
        $result = $this->_blogHelper->getConfig($key);
        $c = explode("/", $key);
        if(count($c)==2){
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

    public function getPost(){
        $post = $this->_coreRegistry->registry('current_post');
        return $post;
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

    public function getCommentFormUrl(){
        return $this->getUrl("vesblog/comment/add");
    }

    /**
     * Get the logged in customer
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    public function getCustomer()
    {
        try {
            $customer = $this->customerRepository->getById($this->currentCustomer->getCustomerId());
            return $customer;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
    }


}
