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
namespace Ves\Blog\Block\Adminhtml\Author\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Author Information'));

        $this->addTab(
                'main_section',
                [
                    'label' => __('General'),
                    'content' => $this->getLayout()->createBlock('Ves\Blog\Block\Adminhtml\Author\Edit\Tab\Main')->toHtml()
                ]
            );

        $this->addTab(
                'meta_section',
                [
                    'label' => __('SEO'),
                    'content' => $this->getLayout()->createBlock('Ves\Blog\Block\Adminhtml\Author\Edit\Tab\Meta')->toHtml()
                ]
            );
    }
}
