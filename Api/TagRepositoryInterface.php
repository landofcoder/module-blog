<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ves\Blog\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TagRepositoryInterface
{

    /**
     * Save Tag
     * @param \Ves\Blog\Api\Data\TagInterface $tag
     * @return \Ves\Blog\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Ves\Blog\Api\Data\TagInterface $tag
    );

    /**
     * Retrieve tag
     * @param string $tagId
     * @return \Ves\Blog\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($tagId);

    /**
     * Retrieve tag
     * @param string $tagAlias
     * @return \Ves\Blog\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function view($tagAlias);

    /**
     * Retrieve Tag matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Ves\Blog\Api\Data\TagSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Tag
     * @param \Ves\Blog\Api\Data\TagInterface $tag
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Ves\Blog\Api\Data\TagInterface $tag
    );

    /**
     * Delete Tag by ID
     * @param string $tagId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tagId);
}

