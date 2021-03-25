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
namespace Ves\Blog\Model\ResourceModel\Post;

use Ves\Blog\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'post_id';

    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'post_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'post_collection';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('ves_blog_post_store', 'post_id');
        $this->getCategoriesAfterLoad();
        $this->getCommentsAfterLoad();
        $this->getTagsAfterLoad();
        $this->getProductsAfterLoad();
        $this->getRelatedPostsAfterLoad();
        return parent::_afterLoad();
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string $columnName
     * @return void
     */
    protected function getCategoriesAfterLoad()
    {
        $items = $this->getColumnValues("post_id");
        if (count($items)) {
            $connection = $this->getConnection();
            foreach ($this as $item) {
                $categories = [];
                $select = $connection->select()->from(['ves_blog_category' => $this->getTable('ves_blog_category')])
                        ->joinLeft([
                            'cat' => $this->getTable('ves_blog_post_category')], 'cat.category_id = ves_blog_category.category_id', [
                            'post_id' => 'post_id',
                            'category_id' => 'category_id'
                            ])
                        ->where('cat.post_id = ' . $item->getData("post_id"))
                        ->order('ves_blog_category.cat_position DESC');
                $categories = $connection->fetchAll($select);
                $item->setData('categories', $categories);
            }
        }
    }

    /**
     * Perform operations after collection load
     *
     * @return void
     */
    protected function getProductsAfterLoad()
    {
        $items = $this->getColumnValues("post_id");
        if (count($items)) {
            $connection = $this->getConnection();
            foreach ($this as $item) {
                $select = $connection->select()->from(['ves_blog_post_products_related' => $this->getTable('ves_blog_post_products_related')])
                    ->where('ves_blog_post_products_related.post_id = ' . $item->getData("post_id"))
                    ->order('ves_blog_post_products_related.position DESC')
                    ->reset(\Zend_Db_Select::COLUMNS)->columns(["entity_id"]);
                $products = $connection->fetchCol($select);
                $item->setData('products', $products);
                $item->setData('products_related', $products);
            }
        }
    }

    /**
     * Perform operations after collection load
     *
     * @return void
     */
    protected function getRelatedPostsAfterLoad()
    {
        $items = $this->getColumnValues("post_id");
        if (count($items)) {
            $connection = $this->getConnection();
            foreach ($this as $item) {
                $select = $connection->select()->from(['ves_blog_post_related' => $this->getTable('ves_blog_post_related')])
                    ->where('ves_blog_post_related.post_id = ' . $item->getData("post_id"))
                    ->order('ves_blog_post_related.position DESC')
                    ->reset(\Zend_Db_Select::COLUMNS)->columns(["post_related_id"]);
                $products = $connection->fetchCol($select);
                $item->setData('posts_related', $products);
            }
        }
    }

    protected function getCommentsAfterLoad()
    {
        $connection = $this->getConnection();
        foreach ($this as $item) {
            $select = $connection->select()->from(['ves_blog_comment' => $this->getTable('ves_blog_comment')])
                    ->where('ves_blog_comment.post_id = ' . $item->getData("post_id"))
                    ->where('ves_blog_comment.is_active = 1');
            $count = count($connection->fetchAll($select));
            $item->setData('comment_count', $count);
        }
    }

    protected function getTagsAfterLoad()
    {
        $connection = $this->getConnection();
        foreach ($this as $item) {
            $select = $connection->select()->from(['ves_blog_post_tag' => $this->getTable('ves_blog_post_tag')])
                    ->where('ves_blog_post_tag.post_id = ' . $item->getData("post_id"));
            $tags = $connection->fetchAll($select);
            $item->setData('tag', $tags);
        }
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ves\Blog\Model\Post', 'Ves\Blog\Model\ResourceModel\Post');
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['stores'] = 'store_table.store_id';
    }

    /**
     * Returns pairs post_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('post_id', 'title');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $store_id = "";
        if(is_numeric($store)) {
            $store_id = (int)$store;
        } elseif(is_array($store)) {
            $store_id = implode(",",$store);
        } else {
            $store_id = $store->getId();
        }
        $columnName = 'post_id';
        $this->getSelect()->join(
            ['store_table' => $this->getTable('ves_blog_post_store')],
            'main_table.post_id = store_table.post_id',
            []
        )->where('store_table.store_id in (' . $store_id . ', 0) ')
        ->group(
            'main_table.post_id'
        );
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('ves_blog_post_store', 'post_id');
    }

    /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
    public function addLinkAttributeToFilter($code, $condition)
    {
        if($code=='position'){
            $connection = $this->getConnection();
            $where = '';
            if(isset($condition['from'])){
                $where .= 'position >= ' . $condition['from'] . ' AND ';
            }
            if(isset($condition['to'])){
                $where .= ' position <= ' . $condition['to'] . ' AND ';
            }
            if($where!=''){
                $where .= ' post_id = ' . $condition['post_id'];
            }
            $select = 'SELECT post_related_id FROM ' . $this->getTable('ves_blog_post_related') . ' WHERE ' . $where;
            $postIds = $connection->fetchCol($select);
            $this->getSelect()->where('post_id IN (?)', $postIds);
        }
        return $this;
    }

    /**
     * Add link attribute to filter.
     *
     * @param string $code
     * @param array $condition
     * @return $this
     */
    public function addLinkCategoryToFilter($code, $condition)
    {
        if($code=='position'){
            $connection = $this->getConnection();
            $where = '';
            if(isset($condition['from'])){
                $where .= 'position >= ' . $condition['from'] . ' AND ';
            }
            if(isset($condition['to'])){
                $where .= ' position <= ' . $condition['to'] . ' AND ';
            }
            if($where!=''){
                $where .= ' category_id = ' . $condition['category_id'];
            }
            $select = 'SELECT post_id FROM ' . $this->getTable('ves_blog_post_category') . ' WHERE ' . $where;
            $postIds = $connection->fetchCol($select);
            $this->getSelect()->where('post_id IN (?)', $postIds);
        }
        return $this;
    }
    public function addAuthorFilter($author_id=0){
        return $this->addFieldToFilter("user_id", $author_id);
    }

    public function addArchiveFilter($year, $month = ""){
        $this->getSelect()
            ->where('YEAR(creation_time) = ?', (int)$year);
        if($month){
            $this->getSelect()->where('MONTH(creation_time) = ?', (int)$month);
        }
        return $this;
    }

    public function addCategoryFilter($category_id=0){
        if($category_id){
            $this->getSelect()
            ->joinLeft(
                [
                'cat' => $this->getTable('ves_blog_post_category')],
                'cat.post_id = main_table.post_id',
                [
                'post_id' => 'post_id',
                'position' => 'position'
                ]
                )
            ->where('cat.category_id = (?)', (int)$category_id);
        }
        return $this;
    }

    public function addTagFilter($tag = ""){
        if($tag){
            $tag = trim($tag);
            $this->getSelect()
            ->joinLeft(
                [
                'tag' => $this->getTable('ves_blog_post_tag')],
                'tag.post_id = main_table.post_id',
                [
                'post_id' => 'post_id'
                ]
                )
            ->where('tag.alias = ?', $tag);
        }
        return $this;
    }

    public function addActiveFilter(){
       return $this
            ->addFieldToFilter('is_active', 1);
            //->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);
    }
    public function addSearchFilter($searchKey = ""){
        if($searchKey){
            $searchKey = str_replace("+"," ",$searchKey);
            $searchKey = trim($searchKey);
            $this->addFieldToFilter(['title', 'identifier', 'short_content', 'tags','page_title','meta_keywords','meta_description','content'], [
                                        ['like'=>'%'.addslashes($searchKey).'%'],
                                        ['like'=>'%'.addslashes($searchKey).'%'],
                                        ['like'=>'%'.addslashes($searchKey).'%'],
                                        ['like'=>'%'.addslashes($searchKey).'%'],
                                        ['like'=>'%'.addslashes($searchKey).'%'],
                                        ['like'=>'%'.addslashes($searchKey).'%'],
                                        ['like'=>'%'.addslashes($searchKey).'%'],
                                        ['like'=>'%'.addslashes($searchKey).'%']
                                ]);
        }
        return $this;
    }
}
