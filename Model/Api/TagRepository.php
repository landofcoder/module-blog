<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ves\Blog\Model\Api;

use Ves\Blog\Api\TagRepositoryInterface;
use Ves\Blog\Api\Data\TagInterfaceFactory;
use Ves\Blog\Api\Data\TagSearchResultsInterfaceFactory;
use Ves\Blog\Model\TagFactory;
use Ves\Blog\Model\ResourceModel\Tag as ResourceTag;
use Ves\Blog\Model\ResourceModel\Tag\CollectionFactory as TagCollectionFactory;
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
 * Class TagRepository
 * @package Ves\Blog\Model\Api
 */
class TagRepository implements TagRepositoryInterface
{

    /**
     * @var TagCollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ResourceTag
     */
    protected $resource;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var TagFactory
     */
    protected $tagFactory;

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
     * @var TagInterfaceFactory
     */
    protected $dataTagFactory;

    /**
     * @var TagSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var PostRepository
     */
    private $postRepository;


    /**
     * @param ResourceTag $resource
     * @param TagFactory $tagFactory
     * @param TagInterfaceFactory $dataTagFactory
     * @param TagCollectionFactory $tagCollectionFactory
     * @param TagSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param PostRepository $postRepository
     */
    public function __construct(
        ResourceTag $resource,
        TagFactory $tagFactory,
        TagInterfaceFactory $dataTagFactory,
        TagCollectionFactory $tagCollectionFactory,
        TagSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        PostRepository $postRepository
    ) {
        $this->resource = $resource;
        $this->tagFactory = $tagFactory;
        $this->tagCollectionFactory = $tagCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataTagFactory = $dataTagFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->postRepository = $postRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Ves\Blog\Api\Data\TagInterface $tag
    ) {
        $tagData = $this->extensibleDataObjectConverter->toNestedArray(
            $tag,
            [],
            \Ves\Blog\Api\Data\TagInterface::class
        );

        $tagModel = $this->tagFactory->create()->setData($tagData);

        try {
            $this->resource->save($tagModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the tag: %1',
                $exception->getMessage()
            ));
        }
        return $tagModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($tagId)
    {
        $tag = $this->tagFactory->create();
        $this->resource->load($tag, $tagId);
        if (!$tag->getId()) {
            throw new NoSuchEntityException(__('Tag with id "%1" does not exist.', $tagId));
        }
        return $tag->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function view($tagAlias)
    {
        $tag = null;
        $collection = $this->tagCollectionFactory->create();
        $collection->addFieldToFilter("alias", $tagAlias);
        if(!empty($collection)){
            $tag = $collection->getFirstItem();
            if (!$tag->getTagId()) {
                throw new NoSuchEntityException(__('Tag with alias "%1" does not exist.', $tagAlias));
            }
        }
        if (!$tag) {
            throw new NoSuchEntityException(__('Tag with alias "%1" does not exist.', $tagAlias));
        }
        return $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->tagCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Ves\Blog\Api\Data\TagInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $key => $model) {
            $items[$key] = $model->getDataModel();
            $items[$key]['post'] = $this->postRepository->get($model->getPostId());
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Ves\Blog\Api\Data\TagInterface $tag
    ) {
        try {
            $tagModel = $this->tagFactory->create();
            $this->resource->load($tagModel, $tag->getTagId());
            $this->resource->delete($tagModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Tag: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($tagId)
    {
        return $this->delete($this->get($tagId));
    }
}

