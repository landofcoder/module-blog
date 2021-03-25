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
namespace Ves\Blog\Controller\Feed;

use Magento\Framework\Exception\NotFoundException;

class Index extends \Magento\Rss\Controller\Feed
{
    /**
     * Index action
     *
     * @return void
     * @throws NotFoundException
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        try {
            $provider = $this->rssManager->getProvider($type);
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundException(__($e->getMessage()));
        }

        if ($provider->isAuthRequired() && !$this->auth()) {
            return;
        }

        /** @var $rss \Magento\Rss\Model\Rss */
        $rss = $this->rssFactory->create();
        $rss->setDataProvider($provider);

        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
        $this->getResponse()->setBody($rss->createRssXml());
    }
}