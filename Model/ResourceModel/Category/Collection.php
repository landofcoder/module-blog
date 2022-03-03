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
namespace Ves\Blog\Model\ResourceModel\Category;

use Ves\Blog\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'category_id';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('ves_blog_category_store', 'category_id');

        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ves\Blog\Model\Category', 'Ves\Blog\Model\ResourceModel\Category');
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Returns pairs category_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('category_id', 'name');
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
        $this->performAddStoreFilter($store, $withAdmin);

        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('ves_blog_category_store', 'category_id');
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
            $this->addFieldToFilter(['identifier', 'name','page_title','meta_keywords','meta_description','description'], [
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

    public function addPostToFilter($post_id = 0){
        if($post_id) {
            $this->getSelect()
            ->joinLeft(
                [
                'cat' => $this->getTable('ves_blog_post_category')],
                'cat.category_id = main_table.category_id',
                [
                'category_id' => 'category_id'
                ]
                )
            ->where('cat.post_id = (?)', (int)$post_id);
        }
        return $this;
    }
}
