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

namespace Ves\Blog\Block\Adminhtml\Import;

class Wordpress extends \Magento\Backend\Block\Widget\Form\Container
{

    /**
     * Initialize wordpress import block
     *
     * @return void
     */
    protected function _construct()
    {
    	$this->_objectId = 'id';
    	$this->_blockGroup = 'Ves_Blog';
    	$this->_controller = 'adminhtml_import';

    	parent::_construct();

    	if (!$this->_isAllowedAction('Ves_Blog::import')) {
    		$this->buttonList->remove('save');
    	} else {
    		$this->buttonList->update(
    			'save', 'label', __('Import')
    			);
    	}

    	$this->buttonList->remove('delete');
    }


    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
    	return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * Get form save URL
     *
     * @see getFormActionUrl()
     * @return string
     */
    public function getSaveUrl()
    {
    	return $this->getUrl('*/*/run', ['_current' => true]);
    }
}
