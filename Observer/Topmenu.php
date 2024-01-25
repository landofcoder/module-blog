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

namespace Ves\Blog\Observer;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Event\ObserverInterface;

class Topmenu implements ObserverInterface
{
    /**
     * @var \Ves\Blog\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_url;

    public function __construct(
        \Ves\Blog\Helper\Data $helper,
        \Magento\Framework\Url $url
    ) {
        $this->_helper = $helper;
        $this->_url = $url;
    }
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $enable = $this->_helper->getConfig('general_settings/enable');
        if($enable){
            $menu = $observer->getMenu();
            $tree = $menu->getTree();
            $data = [
                'name'      => $this->_helper->getConfig('general_settings/menu_title'),
                'id'        => 'ves-blog',
                'url'       => $this->_helper->getLatestPageUrl(),
            ];
            $node = new Node($data, 'id', $tree, $menu);
            $menu->addChild($node);
        }
        return $this;
    }
}
