<?php


namespace Ves\Blog\Api\Data;

interface TagSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get post list.
     * @return \Ves\Blog\Api\Data\TagInterface[]
     */
    public function getItems();

    /**
     * Set post list.
     * @param \Ves\Blog\Api\Data\TagInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
