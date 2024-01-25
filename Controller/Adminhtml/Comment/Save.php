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
namespace Ves\Blog\Controller\Adminhtml\Comment;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    protected $authSession;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\ObjectManagerInterface
     * @param \Magento\Framework\Filesystem
     * @param \Magento\Backend\Helper\Js
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        $this->authSession = $authSession;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_Blog::comment_save');
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $adminUser = $this->getCurrentUser();
        $data = $this->getRequest()->getPostValue();

        $links = $this->getRequest()->getPost('links');
        $links = is_array($links) ? $links : [];

        if(!empty($links)){
            $posts = $this->jsHelper->decodeGridSerializedInput($links['posts']);
            $data['posts'] = $posts;
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Ves\Blog\Model\Comment');
            // $replyModel = $this->_objectManager->create('Ves\Blog\Model\Comment');

            $id = $this->getRequest()->getParam('comment_id');

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'ves/blog/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            $reply_data['post_id']      =   $data['post_id'];
            $reply_data['user_name']    =   (isset($data['reply_user_name']) && $data['reply_user_name'])?$data['reply_user_name']:$adminUser->getUsername();
            $reply_data['user_email']   =   (isset($data['reply_user_email']) && $data['reply_user_email'])?$data['reply_user_email']:$adminUser->getEmail();
            $reply_data['content']      =   isset($data['reply_content'])?$data['reply_content']:'';
            $reply_data['has_read']     =   1;
            $reply_data['is_active']    =   1;
            $reply_data['parent_id']    =   $id;
            $reply_data['stores']       =   $data['stores'];

            try {
                if ($reply_data['content']) {
                    $model->setData($reply_data);
                    $model->save();
                    $model->unsetData();
                }

                if ($id) {
                    $model->load($id);
                }
                $model->setData($data);
                $model->save();


                $this->messageManager->addSuccess(__('You saved this comment.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['comment_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the comment.'));
                $this->messageManager->addError($e->getMessage());
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['comment_id' => $this->getRequest()->getParam('comment_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }


}
