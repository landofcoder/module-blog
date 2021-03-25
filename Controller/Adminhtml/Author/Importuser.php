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
namespace Ves\Blog\Controller\Adminhtml\Author;

use Magento\Framework\App\Filesystem\DirectoryList;

class Importuser extends \Magento\Backend\App\Action
{
     /**
     * @var \Psr\Log\LoggerInterface
     */
    private $_logger;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $jsHelper;

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
        \Psr\Log\LoggerInterface $logger
        ) {
        $this->_fileSystem = $filesystem;
        $this->jsHelper = $jsHelper;
        $this->_logger     = $logger;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_Blog::author_import_user');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        //$model = $this->_objectManager->create('Ves\Blog\Model\Author');
        $model = $this->_objectManager->create('Magento\User\Model\User');
        
        $collection = $model->getCollection();
        try {
            if($collection->getSize()) {
                $count = 0;
                foreach($collection as $user_item) {
                    $user_id = $user_item->getId();
                    $model = $this->_objectManager->create('Ves\Blog\Model\Author');
                    $model->load($user_id);
                    if(!$model->getAuthorId()) {
                        $model->loadByUserName($user_item->getUsername());
                    }
                    if(!$model->getAuthorId()) {
                        $data = [];
                        $data['email'] = $user_item->getEmail();
                        $data['user_name'] = $user_item->getUsername();
                        $data['avatar'] = '';
                        $data['user_id'] = $user_item->getId();
                        $model->setData($data);
                        $model->save();
                        $count++;
                    }
                }
                $this->messageManager->addSuccess(__('We imported %1 users to blog authors sucessfully.', $count));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while import users to blog authors.'));
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }

}