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

namespace Ves\Blog\Model;

use Ves\Blog\Api\ManagementInterface;

/**
 * Abstract management model
 */
abstract class AbstractManagement implements ManagementInterface
{
    /**
     * @var Magento\Framework\Model\AbstractModel
     */
    protected $_itemFactory;

    /**
     * Delete item by id
     *
     * @param  int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $item = $this->_itemFactory->create();
            $item->load($id);
            if ($item->getId()) {
                $item->delete();
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
