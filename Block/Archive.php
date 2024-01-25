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
namespace Ves\Blog\Block;

class Archive extends \Magento\Framework\View\Element\Template
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
        \Magento\Framework\Url $url,
        array $data = []
    ) {
        $this->_blogHelper = $blogHelper;
        $this->_coreRegistry = $registry;
        $this->_postFactory = $postFactory;
        $this->_url = $url;
        parent::__construct($context, $data);

    }

    /**
     * @var string
     */
    protected $_widgetKey = 'archive';

    /**
     * Available months
     * @var array
     */
    protected $_months;

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        $store = $this->_storeManager->getStore();
        $this->_postCollection = $this->_postFactory->getCollection()
        ->addFieldToFilter('is_active',1)
        ->addStoreFilter($store)
        ->setCurPage(1);

        $this->_postCollection->getSelect()->group(
            'MONTH(main_table.creation_time)',
            'DESC'
        );
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

    /**
     * Retrieve available months
     * @return array
     */
    public function getMonths()
    {
        if (is_null($this->_months)) {
            $this->_months = array();
            $this->_preparePostCollection();
            foreach($this->_postCollection as $post) {
                $time = strtotime($post->getData('creation_time'));
                if(isset($this->_months[date('Y-m', $time)]['count'])){
                $this->_months[date('Y-m', $time)]['count'] =  (int)$this->_months[date('Y-m', $time)]['count']+1;
                }else{
                    $this->_months[date('Y-m', $time)]['count'] = 1;
                }
                $this->_months[date('Y-m', $time)]['time'] = $time;
            }
        }
        usort($this->_months, function($a, $b){
            return $b['time'] - $a['time'];
        });
        return $this->_months;
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
     * Retrieve archive url by time
     * @param  int $time
     * @return string
     */
    public function getTimeUrl($time)
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $url_prefix = $this->getConfig('general_settings/url_prefix');
        $url_suffix = $this->getConfig('general_settings/url_suffix');
        $urlPrefix = '';
        if($url_prefix){
            $urlPrefix = $url_prefix.'/';
        }
        return $url . $urlPrefix . 'archive/' . date('Y-m', $time);
    }

    /**
     * Retrieve blog identities
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_blog_archive_widget'];
    }

}
