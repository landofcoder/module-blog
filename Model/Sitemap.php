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

use Magento\Framework\App\ProductMetadataInterface;
use Ves\Blog\Api\SitemapConfigInterface;

/**
 * Deprecated
 * Used for Magento 2.1.x only to create blog_sitemap.xml
 * Overide sitemap
 */
class Sitemap extends \Magento\Sitemap\Model\Sitemap
{
    /**
     * Initialize sitemap items
     *
     * @return void
     */
    protected function _initSitemapItems()
    {
        parent::_initSitemapItems();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sitemapConfig = $objectManager->get(SitemapConfigInterface::class);

        $sitemapItems = [];
        if ($sitemapConfig->isEnabledSitemap(SitemapConfigInterface::HOME_PAGE)) {
            $sitemapItems[] = new \Magento\Framework\DataObject(
                [
                    'changefreq' => $sitemapConfig->getFrequency(SitemapConfigInterface::HOME_PAGE),
                    'priority' => $sitemapConfig->getPriority(SitemapConfigInterface::HOME_PAGE),
                    'collection' => \Magento\Framework\App\ObjectManager::getInstance()->create(
                        \Magento\Framework\Data\Collection::class
                    )->addItem(
                        \Magento\Framework\App\ObjectManager::getInstance()->create(
                            \Magento\Framework\DataObject::class
                        )->setData([
                            'updated_at' => '',
                            'url' => $objectManager->get(\Ves\Blog\Model\Url::class)->getBasePath(),
                        ])
                    )
                ]
            );
        }


        if ($sitemapConfig->isEnabledSitemap(SitemapConfigInterface::CATEGORIES_PAGE)) {
            $sitemapItems[] = new \Magento\Framework\DataObject(
                [
                    'changefreq' => $sitemapConfig->getFrequency(SitemapConfigInterface::CATEGORIES_PAGE),
                    'priority' => $sitemapConfig->getPriority(SitemapConfigInterface::CATEGORIES_PAGE),
                    'collection' => \Magento\Framework\App\ObjectManager::getInstance()->create(
                        \Ves\Blog\Model\Category::class
                    )->getCollection($this->getStoreId())
                        ->addStoreFilter($this->getStoreId())
                        ->addActiveFilter(),
                ]
            );
        }

        if ($sitemapConfig->isEnabledSitemap(SitemapConfigInterface::POSTS_PAGE)) {
            $sitemapItems[] = new \Magento\Framework\DataObject(
                [
                    'changefreq' => $sitemapConfig->getFrequency(SitemapConfigInterface::POSTS_PAGE),
                    'priority' => $sitemapConfig->getPriority(SitemapConfigInterface::POSTS_PAGE),
                    'collection' => \Magento\Framework\App\ObjectManager::getInstance()->create(
                        \Ves\Blog\Model\Post::class
                    )->getCollection($this->getStoreId())
                        ->addStoreFilter($this->getStoreId())
                        ->addActiveFilter(),
                ]
            );
        }

        $productMetadata = $objectManager->get(ProductMetadataInterface::class);
        if (version_compare($productMetadata->getVersion(), '2.3.0', '<')) {
            if($sitemapItems){
                foreach($sitemapItems as $k=>&$item){
                    if(isset($item['collection']) && $item['collection']){
                        foreach($item['collection'] as &$item_collection_item){
                            $item_url = $item_collection_item->getUrl(false);
                        }
                    }
                }
            }
            $this->_sitemapItems = $sitemapItems;
        } else {
            $this->_sitemapItems = [];
            foreach ($sitemapItems as $sitemapItem) {
                foreach ($sitemapItem->getCollection() as $item) {
                    $this->_sitemapItems[] = new \Magento\Framework\DataObject(
                        [
                            'url' => $item->getUrl(false),
                            'updated_at' => $item->getData('update_time'),
                            'priority' => $sitemapItem->getData('priority'),
                            'change_frequency' =>  $sitemapItem->getData('changefreq'),
                        ]
                    );
                }
            }
        }
    }

    /**
     * Disable save action
     * @return self
     */
    public function save()
    {
        return $this;
    }
}
