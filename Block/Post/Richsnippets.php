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
namespace Ves\Blog\Block\Post;

use Magento\Store\Model\ScopeInterface;

/**
 * Blog post view rich snippets
 */
class Richsnippets extends Opengraph
{
    /**
     * @param  array
     */
    protected $_options;

    /**
     * Retrieve snipet params
     *
     * @return array
     */
    public function getOptions()
    {
        if ($this->_options === null) {
            $post = $this->getPost();

            $logoBlock = $this->getLayout()->getBlock('logo');
            if (!$logoBlock) {
                $logoBlock = $this->getLayout()->getBlock('amp.logo');
            }
            
            $this->_options = [
                '@context' => 'http://schema.org',
                '@type' => 'BlogPosting',
                '@id' => $post->getPostUrl(),
                'author' => $this->getAuthor(),
                'headline' => $this->getTitle(),
                'description' => $this->getContent(),
                'datePublished' => $post->getPublishDate('c'),
                'dateModified' => $post->getUpdateDate('c'),
                'image' => [
                    '@type' => 'ImageObject',
                    'url' => $this->getImage() ?:
                        ($logoBlock ? $logoBlock->getLogoSrc() : ''),
                    'width' => 720,
                    'height' => 720,
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $this->getPublisher(),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $logoBlock ? $logoBlock->getLogoSrc() : '',
                    ],
                ],
                'mainEntityOfPage' => $this->_url->getBaseUrl(),
            ];
        }

        return $this->_options;
    }

    /**
     * Retrieve author name
     *
     * @return array
     */
    public function getAuthor()
    {
        $post = $this->getPost();
        if ($author = $this->_blogHelper->getPostAuthor($post)) {
            if ($author->getNickName()) {
                return $author->getNickName();
            }
        }

        // if no author name return name of publisher
        return $this->getPublisher();
    }

    /**
     * Retrieve publisher name
     *
     * @return array
     */
    public function getPublisher()
    {
        $publisher =  $this->_scopeConfig->getValue(
            'general/store_information/name',
            ScopeInterface::SCOPE_STORE
        );

        if (!$publisher) {
            $publisher = 'Magento2 Store';
        }

        return $publisher;
    }

    /**
     * Render html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '<script type="application/ld+json">'
            . json_encode($this->getOptions())
            . '</script>';
    }
}
