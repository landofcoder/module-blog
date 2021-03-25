<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ves\Blog\Model\Api;

use Ves\Blog\Api\CategoryRepositoryInterface;
use Ves\Blog\Api\Data\CategoryInterfaceFactory;
use Ves\Blog\Api\Data\CategorySearchResultsInterfaceFactory;
use Ves\Blog\Model\CategoryFactory;
use Ves\Blog\Api\PostManagementInterface;
use Ves\Blog\Model\ResourceModel\Category as ResourceCategory;
use Ves\Blog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CategoryRepository
 * @package Ves\Blog\Model\Api
 */
class CategoryRepository implements CategoryRepositoryInterface
{

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ResourceCategory
     */
    protected $resource;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryInterfaceFactory
     */
    protected $dataCategoryFactory;

    /**
     * @var CategorySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var PostManagementInterface
     */
    private $postManagement;


    /**
     * @param ResourceCategory $resource
     * @param CategoryFactory $categoryFactory
     * @param CategoryInterfaceFactory $dataCategoryFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategorySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param PostManagementInterface $postManagement
     */
    public function __construct(
        ResourceCategory $resource,
        CategoryFactory $categoryFactory,
        CategoryInterfaceFactory $dataCategoryFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        CategorySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        PostManagementInterface $postManagement
    ) {
        $this->resource = $resource;
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCategoryFactory = $dataCategoryFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->postManagement = $postManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Ves\Blog\Api\Data\CategoryInterface $category
    ) {
        $categoryData = $this->extensibleDataObjectConverter->toNestedArray(
            $category,
            [],
            \Ves\Blog\Api\Data\CategoryInterface::class
        );

        $categoryModel = $this->categoryFactory->create()->setData($categoryData);

        try {
            $this->resource->save($categoryModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the category: %1',
                $exception->getMessage()
            ));
        }
        return $categoryModel->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function get($categoryId)
    {
        $category = $this->categoryFactory->create();
        $this->resource->load($category, $categoryId);
        if (!$category->getId()) {
            throw new NoSuchEntityException(__('Category with id "%1" does not exist.', $categoryId));
        }
        $postData = [];
        $posts = $category->getPosts();
        foreach ($posts as $key => $item) {
            $postData[$key] = $this->postManagement->get($item['post_id']);
        }
        $category->setPosts($postData);
        return $category->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function view($categoryId,$storeId =null)
    {
        $category = $this->categoryFactory->create();
        $this->resource->load($category, $categoryId);
        if (!$category->getId()) {
            throw new NoSuchEntityException(__('Category with id "%1" does not exist.', $categoryId));
        }
        if (!$category->getIsActive()) {
            throw new NoSuchEntityException(__('Category with id "%1" is not active.', $categoryId));
        }
        if ($storeId !=null && !$category->isVisibleOnStore($storeId)) {
            throw new NoSuchEntityException(__('Category with id "%1" is not avaialable in the store "%2".', $categoryId, $storeId));
        }
        return $category->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->categoryCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\CategoryInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $model->load($model->getCategoryId());
            $postData = [];
            $posts = $model->getPosts();
            foreach ($posts as $key => $item) {
                $postData[$key] = $this->postManagement->get($item['post_id']);
            }
            $model->setPosts($postData);
            $items[] = $model->getData();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublishList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->categoryCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\CategoryInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $collection->addFieldToFilter("is_active", 1);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Ves\Blog\Api\Data\CategoryInterface $category
    ) {
        try {
            $categoryModel = $this->categoryFactory->create();
            $this->resource->load($categoryModel, $category->getCategoryId());
            $this->resource->delete($categoryModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Category: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($categoryId)
    {
        return $this->delete($this->get($categoryId));
    }
}

