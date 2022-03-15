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

namespace Ves\Blog\Block\Adminhtml\Import\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        /**
         * Checking if user have permission to save information
         */
        if($this->_isAllowedAction('Ves_Setup::export')){
            $isElementDisabled = false;
        }else {
            $isElementDisabled = true;
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                    'data'    => [
                    'id'      => 'edit_form',
                    'action'  => $this->getData('action'),
                    'method'  => 'post',
                    'enctype' => 'multipart/form-data'
                ]
            ]
            );
        $form->setHtmlIdPrefix('blogimport_');

        $fieldset = $form->addFieldset('editortop', ['legend' => '']);

        $fieldset->addField(
            'dbname',
            'text',
            [
                'name'               => 'dbname',
                'label'              => __('Database Name'),
                'title'              => __('Database Name'),
                'required'           => true,
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('The database name you run in WP.'),
            ]
        );

        $fieldset->addField(
            'uname',
            'text',
            [
                'label'              => __('User Name'),
                'title'              => __('User Name'),
                'name'               => 'uname',
                'required'           => true,
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('Your WP MySQL username.')
            ]
        );

        $fieldset->addField(
            'pwd',
            'text',
            [
                'label'              => __('Password'),
                'title'              => __('Password'),
                'name'               => 'pwd',
                'required'           => true,
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('Your WP MySQL password.')
            ]
        );

        $fieldset->addField(
            'dbhost',
            'text',
            [
                'label'              => __('Database Host'),
                'title'              => __('Database Host'),
                'name'               => 'dbhost',
                'required'           => true,
                'value'              => 'localhost',
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('Your WP MySQL host.')
            ]
        );

        $fieldset->addField(
            'prefix',
            'text',
            [
                'label'              => __('Table Prefix'),
                'title'              => __('Table Prefix'),
                'name'               => 'prefix',
                'required'           => true,
                'value'              => 'wp_',
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('Your WP MySQL table prefix')
            ]
        );

        $fieldset->addField(
            'overwrite',
            'select',
            [
                'name'    => 'overwrite',
                'label'   => __('Overwrite Existing Post/Category'),
                'title'   => __('Overwrite Existing Post/Category'),
                'options' => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('<p>If set to <b>Yes</b>, the import data will override exist data. Check exits data according to the field <b>Identifier</b> of post and category</b>.</p>')
            ]
        );

        $fieldset->addField(
            'import_posts',
            'select',
            [
                'label'    => __('Import All Posts'),
                'title'    => __('Import All Posts'),
                'name'     => 'import_posts',
                'options'  => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'value'    => 1,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'import_comments',
            'select',
            [
                'label'    => __('Import All Comments'),
                'title'    => __('Import All Comments'),
                'name'     => 'import_comments',
                'options'  => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'value'    => 1,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'import_categories',
            'select',
            [
                'label'    => __('Import All Categories'),
                'title'    => __('Import All Categories'),
                'name'     => 'import_categories',
                'options'  => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'value'    => 1,
                'disabled' => $isElementDisabled
            ]
        );

        $form->setUseContainer(true);
		$this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Processing block html after rendering
     *
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        $form = $this->getForm();
        $htmlIdPrefix = $form->getHtmlIdPrefix();

        /**
         * Form template has possibility to render child block 'form_after', but we can't use it because parent
         * form creates appropriate child block and uses this alias. In this case we can't use the same alias
         * without core logic changes, that's why the code below was moved inside method '_afterToHtml'.
         */
        /** @var $formAfterBlock \Magento\Backend\Block\Widget\Form\Element\Dependence */
        $formAfterBlock = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Form\Element\Dependence',
            'adminhtml.block.widget.form.element.dependence'
        );
        $formAfterBlock->addFieldMap(
            $htmlIdPrefix . 'import_posts',
            'import_posts'
        )->addFieldMap(
            $htmlIdPrefix . 'import_comments',
            'import_comments'
        )->addFieldDependence(
            'import_comments',
            'import_posts',
            1
        );
        $html = $html . $formAfterBlock->toHtml();

        return $html;
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
}
