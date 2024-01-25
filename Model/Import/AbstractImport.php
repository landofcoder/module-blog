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
namespace Ves\Blog\Model\Import;

/**
 * Abstract import model
 */
abstract class AbstractImport extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Connect to bd
     */
    protected $_connect = null;

    /**
     * @var \Ves\Blog\Model\PostFactory
     */
    protected $_postFactory;

    /**
     * @var \Ves\Blog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    protected $connectionFactory = null;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Ves\Blog\Model\PostFactory $postFactory,
     * @param \Ves\Blog\Model\CategoryFactory $categoryFactory,
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param \Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory
     * @param \Magento\Framework\Filesystem\Io\File $file
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Ves\Blog\Model\PostFactory $postFactory,
        \Ves\Blog\Model\CategoryFactory $categoryFactory,
        \Ves\Blog\Model\CommentFactory $commentFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Ves\Blog\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection\ConnectionFactory $connectionFactory,
        \Magento\Framework\Filesystem\Io\File $file,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_postFactory     = $postFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_commentFactory  = $commentFactory;
        $this->_storeManager    = $storeManager;
        $this->messageManager   = $messageManager;
        $this->_objectManager   = $objectManager;
        $this->authSession      = $authSession;
        $this->_helper          = $helper;
        $this->connectionFactory = $connectionFactory;
        $this->file = $file;
        $this->dir = $dir;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _connect()
    {
        $con = '';
        try{
            $dbname = $this->getData('dbname');
            $uname  = $this->getData('uname');
            $pwd    = $this->getData('pwd');
            $dbhost = $this->getData('dbhost');
            $charset = $this->getData('connect_charset');
            $charset = $charset?$charset:'utf8mb4';

            if($dbname=='' || $uname=='' || $pwd=='' || $dbhost==''){
                throw new \Exception(__("Some fields are required"));
            }
            $con = $this->connectionFactory->create(array(
                'host' => $dbhost,
                'dbname' => $dbname,
                'username' => $uname,
                'password' => $pwd,
                'active' => '1',
            ));
            $con->query("SET character_set_results = '".$charset."', character_set_client = '".$charset."', character_set_connection = '".$charset."', character_set_database = '".$charset."', character_set_server = '".$charset."'");
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->_connect = $con;
        return $con;
    }

    protected function getImageImportFolder($path)
    {
        $images = $this->dir->getPath('media')."/".$path;
        if ( ! file_exists($images)) {
            $this->file->mkdir($images);
        }
        return $images . '/';
    }

    public function getConnection()
    {
        if(!$this->_connect){
            $this->_connect();
        }
        return $this->_connect;
    }

    /**
     * Execute mysql query
     */
    protected function _mysqliQuery($sql)
    {
        $db = $this->getConnection();
        if ($results = $db->fetchAll($sql)) {
            return $results;
        }else {
            $this->messageManager->addError(__('Mysql error: %1.', $sql));
        }
    }
}
