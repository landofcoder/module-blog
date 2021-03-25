<?php


namespace Ves\Blog\Api\Data;

interface CategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get category list.
     * @return \Ves\Blog\Api\Data\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set category list.
     * @param \Ves\Blog\Api\Data\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
