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
use Ves\Blog\Api\Data\AuthorInterface;
use Ves\Blog\Api\Data\AuthorInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class Author extends \Magento\Framework\Model\AbstractModel implements AuthorInterface
{

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $authorDataFactory;

    protected $dataObjectHelper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        AuthorInterfaceFactory $authorDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Ves\Blog\Model\ResourceModel\Author $resource = null,
        \Ves\Blog\Model\ResourceModel\Author\Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->authorDataFactory = $authorDataFactory;
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
        $this->_init(\Ves\Blog\Model\ResourceModel\Author::class);
    }

    /**
     * Retrieve author model with author data
     * @return AuthorInterface
     */
    public function getDataModel()
    {
        $authorData = $this->getData();

        $authorDataObject = $this->authorDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $authorDataObject,
            $authorData,
            AuthorInterface::class
        );

        return $authorDataObject;
    }

    public function loadByUserId($user_id = 0){
    	$this->getResource()->load($this, $user_id, 'user_id');
        return $this;
    }

    public function loadByUserName($user_name = ""){
    	$this->getResource()->load($this, $user_name, 'user_name');
        return $this;
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
    public function getAuthorId(){
        return $this->getData(self::AUTHOR_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorId($value){
        return $this->setData(self::AUTHOR_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(){
        return $this->getData(self::EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($value){
        return $this->setData(self::EMAIL, $value);
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
    public function getDescription(){
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($value){
        return $this->setData(self::DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAvatar(){
        return $this->getData(self::AVATAR);
    }

    /**
     * {@inheritdoc}
     */
    public function setAvatar($value){
        return $this->setData(self::AVATAR, $value);
    }


    /**
     * {@inheritdoc}
     */
    public function getNickName(){
        return $this->getData(self::NICK_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setNickName($value){
        return $this->setData(self::NICK_NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId(){
        return $this->getData(self::USER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($value){
        return $this->setData(self::USER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTitle(){
        return $this->getData(self::PAGE_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageTitle($value){
        return $this->setData(self::PAGE_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords(){
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaKeywords($value){
        return $this->setData(self::META_KEYWORDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription(){
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($value){
        return $this->setData(self::META_DESCRIPTION, $value);
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
    public function getUpdateTime(){
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdateTime($value){
        return $this->setData(self::UPDATE_TIME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSocialNetworks(){
        return $this->getData(self::SOCIAL_NETWORKS);
    }

    /**
     * {@inheritdoc}
     */
    public function setSocialNetworks($value){
        return $this->setData(self::SOCIAL_NETWORKS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsView(){
        return $this->getData(self::IS_VIEW);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsView($value){
        return $this->setData(self::IS_VIEW, $value);
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
        \Ves\Blog\Api\Data\AuthorExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
