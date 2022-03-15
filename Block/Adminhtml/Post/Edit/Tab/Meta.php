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
namespace Ves\Blog\Block\Adminhtml\Post\Edit\Tab;

class Meta extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ves_Blog::post_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }


        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('post_');

        $model = $this->_coreRegistry->registry('current_post');

        $fieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('SEO'), 'class' => 'fieldset-wide']
            );

        $fieldset->addField(
            'canonical_url',
            'text',
            [
            'name' => 'canonical_url',
            'label' => __('Canonical URL'),
            'title' => __('Canonical URL'),
            'note' => __('The canonical URL that this page should poin to, leave empty to default to post link'),
            'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'page_title',
            'text',
            [
            'name' => 'page_title',
            'label' => __('Page Title'),
            'title' => __('Page Title'),
            'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'meta_keywords',
            'textarea',
            [
            'name' => 'meta_keywords',
            'label' => __('Keywords'),
            'title' => __('Meta Keywords'),
            'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'meta_description',
            'textarea',
            [
            'name' => 'meta_description',
            'label' => __('Description'),
            'title' => __('Meta Description'),
            'disabled' => $isElementDisabled
            ]
            );

        $fieldset = $form->addFieldset(
            'opengraph_fieldset',
            ['legend' => __('Open Graph Metadata'), 'class' => 'fieldset-wide']
            );

        $fieldset->addField(
            'og_title',
            'text',
            [
            'name' => 'og_title',
            'label' => __('Og Title'),
            'title' => __('Og Title'),
            'note' => __('Leave blank to use Meta Title by default.'),
            'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'og_description',
            'textarea',
            [
            'name' => 'og_description',
            'label' => __('Og Description'),
            'title' => __('Og Description'),
            'note' => __('Leave blank to use Meta Description by default.'),
            'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'og_img',
            'image',
            [
                'name' => 'og_img',
                'label' => __('Og Image'),
                'title' => __('Og Image'),
                'note' => __('Leave blank to use Main Image or Thumbnail Image by default. Allow image type: jpg, jpeg, gif, png.'),
                'disabled' => $isElementDisabled
            ]
            );

        $fieldset->addField(
            'og_type',
            'text',
            [
            'name' => 'og_type',
            'label' => __('Og Type'),
            'title' => __('Og Type'),
            'note' => __('Leave blank to use "article" type by default.'),
            'disabled' => $isElementDisabled
            ]
            );

        $this->_eventManager->dispatch('adminhtml_blog_post_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('SEO');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('SEO');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
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
