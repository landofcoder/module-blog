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
use Ves\Blog\Api\CategoryManagementInterface;
use Ves\Blog\Api\Data;
/**
 * Category management model
 */
class CategoryManagement extends AbstractManagement implements CategoryManagementInterface
{
    /**
     * @var \Ves\Blog\Model\CategoryFactory
     */
    protected $_itemFactory;

    protected $searchResultsFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Ves\Blog\Model\CategoryFactory $categoryFactory
     * @param Data\CategorySearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \Ves\Blog\Model\CategoryFactory $categoryFactory,
        Data\CategorySearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->_itemFactory = $categoryFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }
    
     /**
      * Retrieve list of category by page type, term, store, etc
      *
      * @param  string $type
      * @param  string $term
      * @param  int $storeId
      * @param  int $page
      * @param  int $limit
      * @return Data\CategorySearchResultsInterface
      */
    public function getList($type, $term, $storeId, $page, $limit)
    {
        try {
            $collection = $this->_itemFactory->create()->getCollection();
            $collection
                ->addActiveFilter()
                ->addStoreFilter($storeId)
                ->setCurPage($page)
                ->setPageSize($limit);

            $type = strtolower($type);

            switch ($type) {
                case 'search':
                    $collection->addSearchFilter($term);
                    break;
            }

            /** @var Data\CategorySearchResultsInterface $searchResults */
            $searchResults = $this->searchResultsFactory->create();
            $searchResults->setItems($collection->getItems());
            $searchResults->setTotalCount($collection->getSize());
            return $searchResults;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Create new item using data
     *
     * @param string $data
     * @return \Ves\Blog\Api\Data\CategoryInterface|bool
     */
    public function create($data)
    {
        try {
            $data = json_decode($data, true);
            $item = $this->_itemFactory->create();
            $item->setData($data)->save();
            return $item;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update item using data
     *
     * @param int $id
     * @param string $data
     * @return \Ves\Blog\Api\Data\CategoryInterface|bool
     */
    public function update($id, $data)
    {
        try {
            $item = $this->_itemFactory->create();
            $item->load($id);

            if (!$item->getId()) {
                return false;
            }
            $data = json_decode($data, true);
            $item->addData($data)->save();
            return $item;
        } catch (\Exception $e) {
            return false;
        }
    }


    /**
     * Get item by id
     *
     * @param  int $id
     * @return \Ves\Blog\Api\Data\CategoryInterface|bool
     */
    public function get($id)
    {
        try {
            $item = $this->_itemFactory->create();
            $item->load($id);

            if (!$item->getId()) {
                return false;
            }
            return $item;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get item by id and store id, only if item published
     *
     * @param  int $id
     * @param  int $storeId
     * @return \Ves\Blog\Api\Data\CategoryInterface|bool
     */
    public function view($id, $storeId)
    {
        try {
            $item = $this->_itemFactory->create();
            $item->load($id);

            if (!$item->isVisibleOnStore($storeId)) {
                return false;
            }
            $item->initDinamicData();
            return $item;
        } catch (\Exception $e) {
            return false;
        }
    }
}
