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
namespace Ves\Blog\Model\Config\Source;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_LIST = 'list';
    const TYPE_GRID = 'grid';
    const TYPE_MASONRY = 'masonry';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_LIST, 'label' => __('List')],
            ['value' => self::TYPE_GRID, 'label' => __('Grid')],
            ['value' => self::TYPE_MASONRY, 'label' => __('Masonry')]
        ];
    }
}
