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
namespace Ves\Blog\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Group Collection
     */
    protected $_groupCollection;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    protected $_config = [];

    /**
     * Template filter factory
     *
     * @var \Magento\Catalog\Model\Template\Filter\Factory
     */
    protected $_templateFilterFactory;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    protected $_postCategories = NULL;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    protected $_postFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $_request;

    protected $_resource;

    protected $_frontendUrlBuilder;
    protected $_userFactory;
    protected $_authorFactory;

    const XML_PATH_AJAXSCROLL_ENABLED = 'ajaxscroll_selectors/enable_ajaxscroll';
    const XML_PATH_BLOG_ENABLED = 'general_settings/enable';

	/**
     * @param \Magento\Framework\App\Helper\Context
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Cms\Model\Template\FilterProvider
     * @param \Magento\Framework\App\ResourceConnection
     * @param \Magento\Framework\UrlInterface
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     * @param \Magento\User\Model\User
     * @param \Ves\Blog\Model\Post
     * @param \Magento\Framework\Registry
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Url $frontendUrlBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\User\Model\User $userFactory,
        \Ves\Blog\Model\Post $postFactory,
        \Ves\Blog\Model\Author $authorFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->_request            = $context->getRequest();
        $this->_filterProvider     = $filterProvider;
        $this->_storeManager       = $storeManager;
        $this->_resource           = $resource;
        $this->_frontendUrlBuilder = $frontendUrlBuilder;
        $this->_userFactory        = $userFactory;
        $this->_localeDate         = $localeDate;
        $this->_postFactory        = $postFactory;
        $this->_coreRegistry       = $registry;
        $this->_authorFactory      = $authorFactory;
    }

    /**
     * Retrieve translated & formated date
     * @param  string $format
     * @param  string $dateOrTime
     * @return string
     */
    public static function getTranslatedDate($format, $dateOrTime)
    {
        $time = is_numeric($dateOrTime) ? $dateOrTime : strtotime($dateOrTime);
        $month = ['F' => '%1', 'M' => '%2'];

        foreach ($month as $from => $to) {
            $format = str_replace($from, $to, $format);
        }

        $date = date($format, $time);

        foreach ($month as $to => $from) {
            $date = str_replace($from, __(date($to, $time)), $date);
        }

        return $date;
    }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
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

    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getAuthorUrl($author)
    {
        if($author) {
            $url = $this->_storeManager->getStore()->getBaseUrl();
            $url_prefix = $this->getConfig('general_settings/url_prefix');
            $url_suffix = $this->getConfig('general_settings/url_suffix');
            $urlPrefix = '';
            if($url_prefix){
                $urlPrefix = $url_prefix.'/';
            }
            return $url . $urlPrefix . 'author/' . $author->getUserName();
        }
        return "";
    }

    public function getCategoryUrl($alias)
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url . $urlPrefix . $alias . $url_suffix;
    }

    public function getPostAuthor($post){
        $authorId = $post->getUserId();
        $author = $this->_authorFactory->loadByUserId($authorId);
        if($author->getIsView()) {
            return $author;
        }
        return false;
    }

    public function getTagUrl($alias){
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url . $urlPrefix . 'tag/' . $alias;
    }

    public function getLatestPageUrl(){
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url . $urlPrefix;
    }

    public function getAuthorsPageUrl(){
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $blog_url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_prefix = $this->getConfig('other_settings/authors_url');
        $urlPrefix = $blog_url_prefix.'/author/list/';
        if($url_prefix){
            $urlPrefix = $blog_url_prefix."/".$url_prefix.'/';
        }
        return $url . $urlPrefix;
    }

    public function formatDate(
        $date = null,
        $format = \IntlDateFormatter::SHORT,
        $showTime = false,
        $timezone = null
        ) {
        $date = $date instanceof \DateTimeInterface ? $date : new \DateTime($date);
        return $this->_localeDate->formatDateTime(
            $date,
            $format,
            $showTime ? $format : \IntlDateFormatter::NONE,
            null,
            $timezone
            );
    }

    public function getFormatDate($date, $type = 'full'){
        $result = '';
        switch ($type) {
            case 'full':
            $result = $this->formatDate($date, \IntlDateFormatter::FULL);
            break;
            case 'long':
            $result = $this->formatDate($date, \IntlDateFormatter::LONG);
            break;
            case 'medium':
            $result = $this->formatDate($date, \IntlDateFormatter::MEDIUM);
            break;
            case 'short':
            $result = $this->formatDate($date, \IntlDateFormatter::SHORT);
            break;
        }
        return $result;
    }

    public function getPostUrlLinkAttr($post){
        $attr = '';
        $enable_custom_post_url = $this->getConfig("other_settings/use_custom_post_link");
        $link_follow = $this->getConfig("other_settings/link_follow");
        $real_post_url = $post->getData("real_post_url");
        if($enable_custom_post_url && $post->hasData("real_post_url") && $link_follow && $real_post_url){
            return ' rel="'.$link_follow.'"';
        }
        return $attr;
    }

    public function getPostUrl($post)
    {
        $enable_custom_post_url = $this->getConfig("other_settings/use_custom_post_link");
        $real_post_url = $post->getData("real_post_url");
        if($enable_custom_post_url && $post->hasData("real_post_url") && $real_post_url){
            return $real_post_url;
        }

        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $categoriesUrls = $this->getConfig('general_settings/categories_urls');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        $categoryUrl = '';

        if($categoriesUrls){
            $category = $post->getData("categories");
            if($category && isset($category[0]['identifier']) && $category[0]['identifier']!=''){
                $categoryUrl = $category[0]['identifier'] . '/';
            }

        }
        return $url . $urlPrefix . $categoryUrl . $post->getIdentifier() . $url_suffix;
    }


    public function getPostPreviewUrl($post)
    {
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $categoriesUrls = $this->getConfig('general_settings/categories_urls');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        $categoryUrl = '';

        if($categoriesUrls){
            $category = $post["categories"];
            if($category && isset($category[0]['identifier']) && $category[0]['identifier']!=''){
                $categoryUrl = $category[0]['identifier'] . '/';
            }

        }
        return $urlPrefix . $categoryUrl . $post['identifier'] . $url_suffix;
    }

    public function getSearchFormUrl(){
        $url        = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $urlPrefix  = '';
        if ($url_prefix) {
            $urlPrefix = $url_prefix . '/';
        }
        return $url . $urlPrefix . 'search';
    }


    public function getPostCategories($postId){
        if($this->_postCategories == NULL){
            $connection = $this->_resource->getConnection();
            $select = 'SELECT * FROM ' . $this->_resource->getTableName('ves_blog_post_category') . ' WHERE post_id = ' . $postId . ' ORDER BY position ASC';
            $this->_postCategories = $connection->fetchAll($select);
        }
        return $this->_postCategories;
    }

    public function subString( $text, $length = 100, $replacer ='...', $is_striped=true ){
        if($length == 0) return $text;
        $text = ($is_striped==true)?strip_tags($text):$text;
        if(strlen($text) <= $length){
            return $text;
        }
        $text = substr($text,0,$length);
        $pos_space = strrpos($text,' ');
        return substr($text,0,$pos_space).$replacer;
    }

    /**
     * @param string|null $route
     * @param array|null $params
     * @return string
     */
    public function getUrl($route, $params = [])
    {
        return $this->_frontendUrlBuilder->getUrl($route, $params);
    }

    public function getPostCategoryUrl($post)
    {
        $category = '';
        $categories = $this->getPostCategories($post->getPostId());
        if(!empty($categories)){
            $connection = $this->_resource->getConnection();
            $select = 'SELECT * FROM ' . $this->_resource->getTableName('ves_blog_category') . ' WHERE category_id = ' . $categories[0]['category_id'];
            $category = $connection->fetchAll($select);
            return $category;
        }
        return $category;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        $parsed = parse_url($file);
        if (empty($parsed['scheme'])) {
            return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $file;
        }else{
            return $file;
        }
    }

    public function getPostByCategory($categoryId){
        $collection = $this->_postFactory->getCollection();
    }

    public function getCoreRegistry(){
        return $this->_coreRegistry;
    }

    public function getSearchKey(){
        return $this->_request->getParam('s');
    }
    public function isEnabled($storeId = null)
    {
        return $this->getConfig(self::XML_PATH_BLOG_ENABLED, $storeId);
    }
    /**
     * Escape quotes in java script
     *
     * @param mixed $data
     * @param string $quote
     * @return mixed
     */
    public function jsQuoteEscape($data, $quote='\'')
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $item) {
                $result[] = str_replace($quote, '\\'.$quote, $item);
            }
            return $result;
        }
        return str_replace($quote, '\\'.$quote, $data);
    }

    /**
     * @param $data_array
     * @return array
     */
    public function xss_clean_array($data_array)
    {
        $result = [];
        if (is_array($data_array)) {
            foreach ($data_array as $key => $val) {
                $val = $this->xss_clean($val);
                $result[$key] = $val;
            }
        }
        return $result;
    }

    /**
     * @param $data
     * @return string|string[]|null
     */
    public function xss_clean($data)
    {
        if (!is_string($data)) {
            return $data;
        }
        // Fix &entity\n;
        $data = str_replace(['&amp;', '&lt;', '&gt;'], ['&amp;amp;', '&amp;lt;', '&amp;gt;'], $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2nojavascript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu',
            '$1=$2novbscript...',
            $data
        );
        $data = preg_replace(
            '#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u',
            '$1=$2nomozbinding...',
            $data
        );

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i',
            '$1>',
            $data
        );
        $data = preg_replace(
            '#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu',
            '$1>',
            $data
        );

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace(
                '#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i',
                '',
                $data
            );
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }
}
