<?php


namespace Ves\Blog\Api\Data;

interface CategoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const NAME = 'name';
    const CATEGORY_ID = 'category_id';
    const IDENTIFIER = 'identifier';
    const DESCRIPTION = 'description';
    const IMAGE = 'image';
    const LAYOUT_TYPE = 'layout_type';
    const ORDERBY = 'orderby';
    const COMMENTS = 'comments';
    const ITEM_PER_PAGE = 'item_per_page';
    const LG_COLUMN_ITEM = 'lg_column_item';
    const MD_COLUMN_ITEM = 'md_column_item';
    const SM_COLUMN_ITEM = 'sm_column_item';
    const XS_COLUMN_ITEM = 'xs_column_item';
    const PAGE_LAYOUT = 'page_layout';
    const PAGE_TITLE = 'page_title';
    const CANONICAL_URL = 'canonical_url';
    const LAYOUT_UPDATE_XML = 'layout_update_xml';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const CAT_POSITION = 'cat_position';
    const IS_ACTIVE = 'is_active';
    const PARENT_ID = 'parent_id';
    const POSTS_STYLE = 'posts_style';
    const POSTS_TEMPLATE = 'posts_template';
    const POST_TEMPLATE = 'post_template';
    const STORES = 'stores';

    /**
     * Get value
     * @return int|null
     */
    public function getCategoryId();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setCategoryId($value);

    /**
     * Get value
     * @return string
     */
    public function getName();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setName($value);

    /**
     * Get value
     * @return string
     */
    public function getIdentifier();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setIdentifier($value);

    /**
     * Get value
     * @return string
     */
    public function getDescription();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setDescription($value);

    /**
     * Get value
     * @return string
     */
    public function getImage();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setImage($value);

    /**
     * Get value
     * @return string
     */
    public function getLayoutType();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setLayoutType($value);

    /**
     * Get value
     * @return string
     */
    public function getOrderby();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setOrderby($value);

    /**
     * Get value
     * @return string
     */
    public function getComments();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setComments($value);

    /**
     * Get value
     * @return int|null
     */
    public function getItemPerPage();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setItemPerPage($value);

    /**
     * Get value
     * @return int
     */
    public function getLgColumnItem();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setLgColumnItem($value);

    /**
     * Get value
     * @return int
     */
    public function getMdColumnItem();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setMdColumnItem($value);

    /**
     * Get value
     * @return int
     */
    public function getSmColumnItem();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setSmColumnItem($value);

    /**
     * Get value
     * @return int
     */
    public function getXsColumnItem();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setXsColumnItem($value);

    /**
     * Get value
     * @return string
     */
    public function getPageLayout();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setPageLayout($value);

    /**
     * Get value
     * @return string
     */
    public function getPageTitle();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setPageTitle($value);

    /**
     * Get value
     * @return string
     */
    public function getCanonicalUrl();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setCanonicalUrl($value);

    /**
     * Get value
     * @return string
     */
    public function getLayoutUpdateXml();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setLayoutUpdateXml($value);

    /**
     * Get value
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
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
     * @return \Ves\Blog\Api\Data\CategoryInterface
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
     * @return \Ves\Blog\Api\Data\CategoryInterface
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
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setUpdateTime($value);

    /**
     * Get value
     * @return int
     */
    public function getCatPosition();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setCatPosition($value);

    /**
     * Get value
     * @return int
     */
    public function getIsActive();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
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
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setParentId($value);

    /**
     * Get value
     * @return string
     */
    public function getPostsStyle();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setPostsStyle($value);

    /**
     * Get value
     * @return string
     */
    public function getPostsTemplate();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setPostsTemplate($value);

    /**
     * Get value
     * @return string
     */
    public function getPostTemplate();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setPostTemplate($value);

    /**
     * Get value
     * @return int[]
     */
    public function getStores();

    /**
     * Set value
     * @param int[] $value
     * @return \Ves\Blog\Api\Data\CategoryInterface
     */
    public function setStores($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Ves\Blog\Api\Data\CategoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Ves\Blog\Api\Data\CategoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ves\Blog\Api\Data\CategoryExtensionInterface $extensionAttributes
    );
}