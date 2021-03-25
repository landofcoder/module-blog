<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ves\Blog\Model\Api;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Ves\Blog\Api\AuthorRepositoryInterface;
use Ves\Blog\Api\Data\AuthorInterfaceFactory;
use Ves\Blog\Api\Data\AuthorSearchResultsInterfaceFactory;
use Ves\Blog\Model\AuthorFactory;
use Ves\Blog\Model\ResourceModel\Author as ResourceAuthor;
use Ves\Blog\Model\ResourceModel\Author\CollectionFactory as AuthorCollectionFactory;
use Ves\Blog\Model\ResourceModel\Post\Collection;
use Ves\Blog\Model\ResourceModel\Post\CollectionFactory;

/**
 * Class AuthorRepository
 * @package Ves\Blog\Model\Api
 */
class AuthorRepository implements AuthorRepositoryInterface
{

    /**
     * @var AuthorCollectionFactory
     */
    protected $authorCollectionFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ResourceAuthor
     */
    protected $resource;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var AuthorFactory
     */
    protected $authorFactory;

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
     * @var AuthorInterfaceFactory
     */
    protected $dataAuthorFactory;

    /**
     * @var AuthorSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var Collection
     */
    private $postCollection;


    /**
     * @param ResourceAuthor $resource
     * @param AuthorFactory $authorFactory
     * @param AuthorInterfaceFactory $dataAuthorFactory
     * @param AuthorCollectionFactory $authorCollectionFactory
     * @param AuthorSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param Collection $postCollection
     */
    public function __construct(
        ResourceAuthor $resource,
        AuthorFactory $authorFactory,
        AuthorInterfaceFactory $dataAuthorFactory,
        AuthorCollectionFactory $authorCollectionFactory,
        AuthorSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CollectionFactory $postCollection
    ) {
        $this->resource = $resource;
        $this->authorFactory = $authorFactory;
        $this->authorCollectionFactory = $authorCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataAuthorFactory = $dataAuthorFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->postCollection = $postCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Ves\Blog\Api\Data\AuthorInterface $author
    ) {
        $authorData = $this->extensibleDataObjectConverter->toNestedArray(
            $author,
            [],
            \Ves\Blog\Api\Data\AuthorInterface::class
        );

        $authorModel = $this->authorFactory->create()->setData($authorData);

        try {
            $this->resource->save($authorModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the author: %1',
                $exception->getMessage()
            ));
        }
        return $authorModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($authorId)
    {
        $author = $this->authorFactory->create();
        $this->resource->load($author, $authorId);
        $postCollection = $this->postCollection->create()->addFieldToFilter('user_id', $author->getUserId())->addFieldToFilter('is_active', '1');
        $posts = [];
        $posts['total_count'] = $postCollection->getSize();
        foreach ($postCollection as $key => $post) {
            $post->load($post->getPostId());
            $posts['items'][$key] = $post->getData();
        }
        $author->setData('posts', $posts);
        $avatar = $this->getBaseUrl()."media/".$author->getAvatar();
        $author->setAvatar($avatar);
        if (!$author->getId()) {
            throw new NoSuchEntityException(__('Author with id "%1" does not exist.', $authorId));
        }
        return $author->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function view($authorId)
    {
        $author = $this->authorFactory->create();
        $this->resource->load($author, $authorId);
        if (!$author->getId()) {
            throw new NoSuchEntityException(__('Author with id "%1" does not exist.', $authorId));
        }
        if (!$author->getIsView()) {
            throw new NoSuchEntityException(__('Author with id "%1" does not exist.', $authorId));
        }
        return $author->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->authorCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\AuthorInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $postCollection = $this->postCollection->create()->addFieldToFilter('user_id', $model->getUserId())->addFieldToFilter('is_active', '1');
            $posts = [];
            $posts['total_count'] = $postCollection->getSize();
            foreach ($postCollection as $key => $post) {
                $post->load($post->getPostId());
                $posts['items'][$key] = $post->getData();
            }
            $model->setData('posts', $posts);
            $avatar = $this->getBaseUrl()."media/".$model->getAvatar();
            $model->setAvatar($avatar);
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
        $collection = $this->authorCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\AuthorInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);
        $collection->addFieldToFilter("is_view", 1);
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
        \Ves\Blog\Api\Data\AuthorInterface $author
    ) {
        try {
            $authorModel = $this->authorFactory->create();
            $this->resource->load($authorModel, $author->getAuthorId());
            $this->resource->delete($authorModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Author: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($authorId)
    {
        return $this->delete($this->get($authorId));
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

