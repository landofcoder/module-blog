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
namespace Ves\Blog\Block\Adminhtml\Category\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Ves\Blog\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection;

    protected $_drawLevel;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Ves\Blog\Model\ResourceModel\Category\Collection $categoryCollection,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _getSpaces($n)
    {
        $s = '';
        for($i = 0; $i < $n; $i++) {
            $s .= '--- ';
        }

        return $s;
    }

    public function drawItems($collection, $cat, $level = 0){
        foreach ($collection as $_cat) {
            if($_cat->getParentId() == $cat['id']){
                $cat1 = [
                    'label' => $_cat->getName(),
                    'value' => $_cat->getId(),
                    'id' => $_cat->getId(),
                    'parent_id' => $_cat->getParentId(),
                    'level' => 0,
                    'postion' => $_cat->getCatPosition()
                ];
                $children[] = $this->drawItems($collection, $cat1, $level+1);
                $cat['children'] = $children;
            }
        }
        $cat['level'] = $level;
        return $cat;
    }

    public function drawSpaces($cats){
        if(is_array($cats)){
            foreach ($cats as $k => $v) {
                $v['label'] = $this->_getSpaces($v['level']) . $v['label'];
                $this->_drawLevel[] = $v;
                if(isset($v['children']) && $children = $v['children']){
                    $this->drawSpaces($children);
                }
            }
        }
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('blog_category');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ves_Blog::category_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('category_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Information')]);

        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', ['name' => 'category_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('Identifier'),
                'title' => __('Identifier'),
                'required' => true,
                'note' => __('Relative to Web Site Base URL'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'disabled' => $isElementDisabled
            ]
            );

        $categories[] = ['label' => __('Please select'), 'value' => 0];

        $this->_drawLevel = $categories;

        $collection = $this->_categoryCollection
            ->addFieldToFilter('category_id', array('neq' => $model->getId()))
            ->setOrder('cat_position');

        $collection = $this->getCatCollection();
        $cats = [];
        foreach ($collection as $_cat) {
            if(!$_cat->getParentId()){
                $cat = [
                    'label' => $_cat->getName(),
                    'value' => $_cat->getId(),
                    'id' => $_cat->getId(),
                    'parent_id' => $_cat->getParentId(),
                    'level' => 0,
                    'postion' => $_cat->getCatPosition()
                ];
                $cats[] = $this->drawItems($collection, $cat);
            }
        }
        $this->drawSpaces($cats);

        if (count($this->_drawLevel)) {
            $fieldset->addField(
                'parent_id',
                'select',
                [
                    'name' => 'parent_id',
                    'label' => __('Parent Category'),
                    'title' => __('Parent Category'),
                    'values' => $this->_drawLevel,
                    'disabled' => $isElementDisabled
                ]
            );
        }

        $contentField = $fieldset->addField(
            'description',
            'editor',
            [
                'label' => __('Description'),
                'title' => __('Description'),
                'name' => 'description',
                'style' => 'height:20em;',
                'disabled' => $isElementDisabled,
                'config' => $wysiwygConfig
            ]
        );


        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'cat_position',
            'text',
            [
                'name' => 'cat_position',
                'label' => __('Position'),
                'title' => __('Position'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Category Status'),
                'name' => 'is_active',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $this->_eventManager->dispatch('adminhtml_blog_category_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getCatCollection(){
        $model = $this->_coreRegistry->registry('blog_category');
        $collection = $this->_categoryCollection
            ->addFieldToFilter('category_id', array('neq' => $model->getId()))
            ->setOrder('cat_position');
        return $collection;
    }

    public function getCats($categories, $cats = [], $level = 0){
        foreach ($cats as $k => $v) {

        }
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Category Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Category Information');
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
