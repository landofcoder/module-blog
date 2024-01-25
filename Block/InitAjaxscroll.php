<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://Venustheme.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Blog
 *
 * @copyright  Copyright (c) 2017 Venustheme (http://www.Venustheme.com/)
 * @license    http://www.Venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Blog\Block;

class InitAjaxscroll extends \Magento\Framework\View\Element\Template
{

    protected $coreRegistry = null;
    /**
     * @var Ves\Blog\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ves\Blog\Helper\Data                      $helperData
     * @param \Magento\Framework\Registry                      $registry
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ves\Blog\Helper\Data $helperData,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData    = $helperData;
        $this->coreRegistry = $registry;
    }

    public function isEnable()
    {
        $fullAction       = $this->getRequest()->getFullActionName();
        $enable_latest   = $this->helperData->getConfig('ajaxscroll_selectors/enable_latest');
        $enable_category = $this->helperData->getConfig('ajaxscroll_selectors/enable_category');
        $enable_search = $this->helperData->getConfig('ajaxscroll_selectors/enable_search');
        $enable_archive = $this->helperData->getConfig('ajaxscroll_selectors/enable_archive');
        $enable_tag = $this->helperData->getConfig('ajaxscroll_selectors/enable_tag');
        $enable_author = $this->helperData->getConfig('ajaxscroll_selectors/enable_author');
        $enable_comment = $this->helperData->getConfig('ajaxscroll_selectors/enable_comment');
        if (($enable_latest && $fullAction == 'vesblog_latest_view') || ($enable_category && $fullAction == 'vesblog_category_view')|| ($enable_search && $fullAction == 'vesblog_search_result')|| ($enable_archive && $fullAction == 'vesblog_archive_view')|| ($enable_tag && $fullAction == 'vesblog_tag_view')|| ($enable_author && $fullAction == 'vesblog_author_view')|| ($enable_comment && $fullAction == 'vesblog_post_view')) {
            return true;
        }

        return false;
    }

    /**
     * @return bool|false
     */
    public function getLoaderImage()
    {

        $url = $this->helperData->getConfig('ajaxscroll_selectors/loading_image');
        if(!empty($url)) {
            $url = strpos($url, 'http') === 0 ? $url : $this->getViewFileUrl($url);
        }
        return empty($url) ? false : $url;
    }
}
