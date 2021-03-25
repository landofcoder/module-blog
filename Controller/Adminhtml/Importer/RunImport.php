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
namespace Ves\Blog\Controller\Adminhtml\Importer;

use Magento\Backend\App\Action;

class RunImport extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    protected $_importerFactory;

    protected $_importerLogFactory;
    /**
     * @param Action\Context
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Magento\Framework\Registry
     * @param \Ves\Blog\Model\ImporterFactory $importerFactory
     * @param \Ves\Blog\Model\ImporterLogFactory $importerLogFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Ves\Blog\Model\ImporterFactory $importerFactory,
        \Ves\Blog\Model\ImporterLogFactory $importerLogFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_importerFactory = $importerFactory;
        $this->_importerLogFactory = $importerLogFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ves_Blog::importer_run');
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ves_Blog::blog')
            ->addBreadcrumb(__('Blog'), __('Blog'))
            ->addBreadcrumb(__('Manage Importer'), __('Manage Importer'));
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('importer_id');
        $model = $this->_importerFactory->create();
        $modelLog = $this->_importerLogFactory->create();
        /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            $data = [];
            $data['importer_id'] = (int)$id;
            $data['importer_title'] = "";
            if (!$model->getId()) {
                $data['log_message'] = __('Error: This importer no longer exists.');
                $this->messageManager->addError(__('This importer no longer exists.'));
            } else{
                $data['importer_title'] = $model->getTitle();
                try{
                    $model->runImport();
                    $data['log_message'] = __('Success: You Run This Importer Sucessfully.');
                    $this->messageManager->addSuccess(__('You Run This Importer Sucessfully.'));
                }catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while run the importer.'));
                    $this->messageManager->addError($e->getMessage());
                    $data['log_message'] = __('Error: Something went wrong while run the importer.');
                    $data['log_message'] .= "\n".$e->getMessage();
                }
            }
            //save import log data
            try{
                $modelLog->setData($data);
                $modelLog->save();
            }catch (\Exception $e) {
                $this->messageManager->addException($e, __('Can not save import log data.'));
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
