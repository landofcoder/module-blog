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
namespace Ves\Blog\Model;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Ves\Blog\Api\Data\CommentInterface;
use Ves\Blog\Api\Data\CommentInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
/**
 * Cms Page Model
 *
 * @method \Magento\Cms\Model\ResourceModel\Page _getResource()
 * @method \Magento\Cms\Model\ResourceModel\Page getResource()
 */
class Comment extends \Magento\Framework\Model\AbstractModel implements CommentInterface
{
    /**#@+
     * Comment's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $commentDataFactory;

    protected $dataObjectHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CommentInterfaceFactory $commentDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Ves\Blog\Model\ResourceModel\Comment $resource = null,
        \Ves\Blog\Model\ResourceModel\Comment\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->commentDataFactory = $commentDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Ves\Blog\Model\ResourceModel\Comment::class);
    }

    /**
     * Retrieve comment model with comment data
     * @return CommentInterface
     */
    public function getDataModel()
    {
        $commentData = $this->getData();

        $commentDataObject = $this->commentDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $commentDataObject,
            $commentData,
            CommentInterface::class
        );

        return $commentDataObject;
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * {@inheritdoc}
     */
    public function getCommentId(){
        return $this->getData(self::COMMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCommentId($value){
        return $this->setData(self::COMMENT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPostId(){
        return $this->getData(self::POST_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setPostId($value){
        return $this->setData(self::POST_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition(){
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($value){
        return $this->setData(self::POSITION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(){
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($value){
        return $this->setData(self::CONTENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserEmail(){
        return $this->getData(self::USER_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserEmail($value){
        return $this->setData(self::USER_EMAIL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserName(){
        return $this->getData(self::USER_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserName($value){
        return $this->setData(self::USER_NAME, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getCreationTime(){
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreationTime($value){
        return $this->setData(self::CREATION_TIME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getHasRead(){
        return $this->getData(self::HAS_READ);
    }

    /**
     * {@inheritdoc}
     */
    public function setHasRead($value){
        return $this->setData(self::HAS_READ, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsActive(){
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsActive($value){
        return $this->setData(self::IS_ACTIVE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getParentId(){
        return $this->getData(self::PARENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setParentId($value){
        return $this->setData(self::PARENT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->getDataExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Ves\Blog\Api\Data\CommentExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

}
