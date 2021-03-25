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

namespace Ves\Blog\Api;

/**
 * Interface SitemapConfigInterface
 * @package Ves\Blog\Api
 */
interface SitemapConfigInterface
{
    const HOME_PAGE = 'index';
    const CATEGORIES_PAGE = 'category';
    const POSTS_PAGE = 'post';

    /**
     * @param string $page
     * @return bool
     */
    public function isEnabledSitemap($page);

    /**
     * @param string $page
     * @return string
     */
    public function getFrequency($page);

    /**
     * @param string $page
     * @return float
     */
    public function getPriority($page);

}