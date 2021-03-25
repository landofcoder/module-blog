<?php


namespace Ves\Blog\Api\Data;

interface CommentInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const COMMENT_ID = 'comment_id';
    const POST_ID = 'post_id';
    const POSITION = 'position';
    const CONTENT = 'content';
    const USER_NAME = 'user_name';
    const USER_EMAIL = 'user_email';
    const CREATION_TIME = 'creation_time';
    const HAS_READ = 'has_read';
    const IS_ACTIVE = 'is_active';
    const PARENT_ID = 'parent_id';

    /**
     * Get value
     * @return int|null
     */
    public function getCommentId();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setCommentId($value);

    /**
     * Get value
     * @return int
     */
    public function getPostId();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setPostId($value);

    /**
     * Get value
     * @return int|null
     */
    public function getPosition();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setPosition($value);

    /**
     * Get value
     * @return string
     */
    public function getContent();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setContent($value);

    /**
     * Get value
     * @return string
     */
    public function getUserEmail();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setUserEmail($value);

    /**
     * Get value
     * @return string
     */
    public function getUserName();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setUserName($value);

    
    /**
     * Get value
     * @return string
     */
    public function getCreationTime();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setCreationTime($value);

    /**
     * Get value
     * @return int|null
     */
    public function getHasRead();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setHasRead($value);

    /**
     * Get value
     * @return int
     */
    public function getIsActive();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setIsActive($value);

    /**
     * Get value
     * @return int|null
     */
    public function getParentId();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\CommentInterface
     */
    public function setParentId($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Ves\Blog\Api\Data\CommentExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Ves\Blog\Api\Data\CommentExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ves\Blog\Api\Data\CommentExtensionInterface $extensionAttributes
    );
}