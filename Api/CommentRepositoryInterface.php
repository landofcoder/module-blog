<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ves\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CommentRepositoryInterface
{

    /**
     * Save Comment
     * @param \Ves\Blog\Api\Data\CommentInterface $comment
     * @return \Ves\Blog\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Ves\Blog\Api\Data\CommentInterface $comment
    );

    /**
     * Retrieve comment
     * @param string $commentId
     * @return \Ves\Blog\Api\Data\CommentInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($commentId);

    /**
     * Retrieve Comment matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ves\Blog\Api\Data\CommentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Comment matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ves\Blog\Api\Data\CommentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPublishList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Retrieve Comment matching the specified criteria.
     * @param int $postId
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ves\Blog\Api\Data\CommentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPostCommentList(
        $postId,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Comment
     * @param \Ves\Blog\Api\Data\CommentInterface $comment
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Ves\Blog\Api\Data\CommentInterface $comment
    );

    /**
     * Delete Comment by ID
     * @param string $commentId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($commentId);
}

