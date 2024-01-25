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

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Ves\Blog\Api\Data\PostInterface;
use Ves\Blog\Api\Data\PostInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class Post
 * @package Ves\Blog\Model
 */
class Post extends \Magento\Framework\Model\AbstractModel implements PostInterface, IdentityInterface
{
    /**
     * Blog's Statuses
     */
    const STATUS_ENABLED = 1;
    /**
     *
     */
    const STATUS_DISABLED = 0;

    /**
     * blog category cache tag
     */
    const CACHE_TAG = 'vesblog_p';

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

    /**
     * @var
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'blog_post';


    /**
     * @var PostInterfaceFactory
     */
    protected $postDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var CollectionFactory
     */
    private $productCollection;

    /**
     * Post constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param PostInterfaceFactory $postDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CollectionFactory $collectionFactory
     * @param ResourceModel\Post|null $resource
     * @param ResourceModel\Post\Collection|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        PostInterfaceFactory $postDataFactory,
        DataObjectHelper $dataObjectHelper,
        CollectionFactory $collectionFactory,
        \Ves\Blog\Model\ResourceModel\Post $resource = null,
        \Ves\Blog\Model\ResourceModel\Post\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->filterProvider = $filterProvider;
        $this->scopeConfig = $scopeConfig;
        $this->postDataFactory = $postDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->productCollection = $collectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ves\Blog\Model\ResourceModel\Post');
    }

    /**
     * Retrieve post model with post data
     * @return PostInterface
     */
    public function getDataModel()
    {
        $postData = $this->getData();

        $postDataObject = $this->postDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $postDataObject,
            $postData,
            PostInterface::class
        );

        return $postDataObject;
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
     * @return array
     */
    public function getPostTags()
    {
        $tags = [];
        if($post_id = $this->getData("post_id")){
            $tags = $this->getResource()->getPostTags($post_id);
        }
        return $tags;
    }

    /**
     * @return array
     */
    public function getPostCategories(){
        $categories = [];
        if($post_id = $this->getData("post_id")){
            $categories = $this->getResource()->getPostCategories($post_id);
        }

        return $categories;
    }

    /**
     * @param $key
     * @param null $store
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'vesblog/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    /**
     * @param bool $base_url
     * @return array|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($base_url = true)
    {
        $enable_custom_post_url = $this->getConfig("other_settings/use_custom_post_link");
        if($enable_custom_post_url && ($real_post_url = $this->hasData("real_post_url"))){
            $this->setData("blog_item_url", $real_post_url);
        }
        if(!$this->hasData('blog_item_url')){
            if($base_url) {
                $url = $this->_storeManager->getStore()->getBaseUrl();
            } else {
                $url = "";
            }
            $url_prefix = $this->getConfig('general_settings/url_prefix');
            $url_suffix = $this->getConfig('general_settings/url_suffix');
            $categoriesUrls = $this->getConfig('general_settings/categories_urls');
            $urlPrefix = '';
            if($url_prefix){
                $urlPrefix = $url_prefix.'/';
            }
            $categoryUrl = '';
            if($categoriesUrls){
                $category = $this->getPostCategories();
                if($category && isset($category[0]['identifier']) && $category[0]['identifier']!=''){
                    $categoryUrl = $category[0]['identifier'] . '/';
                }

            }
            $item_url = $url . $urlPrefix . $categoryUrl . $this->getIdentifier() . $url_suffix;
            $this->setData('blog_item_url', $item_url);
        }
        return $this->getData('blog_item_url');
    }


    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param $image
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFullImageUrl($image){
        $media_base_url = $this->getBaseMediaUrl();
        $image = str_replace($media_base_url, "", $image);
        $media_base_url = str_replace("https://","http://", $media_base_url);
        $image = str_replace($media_base_url, "", $image);
        return $this->getBaseMediaUrl().$image;
    }
    /**
     * Retrieve og image url
     * @return string
     */
    public function getOgImage()
    {
        if (!$this->hasData('og_image')) {
            $file = false;
            if ($this->getData('og_img')) {
                $file = $this->getData('og_img');
            }elseif($this->getPostImage()){
                $file = $this->getPostImage();
            }elseif($this->getData('thumbnail')){
                $file = $this->getData('thumbnail');
            }
            if($file){
                $image = $this->getFullImageUrl($file);
            }else {
                $image = false;
            }
            $this->setData('og_image', $image);
        }

        return $this->getData('og_image');
    }

    /**
     * @return array|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl(){
        if (!$this->hasData('image_url')) {
            $file = $this->getImage();
            $image = $this->getFullImageUrl($file);
            $this->setData('image_url', $image);
        }

        return $this->getData('image_url');
    }

    /**
     * @return array|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getThumbnailUrl(){
        if (!$this->hasData('thumbnail_url')) {
            $file = $this->getThumbnail();
            $image = $this->getFullImageUrl($file);
            $this->setData('thumbnail_url', $image);
        }

        return $this->getData('thumbnail_url');
    }

    /**
     * @param string $format
     * @return string
     */
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        $publishDate = $this->getData('creation_time');
        if ($this->hasData('publish_time')) {
            $publishDate = $this->getData('publish_time');
        }
        return \Ves\Blog\Helper\Data::getTranslatedDate(
            $format,
            $publishDate
        );
    }

    /**
     * Retrieve post publish date using format
     * @param  string $format
     * @return string
     */
    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Ves\Blog\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
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
            && $publishDate <= $this->getResource()->getDate()->gmtDate()
            && array_intersect([0, $storeId], $this->getStoreIds());
    }

    /**
     * Prepare all additional data
     * @param  string $format
     * @return self
     */
    public function initDinamicData()
    {
        $keys = [
            'og_image',
            'og_type',
            'og_description',
            'og_title',
            'meta_description',
            'meta_title',
            'short_filtered_content',
            'filtered_content',
            'first_image',
            'featured_image',
            'post_url',
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
     * @return array|mixed|string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostImage(){
        if($this->getId()){
            $post_image = $this->getData('image');
            $use_custom_post_img = $this->getConfig("other_settings/use_custom_post_img");
            $real_image_url = $this->getData('real_image_url');
            if($use_custom_post_img && $real_image_url){
                $post_image = $real_image_url;
            }
            return $post_image;
        }
        return "";
    }

    /**
     * @return array|mixed|string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostThumbnail(){
        if($this->getId()){
            $post_image = $this->getData('thumbnail');
            $use_custom_post_img = $this->getConfig("other_settings/use_custom_post_img");
            $real_image_url = $this->getData('real_thumbnail_url');
            if($use_custom_post_img && $real_image_url){
                $post_image = $real_image_url;
            }
            return $post_image;
        }
        return "";
    }

    /**
     * Retrieve featured image url
     * @return string
     */
    public function getFeaturedImage()
    {
        if (!$this->hasData('featured_image')) {
            if ($file = $this->getPostThumbnail()) {
                $parsed = parse_url($file);
                if (empty($parsed['scheme'])) {
                    $image = $this->_url->getMediaUrl($file);
                }else{
                    $image = $file;
                }
            }
            elseif ($file = $this->getPostImage()) {
                $parsed = parse_url($file);
                if (empty($parsed['scheme'])) {
                    $image = $this->_url->getMediaUrl($file);
                }else{
                    $image = $file;
                }
            } else {
                $image = false;
            }
            $this->setData('featured_image', $image);
        }

        return $this->getData('featured_image');
    }

    /**
     * Retrieve first image url
     * @return string
     */
    public function getFirstImage()
    {
        if (!$this->hasData('first_image')) {
            $image = $this->getFeaturedImage();
            if (!$image) {
                $content = $this->getFilteredContent();
                $match = null;
                preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $match);
                if (!empty($match['src'])) {
                    $image = $match['src'];
                }
            }
            $this->setData('first_image', $image);
        }

        return $this->getData('first_image');
    }


    /**
     * Retrieve post url
     * @return string
     */
    public function getPostUrl()
    {
        $enable_custom_post_url = $this->getConfig("other_settings/use_custom_post_link");
        if($enable_custom_post_url && $this->hasData("real_post_url")){
            $real_post_url = $this->hasData("real_post_url");
            $this->setData("post_url", $real_post_url);
        }
        if (!$this->hasData('post_url')) {
            $url = $this->getUrl();
            $this->setData('post_url', $url);
        }

        return $this->getData('post_url');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getPostId()];
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
    public function getTitle(){
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($value){
        return $this->setData(self::TITLE, $value);
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
    public function getContent(){
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($value){
        return $this->setData(self::CONTENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getShortContent(){
        return $this->getData(self::SHORT_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setShortContent($value){
        return $this->setData(self::SHORT_CONTENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId(){
        return $this->getData(self::USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($value){
        return $this->setData(self::USER_ID, $value);
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
    public function getImageType(){
        return $this->getData(self::IMAGE_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageType($value){
        return $this->setData(self::IMAGE_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageVideoType(){
        return $this->getData(self::IMAGE_VIDEO_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageVideoType($value){
        return $this->setData(self::IMAGE_VIDEO_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getImageVideoId(){
        return $this->getData(self::IMAGE_VIDEO_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setImageVideoId($value){
        return $this->setData(self::IMAGE_VIDEO_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnail(){
        return $this->getData(self::THUMBNAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setThumbnail($value){
        return $this->setData(self::THUMBNAIL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnailType(){
        return $this->getData(self::THUMBNAIL_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setThumbnailType($value){
        return $this->setData(self::THUMBNAIL_TYPE, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getThumbnailVideoType(){
        return $this->getData(self::THUMBNAIL_VIDEO_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setThumbnailVideoType($value){
        return $this->setData(self::THUMBNAIL_VIDEO_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getThumbnailVideoId(){
        return $this->getData(self::THUMBNAIL_VIDEO_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setThumbnailVideoId($value){
        return $this->setData(self::THUMBNAIL_VIDEO_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        $desc = $this->getData(self::META_DESCRIPTION);
        if (!$desc) {
            $desc = $this->getData('content');
        }

        $desc = strip_tags($desc);
        if (mb_strlen($desc) > 300) {
            $desc = mb_substr($desc, 0, 300);
        }

        return trim($desc);
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
    public function getTags(){
        $return = [];
        $tags = $this->getData(self::TAGS);
        if(!$tags){
            $tags = $this->getPostTags();
            if($tags){
                foreach($tags as $tag){
                    $return[] = trim($tag["name"]);
                }
            }
            return implode(",",$return);
        }else {
            return $tags;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTags($value){
        return $this->setData(self::TAGS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getHits(){
        return $this->getData(self::HITS);
    }

    /**
     * {@inheritdoc}
     */
    public function setHits($value){
        return $this->setData(self::HITS, $value);
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
    public function getEnableComment(){
        return $this->getData(self::ENABLE_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnableComment($value){
        return $this->setData(self::ENABLE_COMMENT, $value);
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
    public function getIsPrivate(){
        return $this->getData(self::IS_PRIVATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsPrivate($value){
        return $this->setData(self::IS_PRIVATE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLike(){
        return $this->getData(self::LIKE);
    }

    /**
     * {@inheritdoc}
     */
    public function setLike($value){
        return $this->setData(self::LIKE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDisklike(){
        return $this->getData(self::DISKLIKE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDisklike($value){
        return $this->setData(self::DISKLIKE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        $title = $this->getData(self::META_TITLE);
        if (!$title) {
            $title = $this->getData('title');
        }

        return trim($title);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($value){
        return $this->setData(self::META_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOgMetadata(){
        return $this->getData(self::OG_METADATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setOgMetadata($value){
        return $this->setData(self::OG_METADATA, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getOgTitle()
    {
        $title = $this->getData(self::OG_TITLE);
        if (!$title) {
            $title = $this->getMetaTitle();
        }

        return trim($title);
    }

    /**
     * {@inheritdoc}
     */
    public function setOgTitle($value){
        return $this->setData(self::OG_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOgDescription()
    {
        $desc = $this->getData(self::OG_DESCRIPTION);
        if (!$desc) {
            $desc = $this->getMetaDescription();
        } else {
            $desc = strip_tags($desc);
            if (mb_strlen($desc) > 160) {
                $desc = mb_substr($desc, 0, 160);
            }
        }

        return trim($desc);
    }

    /**
     * {@inheritdoc}
     */
    public function setOgDescription($value){
        return $this->setData(self::OG_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOgImg(){
        return $this->getData(self::OG_IMG);
    }

    /**
     * {@inheritdoc}
     */
    public function setOgImg($value){
        return $this->setData(self::OG_IMG, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getOgType()
    {
        $type = $this->getData(self::OG_TYPE);
        if (!$type) {
            $type = 'article';
        }

        return trim($type);
    }

    /**
     * {@inheritdoc}
     */
    public function setOgType($value){
        return $this->setData(self::OG_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRealPostUrl(){
        return $this->getData(self::REAL_POST_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRealPostUrl($value){
        return $this->setData(self::REAL_POST_URL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRealImageUrl(){
        return $this->getData(self::REAL_IMAGE_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRealImageUrl($value){
        return $this->setData(self::REAL_IMAGE_URL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRealThumbnailUrl(){
        return $this->getData(self::REAL_THUMBNAIL_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setRealThumbnailUrl($value){
        return $this->setData(self::REAL_THUMBNAIL_URL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilteredContent()
    {
        if (!$this->hasData(self::FILTERED_CONTENT)) {
            $content = $this->filterProvider->getPageFilter()->filter(
                $this->getContent()
            );
            $this->setData(self::FILTERED_CONTENT, $content);
        }
        return $this->getData(self::FILTERED_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilteredContent($value){
        return $this->setData(self::FILTERED_CONTENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStores(){
        return $this->getData(self::STORES);
    }

    /**
     * {@inheritdoc}
     */
    public function setStores($value){
        return $this->setData(self::STORES, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategories(){
        $categories = $this->getData(self::CATEGORIES);
        if(!$categories){
            $categories = $this->getPostCategories();
        }
        return $categories;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategories($value){
        return $this->setData(self::CATEGORIES, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIds(){
        $return = $this->getData(self::CATEGORY_IDS);
        if(!$return){
            $categories = $this->getCategories();
            $return = [];
            if($categories){
                foreach($categories as $_category){
                    if(is_array($_category) && isset($_category["category_id"])){
                        $return[] = $_category["category_id"];
                    }else {
                        $return[] = (int)$_category;
                    }

                }
            }
        }
        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryIds($value){
        return $this->setData(self::CATEGORY_IDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryNames(){
        $return = $this->getData(self::CATEGORY_NAMES);
        if(!$return){
            $categories = $this->getCategories();
            $return = [];
            if($categories){
                foreach($categories as $_category){
                    if(is_array($_category) &&  isset($_category["name"])){
                        $return[] = $_category["name"];
                    }
                }
            }
        }
        return $return?$return:null;
    }


    /**
     * {@inheritdoc}
     */
    public function setCategoryNames($value){
        return $this->setData(self::CATEGORY_NAMES, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getCategoryIdentifiers(){
        $return = $this->getData(self::CATEGORY_IDENTIFIERS);
        if(!$return){
            $categories = $this->getCategories();
            $return = [];
            if($categories){
                foreach($categories as $_category){
                    if(is_array($_category) && isset($_category["identifier"])){
                        $return[] = $_category["identifier"];
                    }
                }
            }
        }
        return $return?$return:null;
    }


    /**
     * {@inheritdoc}
     */
    public function setCategoryIdentifiers($value){
        return $this->setData(self::CATEGORY_IDENTIFIERS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductsRelated(){
        return $this->getData(self::PRODUCTS_RELATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductsRelated($value){
        return $this->setData(self::PRODUCTS_RELATED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostsRelated(){
        return $this->getData(self::POSTS_RELATED);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostsRelated($value){
        return $this->setData(self::POSTS_RELATED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentCount(){
        return $this->getData(self::COMMENT_COUNT);
    }


    /**
     * {@inheritdoc}
     */
    public function setCommentCount($value){
        return $this->setData(self::COMMENT_COUNT, $value);
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
        \Ves\Blog\Api\Data\PostExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @return array
     */
    public function getRelated(){
        $result = [];
        $related = $this->getPostsRelated();
        $relatedItems = $this->getCollection()->addFieldToFilter('post_id',['in' => $related])->getData();
        $result ['related_posts']['items'] = $relatedItems;
        $result ['related_posts']['total_count'] = count($relatedItems);

        $relatedProducts = $this->getRelatedProducts();
        $relatedProducts = $this->productCollection->create()->addFieldToFilter('entity_id',['in' => $relatedProducts]);
        $relatedProductsItems = [];
        foreach ($relatedProducts as $key2 => $product) {
            $relatedProductsItems[$key2] = $product->load($product->getId())->getData();
            $relatedProductsItems[$key2]['model'] = $product;
        }
        $result ['related_products']['items'] = $relatedProductsItems;
        $result ['related_products']['total_count'] = count($relatedProducts);
        return $result;
    }
}
