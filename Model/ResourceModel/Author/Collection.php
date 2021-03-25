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
namespace Ves\Blog\Model\ResourceModel\Author;

use Ves\Blog\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'author_id';

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        return parent::_afterLoad();
    }

    protected function _construct()
    {
        $this->_init('Ves\Blog\Model\Author', 'Ves\Blog\Model\ResourceModel\Author');
    }

    /**
     * Returns pairs comment_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('author_id', 'user_name');
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
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        return;
    }

    public function addJoinPosts($store) {
        $store_id = "";
        if(is_numeric($store)) {
            $store_id = (int)$store;
        } elseif(is_array($store)) {
            $store_id = implode(",", $store);
        } else {
            $store_id = $store->getId();
        }
        $adapter = $this->getResource()->getConnection();
        $cols1 = array(
                'total_posts'  => new \Zend_Db_Expr($adapter->getIfNullSql('COUNT(author_posts.post_id)',0)),
                );

        $cols2 = array('user_id', 'post_id');
        $selectAuthorPosts = $adapter->select()->from(['main_table' => $this->getTable('ves_blog_post')], $cols2)
                                ->join(
                                    ['store_table' => $this->getTable('ves_blog_post_store')],
                                    'main_table.post_id = store_table.post_id',
                                    []
                                )->where('store_table.store_id IN ('.$store_id.',0)')
                                ->where('main_table.user_id IS NOT NULL')
                                ->group('main_table.post_id');


        $this->getSelect()
            ->joinLeft(array('author_posts' => $selectAuthorPosts), 'author_posts.user_id = main_table.author_id', $cols1)
            ->group('main_table.author_id');

        return $this;
    }

}
