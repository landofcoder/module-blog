<?php


namespace Ves\Blog\Api\Data;

interface TagInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const TAG_ID = 'tag_id';
    const POST_ID = 'post_id';
    const NAME = 'name';
    const ALIAS = 'alias';
    const META_ROBOTS = 'meta_robots';

    /**
     * Get value
     * @return int|null
     */
    public function getTagId();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\TagInterface
     */
    public function setTagId($value);

    /**
     * Get value
     * @return string
     */
    public function getPostId();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\TagInterface
     */
    public function setPostId($value);

    /**
     * Get value
     * @return string
     */
    public function getName();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\TagInterface
     */
    public function setName($value);

    /**
     * Get value
     * @return string
     */
    public function getAlias();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\TagInterface
     */
    public function setAlias($value);

    /**
     * Get value
     * @return string
     */
    public function getMetaRobots();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\TagInterface
     */
    public function setMetaRobots($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Ves\Blog\Api\Data\TagExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Ves\Blog\Api\Data\TagExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ves\Blog\Api\Data\TagExtensionInterface $extensionAttributes
    );
}