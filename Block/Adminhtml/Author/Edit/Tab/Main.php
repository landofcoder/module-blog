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
namespace Ves\Blog\Block\Adminhtml\Author\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_dateTime;

    protected $_category;

    protected $_thumbnailType;
    protected $_videoType;

    protected $_user;

     /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    protected $_postFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\Data\FormFactory
     * @param \Magento\Store\Model\System\Store
     * @param \Magento\Framework\Stdlib\DateTime\DateTime
     * @param \Ves\Blog\Model\Category
     * @param \Ves\Blog\Model\Config\Source\ThumbnailType
     * @param \Ves\Blog\Model\Config\Source\VideoType
     * @param \Ves\Blog\Model\Config\Source\User
     * @param \Magento\Cms\Model\Wysiwyg\Config
     * @param \Magento\Catalog\Model\Product
     * @param array
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ves\Blog\Model\Category $category,
        \Ves\Blog\Model\Config\Source\ThumbnailType $thumbnailType,
        \Ves\Blog\Model\Config\Source\VideoType $videoType,
        \Ves\Blog\Model\Config\Source\User $user,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Ves\Blog\Model\Post $postFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->_systemStore   = $systemStore;
        $this->_dateTime      = $dateTime;
        $this->_category      = $category;
        $this->_thumbnailType = $thumbnailType;
        $this->_videoType     = $videoType;
        $this->_user          = $user;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_postFactory   = $postFactory;
        $this->authSession    = $authSession;
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
        $model = $this->_coreRegistry->registry('current_author');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ves_Blog::author')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('comment_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('About Yourself')]);

        if ($model->getId()) {
            $fieldset->addField('user_id', 'hidden', ['name' => 'user_id']);
        }

        $fieldset->addField(
            'user_name',
            'text',
            [
                'name'     => 'user_name',
                'label'    => __('User Name'),
                'title'    => __('User Name'),
                'class'    => 'validate-identifier',
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'nick_name',
            'text',
            [
                'name'     => 'nick_name',
                'label'    => __('Nick Name'),
                'title'    => __('Nick Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'is_view',
            'select',
            [
                'label' => __('Is View On Frontend?'),
                'title' => __('Is View On Frontend?'),
                'name' => 'is_view',
                'options' => $model->getAvailableStatuses(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'avatar',
            'image',
            [
                'name'     => 'avatar',
                'label'    => __('Avatar'),
                'title'    => __('Avatar'),
                'disabled' => $isElementDisabled
            ]
        );

        $contentField = $fieldset->addField(
            'description',
            'editor',
            [
                'label'    => __('Biographical Info'),
                'title'    => __('Biographical Info'),
                'name'     => 'description',
                'style'    => 'height:20em;',
                'disabled' => $isElementDisabled,
                'config'   => $wysiwygConfig
            ]
        );

        $fieldset = $form->addFieldset('contact_fieldset', ['legend' => __('Contact Info')]);

        $fieldset->addField(
            'email',
            'text',
            [
                'name'     => 'email',
                'label'    => __('Email'),
                'title'    => __('Email'),
                'disabled' => true
            ]
        );

        $fieldset->addField(
            'googleplus',
            'text',
            [
                'name'     => 'network[googleplus]',
                'label'    => __('Google+'),
                'title'    => __('Google+')
            ]
        );

        $fieldset->addField(
            'twitter',
            'text',
            [
                'name'     => 'network[twitter]',
                'label'    => __('Twitter'),
                'title'    => __('Twitter')
            ]
        );

        $fieldset->addField(
            'facebook',
            'text',
            [
                'name'     => "network[facebook]",
                'label'    => __('Facebook'),
                'title'    => __('Facebook')
            ]
        );

        $fieldset->addField(
            'digg',
            'text',
            [
                'name'     => "network[digg]",
                'label'    => __('Digg'),
                'title'    => __('Digg')
            ]
        );

        $fieldset->addField(
            'dribbble',
            'text',
            [
                'name'     => 'network[dribbble]',
                'label'    => __('Dribbble'),
                'title'    => __('Dribbble')
            ]
        );
        $fieldset->addField(
            'flickr',
            'text',
            [
                'name'     => 'network[flickr]',
                'label'    => __('Flickr'),
                'title'    => __('Flickr')
            ]
        );

        $fieldset->addField(
            'instagram',
            'text',
            [
                'name'     => 'network[instagram]',
                'label'    => __('Instagram'),
                'title'    => __('Instagram')
            ]
        );

        $fieldset->addField(
            'linkedin',
            'text',
            [
                'name'     => 'network[linkedin]',
                'label'    => __('Linkedin'),
                'title'    => __('Linkedin')
            ]
        );

        $fieldset->addField(
            'pinterest',
            'text',
            [
                'name'     => 'network[pinterest]',
                'label'    => __('Pinterest'),
                'title'    => __('Pinterest')
            ]
        );

        $fieldset->addField(
            'reddit',
            'text',
            [
                'name'     => 'network[reddit]',
                'label'    => __('Reddit'),
                'title'    => __('Reddit')
            ]
        );

        $fieldset->addField(
            'skype',
            'text',
            [
                'name'     => 'network[skype]',
                'label'    => __('Skype'),
                'title'    => __('Skype')
            ]
        );

        $fieldset->addField(
            'tumblr',
            'text',
            [
                'name'     => 'network[tumblr]',
                'label'    => __('Tumblr'),
                'title'    => __('Tumblr')
            ]
        );

        $fieldset->addField(
            'vimeo',
            'text',
            [
                'name'     => 'network[vimeo]',
                'label'    => __('Vimeo'),
                'title'    => __('Vimeo')
            ]
        );

        $fieldset->addField(
            'youtube',
            'text',
            [
                'name'     => 'network[youtube]',
                'label'    => __('Youtube'),
                'title'    => __('Yimeo')
            ]
        );
        $data = $model->getData();

        if (!$model->getId()) {
            $data['user_name'] = $this->authSession->getUser()->getUsername();
        }

        if(isset($data['social_networks'])) {
            $socialNetworks = unserialize($data['social_networks']);
            if(!empty($socialNetworks)){
                $data = array_merge($data, $socialNetworks);
            }
        }
        $form->setValues($data);
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
        return __('Post Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Post Information');
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
