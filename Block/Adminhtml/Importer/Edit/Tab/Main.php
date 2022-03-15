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
namespace Ves\Blog\Block\Adminhtml\Importer\Edit\Tab;

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
     * @var \Ves\Blog\Model\ResourceModel\Importer\Collection
     */
    protected $_importerCollection;

    protected $_drawLevel;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param \Ves\Blog\Model\ResourceModel\Importer\Collection $importerCollection
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Ves\Blog\Model\ResourceModel\Importer\Collection $importerCollection,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_importerCollection = $importerCollection;
        parent::__construct($context, $registry, $formFactory, $data);
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
        $model = $this->_coreRegistry->registry('blog_importer');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ves_Blog::importer_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('importer_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Information')]);

        if ($model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'blog_base_url',
            'text',
            [
                'name' => 'blog_base_url',
                'label' => __('Blog Base Url'),
                'title' => __('Blog Base Url'),
                'required' => false,
                'disabled' => $isElementDisabled,
                'after_element_html' => __('The blog base url use add for custom real post url. Empty to disable')
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Importer Status'),
                'name' => 'is_active',
                'options' => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'disabled' => $isElementDisabled
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $fieldset->addField(
            'import_type',
            'select',
            [
                'name'    => 'import_type',
                'label'   => __('Import Type'),
                'title'   => __('Import Type'),
                'options' => [
                    'wordpress' => __('Wordpress')
                ],
                'disabled'           => $isElementDisabled
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
            'password',
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
            'password',
            [
                'label'              => __('Password'),
                'title'              => __('Password'),
                'name'               => 'pwd',
                'required'           => false,
                'disabled'           => $isElementDisabled,
                'note' => __('Your WP MySQL password.')
            ]
        );



        $fieldset->addField(
            'prefix',
            'text',
            [
                'label'              => __('Table Prefix'),
                'title'              => __('Table Prefix'),
                'name'               => 'prefix',
                'required'           => false,
                'value'              => 'wp_',
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('Your WP MySQL table prefix')
            ]
        );

        $fieldset->addField(
            'connect_charset',
            'text',
            [
                'label'              => __('DB Connect Charset'),
                'title'              => __('DB Connect Charset'),
                'name'               => 'connect_charset',
                'required'           => false,
                'value'              => 'utf8mb',
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('Connect DB Charset. Default: utf8mb')
            ]
        );

        $fieldset->addField(
            'default_author_id',
            'text',
            [
                'name'               => 'default_author_id',
                'label'              => __('Default Magento 2 Blog Author ID'),
                'title'              => __('Default Magento 2 Blog Author ID'),
                'required'           => false,
                'disabled'           => $isElementDisabled,
                'after_element_html' => __('Input ID of blog author on magento 2.'),
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'default_store_id',
                'select',
                [
                    'name'     => 'default_store_id',
                    'label'    => __('Default Store View'),
                    'title'    => __('Default Store View'),
                    'required' => false,
                    'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'default_store_id',
                'hidden',
                ['name' => 'default_store_id', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setDefaultStoreId($this->_storeManager->getStore(true)->getId());
        }

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
            'convert_image_link',
            'select',
            [
                'label'    => __('Allow Convert Image Link'),
                'title'    => __('Allow Convert Image Link'),
                'name'     => 'convert_image_link',
                'options'  => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
                'disabled' => $isElementDisabled
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
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'import_tags',
            'select',
            [
                'label'    => __('Import All Tags'),
                'title'    => __('Import All Tags'),
                'name'     => 'import_tags',
                'options'  => [
                    1 => __('Yes'),
                    0 => __('No')
                ],
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
                'disabled' => $isElementDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_blog_importer_edit_tab_main_prepare_form', ['form' => $form]);

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
        return __('Importer Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Importer Information');
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
