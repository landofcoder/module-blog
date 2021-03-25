<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ves\Blog\Model\Api;

use Ves\Blog\Api\PostRepositoryInterface;
use Ves\Blog\Api\Data\PostInterfaceFactory;
use Ves\Blog\Api\Data\PostSearchResultsInterfaceFactory;
use Ves\Blog\Model\PostFactory;
use Ves\Blog\Model\ResourceModel\Post as ResourcePost;
use Ves\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class PostRepository implements PostRepositoryInterface
{

    protected $postCollectionFactory;

    protected $dataObjectProcessor;

    protected $resource;

    protected $extensionAttributesJoinProcessor;

    protected $postFactory;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $dataPostFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourcePost $resource
     * @param PostFactory $postFactory
     * @param PostInterfaceFactory $dataPostFactory
     * @param PostCollectionFactory $postCollectionFactory
     * @param PostSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourcePost $resource,
        PostFactory $postFactory,
        PostInterfaceFactory $dataPostFactory,
        PostCollectionFactory $postCollectionFactory,
        PostSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->postFactory = $postFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataPostFactory = $dataPostFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Ves\Blog\Api\Data\PostInterface $post
    ) {
        $postData = $this->extensibleDataObjectConverter->toNestedArray(
            $post,
            [],
            \Ves\Blog\Api\Data\PostInterface::class
        );
        $postModel = $this->postFactory->create()->setData($postData);
        
        try {
            $this->resource->save($postModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the post: %1',
                $exception->getMessage()
            ));
        }
        return $postModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $post = $this->postFactory->create();
        $this->resource->load($post, $id);
        if (!$post->getId()) {
            throw new NoSuchEntityException(__('Post with id "%1" does not exist.', $id));
        }
        return $post->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function view($id,$store_id = null)
    {
        $post = $this->postFactory->create();
        $this->resource->load($post, $id);
        if (!$post->getId()) {
            throw new NoSuchEntityException(__('Post with id "%1" does not exist.', $id));
        }
        if (!$post->getIsActive()) {
            throw new NoSuchEntityException(__('Post with id "%1" is not active.', $id));
        }
        if ($store_id !=null && !$post->isVisibleOnStore($store_id)) {
            throw new NoSuchEntityException(__('Post with id "%1" does not available on the store "%2".', $id, $store_id));
        }
        return $post->getDataModel();
    }


    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->postCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\PostInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        
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
    public function getPublishList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->postCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\PostInterface::class
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
        \Ves\Blog\Api\Data\PostInterface $post
    ) {
        try {
            $postModel = $this->postFactory->create();
            $this->resource->load($postModel, $post->getPostId());
            $this->resource->delete($postModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Post: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($postId)
    {
        return $this->delete($this->get($postId));
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedPosts($postId){
        $releatedPosts = $this->resource->lookupPostsRelatedIds($postId);
        return $releatedPosts?$releatedPosts:null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedProducts($postId){
        $releatedProducts = $this->resource->lookupProductsRelatedIds($postId);
        return $releatedProducts?$releatedProducts:null;
    }
}

