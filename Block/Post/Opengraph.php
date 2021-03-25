<?php
/**
 * Copyright © Venustheme (Venustheme@gmail.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */
namespace Ves\Blog\Block\Post;

use Magento\Store\Model\ScopeInterface;

/**
 * Blog post view opengraph
 */
class Opengraph extends \Ves\Blog\Block\Post\View
{
    /**
     * Retrieve page type
     *
     * @return string
     */
    public function getType()
    {
        return $this->stripTags(
            $this->getPost()->getOgType()
        );
    }

    /**
     * Retrieve page title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->stripTags(
            $this->getPost()->getOgTitle()
        );
    }

    /**
     * Retrieve page short description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->stripTags(
            $this->getPost()->getOgDescription()
        );
    }

    /**
     * Retrieve page url
     *
     * @return string
     */
    public function getPageUrl()
    {
        $post = $this->getPost();
        $post_url = $this->_blogHelper->getPostUrl($post);
        return $this->stripTags(
            $post_url
        );
    }

    /**
     * Retrieve page main image
     *
     * @return string | null
     */
    public function getImage()
    {
        $image = $this->getPost()->getOgImage();

        if (!$image) {
            $image = $this->getPost()->getImageUrl();
        }

        if ($image) {
            return $this->stripTags($image);
        }
    }
}
