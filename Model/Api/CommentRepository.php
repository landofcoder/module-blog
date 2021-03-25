<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ves\Blog\Model\Api;

use Ves\Blog\Api\CommentRepositoryInterface;
use Ves\Blog\Api\Data\CommentInterfaceFactory;
use Ves\Blog\Api\Data\CommentSearchResultsInterfaceFactory;
use Ves\Blog\Model\CommentFactory;
use Ves\Blog\Model\ResourceModel\Comment as ResourceComment;
use Ves\Blog\Model\ResourceModel\Comment\CollectionFactory as CommentCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class CommentRepository implements CommentRepositoryInterface
{

    protected $commentCollectionFactory;

    protected $dataObjectProcessor;

    protected $resource;

    protected $extensionAttributesJoinProcessor;

    protected $commentFactory;

    private $collectionProcessor;

    protected $extensibleDataObjectConverter;
    private $storeManager;

    protected $dataCommentFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;


    /**
     * @param ResourceComment $resource
     * @param CommentFactory $commentFactory
     * @param CommentInterfaceFactory $dataCommentFactory
     * @param CommentCollectionFactory $commentCollectionFactory
     * @param CommentSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceComment $resource,
        CommentFactory $commentFactory,
        CommentInterfaceFactory $dataCommentFactory,
        CommentCollectionFactory $commentCollectionFactory,
        CommentSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->commentFactory = $commentFactory;
        $this->commentCollectionFactory = $commentCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCommentFactory = $dataCommentFactory;
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
        \Ves\Blog\Api\Data\CommentInterface $comment
    ) {
        $commentData = $this->extensibleDataObjectConverter->toNestedArray(
            $comment,
            [],
            \Ves\Blog\Api\Data\CommentInterface::class
        );
        
        $commentModel = $this->commentFactory->create()->setData($commentData);
        
        try {
            $this->resource->save($commentModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the comment: %1',
                $exception->getMessage()
            ));
        }
        return $commentModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($commentId)
    {
        $comment = $this->commentFactory->create();
        $this->resource->load($comment, $commentId);
        if (!$comment->getId()) {
            throw new NoSuchEntityException(__('Comment with id "%1" does not exist.', $commentId));
        }
        return $comment->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->commentCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\CommentInterface::class
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
        $collection = $this->commentCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\CommentInterface::class
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
    public function getPostCommentList(
        $postId,
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->commentCollectionFactory->create();
        
        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\CommentInterface::class
        );
        
        $this->collectionProcessor->process($criteria, $collection);
        $collection->addFieldToFilter("post_id", $postId);
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
        \Ves\Blog\Api\Data\CommentInterface $comment
    ) {
        try {
            $commentModel = $this->commentFactory->create();
            $this->resource->load($commentModel, $comment->getCommentId());
            $this->resource->delete($commentModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Comment: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($commentId)
    {
        return $this->delete($this->get($commentId));
    }
}

