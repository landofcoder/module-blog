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

class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('ves_blog_post', 'post_id');
    }

    /**
     * Process block data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Cms\Model\ResourceModel\Page
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['post_id = ?' => (int)$object->getId()];

        $this->getConnection()->delete($this->getTable('ves_blog_post_store'), $condition);

        $this->getConnection()->delete($this->getTable('ves_blog_post_tag'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * Perform operations before object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->getIsUniqueBlockToStores($object) && !$object->getData('importing')) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('A post identifier with the same properties already exists in the selected store.')
                );
        }
        if($object->getData('importing') && !$this->getIsUniqueBlockToStores($object) && !$object->getData('overwrite')){
            $data = $object->getData();
            $data['identifier'] = $data['identifier'] . time();
            $object->setData($data);
        }
        return $this;
    }

    /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if(!$object->getData("isfrontend")){
            $postData = $object->getData();

            // STORE
            if(isset($postData['stores']) || isset($postData['store_ids'])){
                if(isset($postData['stores'])){
                    $stores = $object->getStores();
                }elseif(isset($postData['store_ids'])){
                    $stores = $object->getStoreIds();
                }
                $table = $this->getTable('ves_blog_post_store');
                $where = ['post_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                if (!empty($stores)) {
                    $data = [];
                    foreach ($stores as $storeId) {
                        $data[] = [
                            'post_id'  => (int)$object->getId(),
                            'store_id' => (int)$storeId
                        ];
                    }
                    try{
                        $this->getConnection()->insertMultiple($table, $data);
                    }catch(\Exception $e){
                        die($e->getMessage());
                    }
                }
            }

            // Categories
            if(isset($postData['categories'])){
                $newCategories = (array)$postData['categories'];
                $table = $this->getTable('ves_blog_post_category');
                $where = ['post_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];

                if (!empty($newCategories)) {
                    foreach ($newCategories as $cat) {
                        $catId = (is_array($cat) && isset($cat['category_id']))?$cat['category_id']:$cat;
                        $data[] = [
                            'post_id'     => (int)$object->getId(),
                            'category_id' => (int)$catId,
                            'position'    => (int)$object->getPosition()
                        ];
                    }
                    $this->getConnection()->insertMultiple($table, $data);
                }
            }

            // Posts Related
            if(isset($postData['posts_related'])){
                $postsRelated = $postData['posts_related'];
                $table = $this->getTable('ves_blog_post_related');
                $where = ['post_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];
                 if(!empty($postsRelated)){
                    foreach ($postsRelated as $k => $_post) {
                        $_position = 0;
                        if(is_array($_post)){
                            $_position = $_post['position'];
                            $_post_id = $k;
                        }else {
                            $_post_id = (int)$_post;
                        }
                        $data[] = [
                            'post_id'         => (int)$object->getId(),
                            'post_related_id' => $_post_id,
                            'position'        => $_position
                        ];
                    }
                    $this->getConnection()->insertMultiple($table, $data);
                }
            }

            // Products Related
            if(isset($postData['products_related'])){
                $productsRelated = $postData['products_related'];
                $table = $this->getTable('ves_blog_post_products_related');
                $where = ['post_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                $data = [];
                if(!empty($productsRelated)){
                    foreach ($productsRelated as $k => $_post) {
                        $_position = 0;
                        if(is_array($_post)){
                            $_position = $_post['position'];
                            $_product_id = $k;
                        }else {
                            $_product_id = (int)$_post;
                        }
                        $data[] = [
                            'post_id'   => (int)$object->getId(),
                            'entity_id' => $_product_id,
                            'position'  => $_position
                        ];
                    }
                    $this->getConnection()->insertMultiple($table, $data);
                }
            }


            // Posts Tag
            if(isset($postData['tags'])){
                $tags = $postData['tags'];
                if(!is_array($tags)) {
                    $tags = explode(",", $tags);
                }
                $table = $this->getTable('ves_blog_post_tag');
                $where = ['post_id = ?' => (int)$object->getId()];
                $this->getConnection()->delete($table, $where);
                if(!empty($tags)){
                    $data = [];
                    foreach ($tags as $k => $_tag) {
                        $name =  strtolower(str_replace(["_", " "], "-", trim($_tag) ) );
                        $data[] = [
                        'post_id' => (int)$object->getId(),
                        'alias' => $name,
                        'name' => $_tag
                        ];
                    }
                    $this->getConnection()->insertMultiple($table, $data);
                }
            }
        }
        return parent::_afterSave($object);
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && $field === null) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($id = $object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
            $object->setData('stores', $stores);
            $object->setData('store_ids', $stores);
            $categories = $this->lookupCategoryIds($object->getId());
            $object->setData('category_id', $categories);
            $object->setData('categories', $categories);

            $connection = $this->getConnection();
            $select = $connection->select()->from($this->getTable('ves_blog_comment'))
            ->where('post_id = ' . $object->getId())
            ->where('is_active = 1');
            $comments = $connection->fetchAll($select);
            $object->setData("comments", $comments);
            $object->setData("comment_count", count($comments));

            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('ves_blog_post_related'))
            ->where(
                'post_id = '.(int)$id
                );
            $postsRelated = $connection->fetchAll($select);
            $object->setData('related_posts', $postsRelated);

            if($postsRelated){
                $_new_postsRelated = [];
                foreach($postsRelated as $_post){
                    $_new_postsRelated[] = (int)$_post['post_related_id'];
                }
                $object->setData('posts_related', $_new_postsRelated);
            }

            $connection = $this->getConnection();
            $select = $connection->select()
            ->from($this->getTable('ves_blog_post_products_related'))
            ->where(
                'post_id = '.(int)$id
                );
            $products = $connection->fetchAll($select);
            $object->setData('related_products', $products);

            if($products){
                $_new_products = [];
                foreach($products as $_product){
                    $_new_products[] = (int)$_product['entity_id'];
                }
                $object->setData('products_related', $_new_products);
            }

        }
        return parent::_afterLoad($object);
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Cms\Model\Block $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $stores = [(int)$object->getStoreId(), \Magento\Store\Model\Store::DEFAULT_STORE_ID];

            $select->join(
                ['cbs' => $this->getTable('ves_blog_post_store')],
                $this->getMainTable() . '.post_id = cbs.post_id',
                ['store_id']
                )->where(
                'is_active = ?',
                1
                )->where(
                'cbs.store_id in (?)',
                $stores
                )->order(
                'store_id DESC'
                )->limit(
                1
                );
            }

            return $select;
        }

    /**
     * Check for unique of identifier of block to selected store(s).
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUniqueBlockToStores(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($this->_storeManager->hasSingleStore()) {
            $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        } else {
            $stores = (array)$object->getData('stores');
        }

        $select = $this->getConnection()->select()->from(
            ['cb' => $this->getMainTable()]
            )->join(
            ['cbs' => $this->getTable('ves_blog_post_store')],
            'cb.post_id = cbs.post_id',
            []
            )->where(
            'cb.identifier = ?',
            $object->getData('identifier')
            )->where(
            'cbs.store_id IN (?)',
            $stores
            );

            if ($object->getId()) {
                $select->where('cb.post_id <> ?', $object->getId());
            }

            if ($this->getConnection()->fetchRow($select)) {
                return false;
            }

            return true;
        }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('ves_blog_post_store'),
            'store_id'
            )->where(
            'post_id = :post_id'
            );

            $binds = [':post_id' => (int)$id];

            return $connection->fetchCol($select, $binds);
        }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return mixed
     */
    public function lookupPostsRelatedIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('ves_blog_post_related'),
            'post_related_id'
            )->distinct(true)
            ->where(
                'post_id = :post_id'
                );
        $binds = [':post_id' => (int)$id];

        return $connection->fetchCol($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return mixed
     */
    public function lookupProductsRelatedIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('ves_blog_post_products_related'),
            'entity_id'
            )->distinct(true)
            ->where(
                'post_id = :post_id'
            );

        $binds = [':post_id' => (int)$id];


        return $connection->fetchCol($select, $binds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupCategoryIds($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('ves_blog_post_category'),
            'category_id'
            )->where(
            'post_id = :post_id'
            );

            $binds = [':post_id' => (int)$id];

            return $connection->fetchCol($select, $binds);
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function getPostTags($post_id)
    {
        $select = $this->getConnection()->select()->from(
            ['tag' => $this->getTable('ves_blog_post_tag')]
            )->where(
            'tag.post_id = ?',
            (int)$post_id
            );
        return $this->getConnection()->fetchAll($select);
    }

    public function getPostCategories($post_id)
    {
        $select = $this->getConnection()->select()->from(
            ['cat' => $this->getTable('ves_blog_category')]
            )->join(
            ['cpc' => $this->getTable('ves_blog_post_category')],
            'cat.category_id = cpc.category_id',
            []
            )->where(
            'cpc.post_id = ?',
            (int)$post_id
            )->order('cat.cat_position DESC');
        return $this->getConnection()->fetchAll($select);
    }
}
