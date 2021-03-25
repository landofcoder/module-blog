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
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Ves\Blog\Api\PostManagementInterface;
use Ves\Blog\Helper\Data;
/**
 * Post management model
 */
class PostManagement extends AbstractManagement implements PostManagementInterface
{
    /**
     * @var PostFactory
     */
    protected $_itemFactory;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Initialize dependencies.
     *
     * @param Data $data
     * @param StoreManagerInterface $storeManager
     * @param PostFactory $postFactory
     */
    public function __construct(
        Data $data,
        StoreManagerInterface $storeManager,
        PostFactory $postFactory
    ) {
        $this->helper = $data;
        $this->storeManager = $storeManager;
        $this->_itemFactory = $postFactory;
    }

    /**
     * Retrieve list of post by page type, term, store, etc
     *
     * @param  string $type
     * @param  string $term
     * @param  int $storeId
     * @param  int $page
     * @param  int $limit
     * @return \Ves\Blog\Api\Data\PostInterface[]
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
                case 'archive':
                    $term = explode('-', $term);
                    if (count($term) < 2) {
                        return false;
                    }
                    list($year, $month) = $term;
                    $year = (int) $year;
                    $month = (int) $month;

                    if ($year < 1970) {
                        return false;
                    }
                    if ($month < 1 || $month > 12) {
                        return false;
                    }

                    $collection->addArchiveFilter($year, $month);
                    $collection->setOrder('creation_time', 'DESC');
                    break;
                case 'author':
                    $collection->addAuthorFilter($term);
                    $collection->setOrder('creation_time', 'DESC');
                    break;
                case 'category':
                    $collection->addCategoryFilter($term);
                    $collection->setOrder('creation_time', 'DESC');
                    break;
                case 'search':
                    $collection->addSearchFilter($term);
                    $collection->setOrder('creation_time', 'DESC');
                    break;
                case 'tag':
                    $collection->addTagFilter($term);
                    $collection->setOrder('creation_time', 'DESC');
                    break;
                case 'latest':
                    $term = strtolower($term);
                    $arr = array("desc","asc");
                    if(!in_array($term, $arr)){
                        $term = "desc";
                    }
                    $collection->getSelect()->order("main_table.creation_time " . $term);
                    break;
            }

            $posts = [];

            foreach ($collection as $item) {
                $item->initDinamicData();
                $posts[] = $item->getData();
            }

            $result = [
                'posts' => $posts,
                'total_number' => $collection->getSize(),
                'current_page' => $collection->getCurPage(),
                'last_page' => $collection->getLastPageNumber(),
            ];

            return json_encode($result);
        } catch (\Exception $e) {
            print_r($e->getMessage());
            return false;
        }
    }


    /**
     * Create new item using data
     *
     * @param string $data
     * @return \Ves\Blog\Api\Data\PostInterface|bool
     */
    public function create($data)
    {
        try {
            $data = json_decode($data, true);
            $item = $this->_itemFactory->create();
            $item->setData($data)->save();
            return json_encode($item->getData());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update item using data
     *
     * @param int $id
     * @param string $data
     * @return \Ves\Blog\Api\Data\PostInterface|bool
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
            return json_encode($item->getData());
        } catch (\Exception $e) {
            return false;
        }
    }



    /**
     * Get item by id
     *
     * @param  int $id
     * @return \Ves\Blog\Api\Data\PostInterface|bool
     */
    public function get($id)
    {
        try {
            $item = $this->_itemFactory->create();
            $item->load($id);
            $author = $this->helper->getPostAuthor($item);
            if ($author) {
                $avatar = $this->getBaseUrl()."media/".$author->getAvatar();
                $author->setAvatar($avatar);
                $item->setAuthor($author->getData());
            }
            $item->setImage($item->getImageUrl());
            $item->setThumbnail($item->getThumbnailUrl());
            $related = $item->getRelated();
            $item->setRelatedProducts($related['related_products']);
            $item->setRelatedPosts($related['related_posts']);
            if (!$item->getId()) {
                return false;
            }
            return $item->getData();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get item by id and store id, only if item published
     *
     * @param  int $id
     * @param  int $storeId
     * @return \Ves\Blog\Api\Data\PostInterface|bool
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
            return json_encode($item->getData());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }
}
