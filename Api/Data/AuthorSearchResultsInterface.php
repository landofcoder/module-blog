<?php


namespace Ves\Blog\Api\Data;

interface AuthorSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get post list.
     * @return \Ves\Blog\Api\Data\AuthorInterface[]
     */
    public function getItems();

    /**
     * Set post list.
     * @param \Ves\Blog\Api\Data\AuthorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
