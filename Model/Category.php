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

use Magento\Framework\DataObject\IdentityInterface;
use Ves\Blog\Api\Data\CategoryInterface;
use Ves\Blog\Api\Data\CategoryInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Ves\Blog\Model\Config\Source\LatestPostsStyle;
use Ves\Blog\Model\Config\Source\Layout;
use Ves\Blog\Model\Config\Source\CatOrderby;

class Category extends \Magento\Framework\Model\AbstractModel implements CategoryInterface, IdentityInterface
{
    /**
     * Blog's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**
     * blog category cache tag
     */
    const CACHE_TAG = 'vesblog_c';


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

    protected $_resource;

    protected $categoryDataFactory;

    protected $dataObjectHelper;

    /**
     * @param \Magento\Framework\Model\Context                          $context
     * @param \Magento\Framework\Registry                               $registry
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \Magento\Framework\UrlInterface                           $url
     * @param \Ves\Blog\Helper\Data                                    $brandHelper
     * @param CategoryInterfaceFactory $categoryDataFactory
     * @param  DataObjectHelper $dataObjectHelper
     * @param \Ves\Blog\Model\ResourceModel\Blog|null                      $resource
     * @param \Ves\Blog\Model\ResourceModel\Blog\Collection|null           $resourceCollection
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Ves\Blog\Helper\Data $blogHelper,
        CategoryInterfaceFactory $categoryDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Ves\Blog\Model\ResourceModel\Category $resource = null,
        \Ves\Blog\Model\ResourceModel\Category\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->categoryDataFactory = $categoryDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        //$this->_resource = $resource;
        $this->_blogHelper = $blogHelper;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Ves\Blog\Model\ResourceModel\Category::class);
    }

    /**
     * Retrieve category model with category data
     * @return CategoryInterface
     */
    public function getDataModel()
    {
        $categoryData = $this->getData();

        $categoryDataObject = $this->categoryDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $categoryDataObject,
            $categoryData,
            CategoryInterface::class
        );

        return $categoryDataObject;
    }

    /**
     * Prevent blocks recursion
     *
     * @return \Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $needle = 'category_id="' . $this->getId() . '"';
        if (!$this->getDescription() || (false == @strstr($this->getDescription(), $needle))) {
            return parent::beforeSave();
        }
        throw new \Magento\Framework\Exception\LocalizedException(
            __('Make sure that category content does not reference the block itself.')
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
    }
    /**
     * {@inheritdoc}
     */
    public function setStores($value){
        return $this->setData(self::STORES, $value);
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

    public function getPostIds(){
        $posts = $this->getData('posts');
        $ids = [];
        foreach ($posts as $k => $v) {
            $ids[] = $v['post_id'];
        }
        return $ids;
    }

    public function getUrl($base_url = true)
    {
        if(!$this->hasData('blog_item_url')){
            if($base_url){
                $url = $this->_storeManager->getStore()->getBaseUrl();
            } else {
                $url = "";
            }
            $url_prefix = $this->_blogHelper->getConfig('general_settings/url_prefix');
            $url_suffix = $this->_blogHelper->getConfig('general_settings/url_suffix');
            $urlPrefix = '';
            if($url_prefix){
                $urlPrefix = $url_prefix.'/';
            }
            $item_url = $url . $urlPrefix . $this->getIdentifier() . $url_suffix;
            $this->setData('blog_item_url', $item_url);
        }
        return $this->getData('blog_item_url');
    }
    /**
     * Prepare all additional data
     * @param  string $format
     * @return self
     */
    public function initDinamicData()
    {
        $keys = [
            'meta_description',
            'meta_title',
        ];

        foreach ($keys as $key) {
            $method = 'get' . str_replace(
                '_',
                '',
                ucwords($key, '_')
            );
            $this->$method();
        }

        return $this;
    }
    /**
     * Deprecated
     * Retrieve true if post is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getIsActive() == self::STATUS_ENABLED);
    }
    /**
     * Retrieve if is visible on store
     * @return bool
     */
    public function isVisibleOnStore($storeId)
    {
        $publishDate = $this->getData('creation_time');
        if($this->hasData('publish_time')){
            $publishDate = $this->getData('publish_time');
        }

        return $this->getIsActive()
            && ($publishDate <= $this->getResource()->getDate()->gmtDate())
            && array_intersect([0, $storeId], $this->getStoreIds());
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getCategoryId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryId(){
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryId($value){
        return $this->setData(self::CATEGORY_ID, $value);
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
    public function getIdentifier(){
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * {@inheritdoc}
     */
    public function setIdentifier($value){
        return $this->setData(self::IDENTIFIER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(){
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($value){
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage(){
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($value){
        return $this->setData(self::IMAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLayoutType(){
        return $this->getData(self::LAYOUT_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLayoutType($value){
        $list_layout_types = [
            Layout::TYPE_LIST,
            Layout::TYPE_GRID,
            Layout::TYPE_MASONRY
        ];
        if(in_array($value, $list_layout_types)){
            return $this->setData(self::LAYOUT_TYPE, $value);
        }else {
            return $this;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getOrderby(){
        return $this->getData(self::ORDERBY);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderby($value){
        $list_orderby = [
            CatOrderby::NEWEST,
            CatOrderby::OLDER,
            CatOrderby::POSITION_HIGHT_TO_LOW,
            CatOrderby::POSITION_LOW_TO_HIGHT
        ];
        if(in_array($value, $list_orderby)){
            return $this->setData(self::ORDERBY, $value);
        }else {
            return $this;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getComments(){
        return $this->getData(self::COMMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setComments($value){
        return $this->setData(self::COMMENTS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemPerPage(){
        return $this->getData(self::ITEM_PER_PAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setItemPerPage($value){
        return $this->setData(self::ITEM_PER_PAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLgColumnItem(){
        return $this->getData(self::LG_COLUMN_ITEM);
    }

    /**
     * {@inheritdoc}
     */
    public function setLgColumnItem($value){
        return $this->setData(self::LG_COLUMN_ITEM, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMdColumnItem(){
        return $this->getData(self::MD_COLUMN_ITEM);
    }

    /**
     * {@inheritdoc}
     */
    public function setMdColumnItem($value){
        return $this->setData(self::MD_COLUMN_ITEM, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSmColumnItem(){
        return $this->getData(self::SM_COLUMN_ITEM);
    }

    /**
     * {@inheritdoc}
     */
    public function setSmColumnItem($value){
        return $this->setData(self::SM_COLUMN_ITEM, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getXsColumnItem(){
        return $this->getData(self::XS_COLUMN_ITEM);
    }

    /**
     * {@inheritdoc}
     */
    public function setXsColumnItem($value){
        return $this->setData(self::XS_COLUMN_ITEM, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageLayout(){
        return $this->getData(self::PAGE_LAYOUT);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageLayout($value){
        return $this->setData(self::PAGE_LAYOUT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTitle(){
        return $this->getData(self::PAGE_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageTitle($value){
        return $this->setData(self::PAGE_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCanonicalUrl(){
        return $this->getData(self::CANONICAL_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCanonicalUrl($value){
        return $this->setData(self::CANONICAL_URL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLayoutUpdateXml(){
        return $this->getData(self::LAYOUT_UPDATE_XML);
    }

    /**
     * {@inheritdoc}
     */
    public function setLayoutUpdateXml($value){
        return $this->setData(self::LAYOUT_UPDATE_XML, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords(){
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($value){
        return $this->setData(self::META_KEYWORDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription(){
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($value){
        return $this->setData(self::META_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreationTime(){
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationTime($value){
        return $this->setData(self::CREATION_TIME, $value);
    }

   /**
     * {@inheritdoc}
     */
    public function getUpdateTime(){
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdateTime($value){
        return $this->setData(self::UPDATE_TIME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCatPosition(){
        return $this->getData(self::CAT_POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCatPosition($value){
        return $this->setData(self::CAT_POSITION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive(){
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($value){
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId(){
        return $this->getData(self::PARENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($value){
        return $this->setData(self::PARENT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostsStyle(){
        return $this->getData(self::POSTS_STYLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostsStyle($value){
        $list_styles = [
            LatestPostsStyle::STYLE1,
            LatestPostsStyle::STYLE2,
            LatestPostsStyle::STYLE3,
            LatestPostsStyle::STYLE4
        ];
        if(in_array($value, $list_styles)){
            return $this->setData(self::POSTS_STYLE, $value);
        }else {
            return $this;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function getPostsTemplate(){
        return $this->getData(self::POSTS_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostsTemplate($value){
        return $this->setData(self::POSTS_TEMPLATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostTemplate(){
        return $this->getData(self::POST_TEMPLATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostTemplate($value){
        return $this->setData(self::POST_TEMPLATE, $value);
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
        \Ves\Blog\Api\Data\CategoryExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

}
