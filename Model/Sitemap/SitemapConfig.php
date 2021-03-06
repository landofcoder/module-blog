<?php
/**
 * Copyright © Ves (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Ves\Blog\Model\Sitemap;

use Ves\Blog\Api\SitemapConfigInterface;

/**
 * Class SitemapConfig
 * @package Ves\Blog\Model\Sitemap
 */
class SitemapConfig implements SitemapConfigInterface
{
    /**
     * @param string $page
     * @return bool
     */
    public function isEnabledSitemap($page)
    {
        $return = false;
        if($page == "index") {
            $return = true;
        }elseif($page == "category") {
            $return = true;
        }elseif($page == "post") {
            $return = true;
        }
        return $return;
    }

    /**
     * @param string $page
     * @return string
     */
    public function getFrequency($page)
    {
        switch ($page) {
            case 'index':
                $frequency = 'daily';
                break;
            case 'category':
                $frequency = 'daily';
                break;
            case 'post':
                $frequency = 'daily';
                break;
            default:
                $frequency = 'daily';
        }
        return $frequency;
    }

    /**
     * @param string $page
     * @return float
     */
    public function getPriority($page)
    {
        switch ($page) {
            case 'index':
                $priority = 1;
                break;
            case 'category':
                $priority = 0.8;
                break;
            case 'post':
                $priority = 0.5;
                break;
            default:
                $priority = 0.3;
        }
        return $priority;
    }
}