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
namespace Ves\Blog\Controller\Adminhtml\Post;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $_logger;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

    protected $_fileSystem;

    /**
     * @param Action\Context                $context
     * @param \Psr\Log\LoggerInterface      $logger
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Helper\Js    $jsHelper
     */
    public function __construct(
        Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Helper\Js $jsHelper
    ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper    = $jsHelper;
        $this->_logger     = $logger;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_Blog::post_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $links = $this->getRequest()->getPost('links');
        $links = is_array($links) ? $links : [];
        if(!empty($links) && isset($links['posts'])){
            $postsRelated = $this->jsHelper->decodeGridSerializedInput($links['posts']);
            $data['posts_related'] = $postsRelated;
        }

        if(!empty($links) && isset($links['related'])){
            $productsRelated = $this->jsHelper->decodeGridSerializedInput($links['related']);
            $data['products_related'] = $productsRelated;
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Ves\Blog\Model\Post');

            $id = $this->getRequest()->getParam('post_id');
            if ($id) {
                $model->load($id);
            }

            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'ves/blog/';
            $path = $mediaDirectory->getAbsolutePath($mediaFolder);

            // Delete, Upload Image
            $imagePath = $mediaDirectory->getAbsolutePath($model->getImage());
            if(isset($data['image']['delete'])){
                if(file_exists($imagePath)){
                    unlink($imagePath);
                }
                $data['image'] = '';
            }
            if(isset($data['image']) && is_array($data['image'])){
                unset($data['image']);
            }
            if($image = $this->uploadImage('image')){
                $data['image'] = $image;
            }

            // Delete, Upload Thumbnail
            $thumbnailPath = $mediaDirectory->getAbsolutePath($model->getThumbnail());
            if(isset($data['thumbnail']['delete'])){
                if(file_exists($thumbnailPath)){
                    unlink($thumbnailPath);
                }
                $data['thumbnail'] = '';
            }

            if(isset($data['thumbnail']) && is_array($data['thumbnail'])){
                unset($data['thumbnail']);
            }
            if($thumbnail = $this->uploadImage('thumbnail')){
                $data['thumbnail'] = $thumbnail;
            }

            // Delete, Upload Og Image
            $ogImagePath = $mediaDirectory->getAbsolutePath($model->getOgImg());
            if(isset($data['og_img']['delete'])){
                if(file_exists($ogImagePath)){
                    unlink($ogImagePath);
                }
                $data['og_img'] = '';
            }

            if(isset($data['og_img']) && is_array($data['og_img'])){
                unset($data['og_img']);
            }
            if($og_image = $this->uploadImage('og_img')){
                $data['og_img'] = $og_image;
            }
            $identifier = isset($data['identifier'])?$data['identifier']:$data['name'];
            $identifier = strip_tags($identifier);
            $identifier = trim($identifier);
            $identifier = strtolower($identifier);
            $identifier = str_replace(" ","-",$identifier);
            $data['identifier'] = $identifier;

            $model->setData($data);
            $this->_eventManager->dispatch(
                'blog_post_prepare_save',
                ['post' => $model, 'request' => $this->getRequest()]
                );

            try {
                $model->save();

                $this->messageManager->addSuccess(__('You saved this post.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId(), '_current' => true]);
                }
                if(!$this->getRequest()->getParam("duplicate")){
                    return $resultRedirect->setPath('*/*/');
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the post.'));
                $this->messageManager->addError($e->getMessage());
            }

            if($this->getRequest()->getParam("duplicate")){
                unset($data['post_id']);
                $data['identifier'] = $data['identifier'] . time() . uniqid();
                $post = $this->_objectManager->create('Ves\Blog\Model\Post');
                $post->setData($data);
                try{
                    $post->save();
                    $this->messageManager->addSuccess(__('You duplicated this post'));
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while duplicating the post.'));
                }
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $this->getRequest()->getParam('post_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function uploadImage($fieldId = 'image')
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='')
        {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
                );

            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
            $mediaFolder = 'ves/blog/';
            try {
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $file_name = $uploader->getUploadedFileName();
                //$file_name = str_replace(" ","_", $file_name);
                $result = $uploader->save($mediaDirectory->getAbsolutePath($mediaFolder)
                    ,$file_name);
                return $mediaFolder.$result['name'];
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['category_id' => $this->getRequest()->getParam('category_id')]);
            }
        }
        return;
    }
}
