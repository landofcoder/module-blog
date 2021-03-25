<?php


namespace Ves\Blog\Api\Data;

interface CommentSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get post list.
     * @return \Ves\Blog\Api\Data\CommentInterface[]
     */
    public function getItems();

    /**
     * Set post list.
     * @param \Ves\Blog\Api\Data\CommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
