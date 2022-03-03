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
 * @copyright  Copyright (c) 2020 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Blog\Cron;

use Ves\Blog\Helper\Data;
use Ves\Blog\Model\ResourceModel\Importer\CollectionFactory;
use Ves\Blog\Model\ImporterLogFactory;
/**
 * Class Sync
 *
 * @package Ves\Blog\Cron
 */
class RunImporter extends \Magento\Backend\App\Action
{
    protected $helper;
    protected $_importerLogFactory;
    protected $importerCollectionFactory;
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param Data $helper
     * @param CollectionFactory $importerCollectionFactory
     * @param ImporterLogFactory $_importerLogFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Data $helper,
        CollectionFactory $importerCollectionFactory,
        ImporterLogFactory $importerLogFactory
    ) {
        $this->helper = $helper;
        $this->importerCollectionFactory = $importerCollectionFactory;
        $this->_importerLogFactory = $importerLogFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $enabled_cron = $this->helper->getConfig("importer_settings/enable_cron");
        if($enabled_cron){
            $collection = $this->importerCollectionFactory->create();
            $collection->addFieldToFilter("importer_is_active", 1);
            if(0 < $collection->count()){
                foreach($collection as $_importer){
                    $modelLog = $this->_importerLogFactory->create();
                    $data = [];
                    $data['importer_id'] = (int)$_importer->getId();
                    $data['importer_title'] = $_importer->getTitle();
                    try{
                        $_importer->runImport();
                        $data['log_message'] = __('Success: You Run This Importer Sucessfully.');
                    }catch (\Exception $e) {
                        $data['log_message'] = __('Error: Something went wrong while run the importer.');
                        $data['log_message'] .= "\n".$e->getMessage();
                    }
                    //save import log data
                    try{
                        $modelLog->setData($data);
                        $modelLog->save();
                    }catch (\Exception $e) {

                    }
                }
            }
        }

    }
}
