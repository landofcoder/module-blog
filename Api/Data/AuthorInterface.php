<?php


namespace Ves\Blog\Api\Data;

interface AuthorInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const AUTHOR_ID = 'author_id';
    const EMAIL = 'email';
    const USER_NAME = 'user_name';
    const NICK_NAME = 'nick_name';
    const DESCRIPTION = 'description';
    const AVATAR = 'avatar';
    const USER_ID = 'user_id';
    const PAGE_TITLE = 'page_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const SOCIAL_NETWORKS = 'social_networks';
    const IS_VIEW = 'is_view';

    /**
     * Get value
     * @return int|null
     */
    public function getAuthorId();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setAuthorId($value);

    /**
     * Get value
     * @return string
     */
    public function getEmail();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setEmail($value);

    /**
     * Get value
     * @return string
     */
    public function getUserName();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setUserName($value);

    /**
     * Get value
     * @return string
     */
    public function getDescription();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setDescription($value);

    /**
     * Get value
     * @return string
     */
    public function getAvatar();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setAvatar($value);


    /**
     * Get value
     * @return string
     */
    public function getNickName();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setNickName($value);

    /**
     * Get value
     * @return int
     */
    public function getUserId();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setUserId($value);

    /**
     * Get value
     * @return string
     */
    public function getPageTitle();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setPageTitle($value);

    /**
     * Get value
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setMetaKeywords($value);

    /**
     * Get value
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setMetaDescription($value);

    /**
     * Get value
     * @return string
     */
    public function getCreationTime();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setCreationTime($value);

    /**
     * Get value
     * @return string
     */
    public function getUpdateTime();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setUpdateTime($value);

    /**
     * Get value
     * @return string
     */
    public function getSocialNetworks();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setSocialNetworks($value);

    /**
     * Get value
     * @return int
     */
    public function getIsView();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\AuthorInterface
     */
    public function setIsView($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Ves\Blog\Api\Data\AuthorExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Ves\Blog\Api\Data\AuthorExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ves\Blog\Api\Data\AuthorExtensionInterface $extensionAttributes
    );
}