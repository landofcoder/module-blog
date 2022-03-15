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
namespace Ves\Blog\Block\Adminhtml\Comment\Edit\Tab;

class Reply extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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

    protected $_commentFactory;

    protected $authSession;

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
        \Ves\Blog\Model\Comment $commentFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_dateTime = $dateTime;
        $this->_category = $category;
        $this->_thumbnailType = $thumbnailType;
        $this->_videoType = $videoType;
        $this->_user = $user;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_postFactory = $postFactory;
        $this->_commentFactory = $commentFactory;
        $this->authSession = $authSession;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
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
        $model = $this->_coreRegistry->registry('current_comment');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Ves_Blog::comment_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('comment_');


        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Reply Comment')]);

        $fieldset->addField(
            'reply_user_name',
            'text',
            [
                'name' => 'reply_user_name',
                'label' => __('User Name'),
                'title' => __('User Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'reply_user_email',
            'text',
            [
                'name' => 'reply_user_email',
                'label' => __('User Email'),
                'title' => __('User Email'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
                'reply_content',
                'editor',
                [
                    'label' => __('Reply'),
                    'title' => __('Reply'),
                    'name' => 'reply_content',
                    'style' => 'height:20em;',
                    'required' => true,
                    'disabled' => $isElementDisabled,
                    'config' => $wysiwygConfig
                ]
            );

        $comment_id = $model->getId();
        $childCommentCollection = $this->_commentFactory->getCollection();
        $childCommentCollection  ->addFieldToFilter('parent_Id', $comment_id)
                                ->setOrder('creation_time','DESC')
                                ->setPageSize('100')
                                ->setCurPage('1');


        if (count($childCommentCollection) >= 1) {

            //load all child reply comments
            $child_fieldset = $form->addFieldset('reply_fieldset', ['legend' => __('All Replied Of This Comment')]);

            $replies_html = '<ul class="list list-comments">';
            $count = 1;
            foreach($childCommentCollection as $child) {
                $obb = (0==($count%2))?'obb':'';
                if(1==$child->getIsActive()){
                    $active = __("Enabled");
                } else {
                    $active = __("Disabled");
                }
                $edit_reply_url = $this->getUrl("*/*/edit/", array("comment_id"=>$child->getId()));
                $replies_html .='<li class="reply-comment-item '.$obb.'">';
                $replies_html .= '<div class="comment-content">'.strip_tags($child->getContent()).'</div>';
                $replies_html .= '<br/>';
                $replies_html .= '<div class="comment-meta-info">';
                $replies_html .= '<div><strong>'.__('Replied by: ').'</strong>'.$child->getUserName().' < '.$child->getUserEmail().' > </div>';
                $replies_html .= '<div><strong>'.__('Replied on: ').'</strong>'.$child->getCreationTime().'</div>';
                $replies_html .= '<div><strong>'.__('Status: ').'</strong>'.$active.'</div>';
                $replies_html .= '<div><a href="'.$edit_reply_url.'" target="_BLANK">'.__('Edit The Reply').'</a></div>';
                $replies_html .= '</div>';
                $replies_html .= '<hr/>';
                $replies_html .='</li>';
                $count++;
            }
            $replies_html .= '</ul>';
            $child_fieldset->addField(
                'child_comments',
                'note',
                [
                    'name' => 'child_comments',
                    'label' => __('Replies'),
                    'title' => __('Replies'),
                    'text' => $replies_html
                ]
            );
        }



        $this->_eventManager->dispatch('adminhtml_blog_comment_edit_tab_reply_prepare_form', ['form' => $form]);


        $adminUser = $this->getCurrentUser();
        $user_name = $adminUser->getUsername();
        $user_email = $adminUser->getEmail();

        $model->setData('reply_user_name', $user_name);
        $model->setData('reply_user_email', $user_email);

        $data = $model->getData();
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
        return __('Reply This Comment');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Reply This Comment');
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
