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
namespace Ves\Blog\Model;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Ves\Blog\Api\Data\TagInterface;
use Ves\Blog\Api\Data\TagInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Tag extends \Magento\Framework\Model\AbstractModel implements TagInterface
{
    /**
     * Blog's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_blogHelper;

    protected $tagDataFactory;

    protected $dataObjectHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        TagInterfaceFactory $tagDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Ves\Blog\Model\ResourceModel\Tag $resource = null,
        \Ves\Blog\Model\ResourceModel\Tag\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->tagDataFactory = $tagDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Ves\Blog\Model\ResourceModel\Tag::class);
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * get url
     *
     * @param bool $base_url
     * @return string
     */
    public function getUrl($base_url = true)
    {
        if(!$this->hasData('blog_item_url')){
            if($base_url){
                $url = $this->_storeManager->getStore()->getBaseUrl();
            } else {
                $url = "";
            }

            $url_prefix = $this->getConfig('general_settings/url_prefix');
            //$url_suffix = $this->getConfig('general_settings/url_suffix');
            $urlPrefix = '';
            if($url_prefix){
                $urlPrefix = $url_prefix.'/';
            }
            $item_url = $url . $urlPrefix . 'tag/' . $this->getAlias();
            $this->setData('blog_item_url', $item_url);
        }
        return $this->getData('blog_item_url');
    }

    /**
     * Retrieve tag model with tag data
     * @return TagInterface
     */
    public function getDataModel()
    {
        $tagData = $this->getData();

        $tagDataObject = $this->tagDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $tagDataObject,
            $tagData,
            TagInterface::class
        );

        return $tagDataObject;
    }

    /**
     * {@inheritdoc}
     */
    public function getTagId(){
        return $this->getData(self::TAG_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setTagId($value){
        return $this->setData(self::TAG_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostId(){
        return $this->getData(self::POST_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostId($value){
        return $this->setData(self::POST_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(){
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value){
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(){
        return $this->getData(self::ALIAS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias($value){
        return $this->setData(self::ALIAS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaRobots(){
        return $this->getData(self::META_ROBOTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaRobots($value){
        return $this->setData(self::META_ROBOTS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getDataExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Ves\Blog\Api\Data\TagExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
