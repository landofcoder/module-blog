<?php


namespace Ves\Blog\Api\Data;

interface PostSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get post list.
     * @return \Ves\Blog\Api\Data\PostInterface[]
     */
    public function getItems();

    /**
     * Set post list.
     * @param \Ves\Blog\Api\Data\PostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
