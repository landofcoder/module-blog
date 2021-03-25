<?php


namespace Ves\Blog\Api\Data;

interface PostInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const POST_ID = 'post_id';
    const TITLE = 'title';
    const IDENTIFIER = 'identifier';
    const CONTENT = 'content';
    const SHORT_CONTENT = 'short_content';
    const USER_ID = 'user_id';
    const IMAGE = 'image';
    const IMAGE_TYPE = 'image_type';
    const IMAGE_VIDEO_TYPE = 'image_video_type';
    const IMAGE_VIDEO_ID = 'image_video_id';
    const THUMBNAIL = 'thumbnail';
    const THUMBNAIL_TYPE = 'thumbnail_type';
    const THUMBNAIL_VIDEO_TYPE = 'thumbnail_video_type';
    const THUMBNAIL_VIDEO_ID = 'thumbnail_video_id';
    const LAYOUT_TYPE = 'layout_type';
    const HITS = 'hits';
    const TAGS = 'tags';
    const ENABLE_COMMENT = 'enable_comment';
    const CANONICAL_URL = 'canonical_url';
    const IS_PRIVATE = 'is_private';
    const LIKE = 'like';
    const PAGE_LAYOUT = 'page_layout';
    const PAGE_TITLE = 'page_title';
    const DISKLIKE = 'disklike';
    const META_TITLE = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS = 'meta_keywords';
    const OG_METADATA = 'og_metadata';
    const OG_TITLE = 'og_title';
    const OG_DESCRIPTION = 'og_description';
    const OG_IMG = 'og_img';
    const OG_TYPE = 'og_type';
    const IS_ACTIVE = 'is_active';
    const REAL_POST_URL = 'real_post_url';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME = 'update_time';
    const REAL_IMAGE_URL = 'real_image_url';
    const REAL_THUMBNAIL_URL = 'real_thumbnail_url';
    const FILTERED_CONTENT = 'filtered_content';
    const STORES = 'stores';
    const CATEGORIES = 'categories';
    const CATEGORY_IDS = 'category_ids';
    const CATEGORY_NAMES = 'category_names';
    const CATEGORY_IDENTIFIERS = 'category_identifiers';
    const PRODUCTS_RELATED = 'products_related';
    const POSTS_RELATED = 'posts_related';
    const COMMENT_COUNT = 'comment_count';

    /**
     * Get value
     * @return int|null
     */
    public function getPostId();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setPostId($value);

    /**
     * Get value
     * @return string
     */
    public function getTitle();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setTitle($value);

    /**
     * Get value
     * @return string
     */
    public function getIdentifier();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setIdentifier($value);

    /**
     * Get value
     * @return string
     */
    public function getContent();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setContent($value);

    /**
     * Get value
     * @return string
     */
    public function getShortContent();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setShortContent($value);

    /**
     * Get value
     * @return int|null
     */
    public function getUserId();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setUserId($value);

    /**
     * Get value
     * @return string
     */
    public function getImage();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setImage($value);

    /**
     * Get value
     * @return string
     */
    public function getImageType();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setImageType($value);

    /**
     * Get value
     * @return string
     */
    public function getImageVideoType();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setImageVideoType($value);

    /**
     * Get value
     * @return string
     */
    public function getImageVideoId();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setImageVideoId($value);

    /**
     * Get value
     * @return string
     */
    public function getThumbnail();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setThumbnail($value);

    /**
     * Get value
     * @return string
     */
    public function getThumbnailType();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setThumbnailType($value);


    /**
     * Get value
     * @return string
     */
    public function getThumbnailVideoType();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setThumbnailVideoType($value);

    /**
     * Get value
     * @return string
     */
    public function getThumbnailVideoId();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setThumbnailVideoId($value);

    /**
     * Get value
     * @return string
     */
    public function getMetaDescription();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setMetaDescription($value);

    /**
     * Get value
     * @return string
     */
    public function getPageLayout();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
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
     * @return \Ves\Blog\Api\Data\PostInterface
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
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setMetaKeywords($value);


    /**
     * Get value
     * @return string
     */
    public function getCanonicalUrl();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setCanonicalUrl($value);

    /**
     * Get value
     * @return string|null
     */
    public function getTags();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setTags($value);

    /**
     * Get value
     * @return int|null
     */
    public function getHits();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setHits($value);

    /**
     * Get value
     * @return string
     */
    public function getCreationTime();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
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
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setUpdateTime($value);

    /**
     * Get value
     * @return int|null
     */
    public function getEnableComment();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setEnableComment($value);

    /**
     * Get value
     * @return int
     */
    public function getIsActive();

    /**
     * Set value
     * @param int $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setIsActive($value);

    /**
     * Get value
     * @return int|null
     */
    public function getIsPrivate();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setIsPrivate($value);

    /**
     * Get value
     * @return int|null
     */
    public function getLike();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setLike($value);

    /**
     * Get value
     * @return int|null
     */
    public function getDisklike();

    /**
     * Set value
     * @param int|null $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setDisklike($value);

    /**
     * Get value
     * @return string
     */
    public function getMetaTitle();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setMetaTitle($value);

    /**
     * Get value
     * @return string
     */
    public function getOgMetadata();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setOgMetadata($value);

    /**
     * Get value
     * @return string
     */
    public function getOgTitle();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setOgTitle($value);

    /**
     * Get value
     * @return string
     */
    public function getOgDescription();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setOgDescription($value);

    /**
     * Get value
     * @return string
     */
    public function getOgImg();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setOgImg($value);

    /**
     * Get value
     * @return string
     */
    public function getOgType();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setOgType($value);

    /**
     * Get value
     * @return string
     */
    public function getRealPostUrl();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setRealPostUrl($value);

    /**
     * Get value
     * @return string
     */
    public function getRealImageUrl();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setRealImageUrl($value);

    /**
     * Get value
     * @return string
     */
    public function getRealThumbnailUrl();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setRealThumbnailUrl($value);

    /**
     * Get value
     * @return string
     */
    public function getFilteredContent();

    /**
     * Set value
     * @param string $value
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setFilteredContent($value);

    /**
     * Get value
     * @return string[]|int[]|null
     */
    public function getStores();

    /**
     * Set value
     * @param string[]|int[]|null
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setStores($value);

    /**
     * Get value
     * @return string[]|int[]|null
     */
    public function getProductsRelated();

    /**
     * Set value
     * @param string[]|int[]|null
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setProductsRelated($value);

    /**
     * Get value
     * @return string[]|int[]|null
     */
    public function getPostsRelated();

    /**
     * Set value
     * @param string[]|int[]|null
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setPostsRelated($value);

    /**
     * Get value
     * @return string[]|int[]|null
     */
    public function getCategories();
    

    /**
     * Set value
     * @param string[]|int[]|\Ves\Blog\Api\Data\CategoryInterface[]|null
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setCategories($value);

    /**
     * Get value
     * @return string[]|int[]|null
     */
    public function getCategoryIds();
    

    /**
     * Set value
     * @param string[]|int[]|null
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setCategoryIds($value);

    /**
     * Get value
     * @return string[]|null
     */
    public function getCategoryNames();
    

    /**
     * Set value
     * @param string[]|null
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setCategoryNames($value);


    /**
     * Get value
     * @return string[]|null
     */
    public function getCategoryIdentifiers();
    

    /**
     * Set value
     * @param string[]|null
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setCategoryIdentifiers($value);

    /**
     * Get value
     * @return int|null
     */
    public function getCommentCount();
    

    /**
     * Set value
     * @param int|null
     * @return \Ves\Blog\Api\Data\PostInterface
     */
    public function setCommentCount($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Ves\Blog\Api\Data\PostExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Ves\Blog\Api\Data\PostExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Ves\Blog\Api\Data\PostExtensionInterface $extensionAttributes
    );
}