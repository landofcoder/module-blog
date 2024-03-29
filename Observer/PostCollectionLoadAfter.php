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

use Magento\Framework\Event\ObserverInterface;

class PostCollectionLoadAfter implements ObserverInterface
{
    protected $authFactory;

	public function __construct(
		\Magento\Backend\Model\AuthFactory $authFactory
	) {
		$this->authFactory = $authFactory;
	}

    /**
     * @inheritdoc
     */
	public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$collection = $observer->getPostCollection();
    	$collection->getSelect()->where('main_table.creation_time <= NOW()');
    }
}
