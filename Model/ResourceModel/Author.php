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
namespace Ves\Blog\Model\ResourceModel;

use \Magento\Framework\Model\AbstractModel;

class Author extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_date;

   /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->_date = $date;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ves_blog_post_author', 'author_id');
    }

    public function getIsUniqueIdentifier(AbstractModel $object)
    {
        $select = $this->getConnection()->select()->from(
            ['lga' => $this->getMainTable()]
            )->where(
            'lga.user_name = ?',
            $object->getData('user_name')
            );
            if ($object->getId()) {
                $select->where('lga.author_id <> ?', $object->getId());
            }
            if ($this->getConnection()->fetchRow($select)) {
                return false;
            }

            return true;
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (!$this->getIsUniqueIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('A author identifier with the same properties already exists.')
                );
        }
        return $this;
    }

    public function getDate()
    {
        return $this->_date;
    }
}
