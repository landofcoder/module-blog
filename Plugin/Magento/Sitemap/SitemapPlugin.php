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

namespace Ves\Blog\Plugin\Magento\Sitemap;

use Ves\Blog\Model\CategoryFactory;
use Ves\Blog\Model\PostFactory;
use Magento\Framework\DataObject;
use Magento\Sitemap\Model\Sitemap;

/**
 * Plugin for sitemap generation
 */
class SitemapPlugin
{
    /**
     * @var \Ves\Blog\Model\SitemapFactory
     */
    protected $sitemapFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var mixed
     */
    protected $config;

    /**
     * Generated sitemaps
     * @var array
     */
    protected $generated = [];

    /**
     * SitemapPlugin constructor.
     * @param \Ves\Blog\Model\SitemapFactory $sitemapFactory
     * @param CategoryFactory $categoryFactory
     * @param PostFactory $postFactory
     * @param null|\Ves\Blog\Helper\Data config
     */
    public function __construct(
        \Ves\Blog\Model\SitemapFactory $sitemapFactory,
        CategoryFactory $categoryFactory,
        PostFactory $postFactory,
        $config = null
    ) {
        $this->postFactory = $postFactory;
        $this->categoryFactory = $categoryFactory;
        $this->sitemapFactory = $sitemapFactory;
        $this->config = $config ?$config: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Ves\Blog\Helper\Data::class);
    }

    /**
     * Deprecated
     * Used for Magento 2.1.x only to create blog_sitemap.xml
     * Add magefan blog actions to allowed list
     * @param  \Magento\Framework\Model\AbstractModel $sitemap
     * @return array
     */
    public function afterGenerateXml(\Magento\Framework\Model\AbstractModel $sitemap, $result)
    {

        if ($this->isEnabled($sitemap)) {
            /* if ($this->isMageWorxXmlSitemap($sitemap) || !method_exists($sitemap, 'collectSitemapItems')) { */
                $sitemapId = $sitemap->getId() ?: 0;

                if (in_array($sitemapId, $this->generated)) {
                    return $result;
                }

                $this->generated[] = $sitemapId;

                $blogSitemap = $this->sitemapFactory->create();
                $blogSitemap->setData(
                    $sitemap->getData()
                );

                $blogSitemap->setSitemapFilename(
                    'blog_' . $sitemap->getSitemapFilename()
                );

                $blogSitemap->generateXml();
            /* } */
        }
        return $result;
    }

    /**
     * Deprecated
     * @param \Magento\Framework\Model\AbstractModel $sitemap
     * @param $result
     * @return mixed
     */
    public function afterCollectSitemapItems(\Magento\Framework\Model\AbstractModel $sitemap, $result)
    {
        return $result;
        /*
        if ($this->isEnabled($sitemap) && !$this->isMageWorxXmlSitemap($sitemap)) {
            $storeId = $sitemap->getStoreId();

            $sitemap->addSitemapItem(new DataObject(
                [
                    'changefreq' => 'weekly',
                    'priority' => '0.25',
                    'collection' => $this->categoryFactory->create()
                        ->getCollection($storeId)
                        ->addStoreFilter($storeId)
                        ->addActiveFilter(),
                ]
            ));

            $sitemap->addSitemapItem(new DataObject(
                [
                    'changefreq' => 'weekly',
                    'priority' => '0.25',
                    'collection' => $this->postFactory->create()
                        ->getCollection($storeId)
                        ->addStoreFilter($storeId)
                        ->addActiveFilter(),
                ]
            ));
        }

        return $result;
        */
    }

    /**
     * @param $sitemap
     * @return mixed
     */
    protected function isEnabled($sitemap)
    {
        return $this->config->isEnabled(
            $sitemap->getStoreId()
        );
    }

    /**
     * Deprecated
     * @param $sitemap
     * @return mixed
     */
    public function isMageWorxXmlSitemap($sitemap)
    {
        return (get_class($sitemap) == 'MageWorx\XmlSitemap\Model\Rewrite\Sitemap\Interceptor');
    }
}
