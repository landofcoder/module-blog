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

class Toplinks extends \Magento\Framework\View\Element\Template
{
	/**
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Ves\Blog\Helper\Data                            $helper
	 * @param array                                            $data
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Ves\Blog\Helper\Data $helper,
		array $data = []
	) {
		parent::__construct($context, $data);
		$this->_helper = $helper;
	}

	/**
     * Render block HTML
     *
     * @return string
     */
	protected function _toHtml()
	{
		if(!$this->_helper->getConfig('general_settings/enable')) return;
		$page_title = $this->_helper->getConfig('blog_latest_page/page_title');
		$route = $this->_helper->getLatestPageUrl();
		$link = '<li><a href="' . $route . '"> ' . $page_title . ' </a></li>';
		return $link;
	}
}
