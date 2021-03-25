<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Ves\Blog\Api;

interface CategoryManagementInterface extends ManagementInterface
{
    /**
     * Create new item.
     *
     * @api
     * @param string $data.
     * @return \Ves\Blog\Api\Data\CategoryInterface|bool
     */
    public function create($data);

    /**
     * Update item by id.
     *
     * @api
     * @param int $id.
     * @param string $data.
     * @return \Ves\Blog\Api\Data\CategoryInterface|bool
     */
    public function update($id, $data);

    /**
     * Get item by id.
     *
     * @api
     * @param int $id.
     * @return \Ves\Blog\Api\Data\CategoryInterface|bool
     */
    public function get($id);

    /**
     * Get item by id and store id, only if item published
     *
     * @api
     * @param int $id
     * @param  int $storeId
     * @return \Ves\Blog\Api\Data\CategoryInterface|bool
     */
    public function view($id, $storeId);

    /**
     * Retrieve list by page type, term, store, etc
     *
     * @param  string $type
     * @param  string $term
     * @param  int $storeId
     * @param  int $page
     * @param  int $limit
     * @return \Ves\Blog\Api\Data\CategoryInterface[]
     */
    public function getList($type, $term, $storeId, $page, $limit);
}
