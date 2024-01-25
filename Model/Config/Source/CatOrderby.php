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

class CatOrderby implements \Magento\Framework\Option\ArrayInterface
{
    const NEWEST = 1;
    const OLDER = 2;
    const POSITION_HIGHT_TO_LOW = 3;
    const POSITION_LOW_TO_HIGHT = 3;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NEWEST, 'label' => __('Newest first')],
            ['value' => self::OLDER, 'label' => __('Older first')],
            ['value' => self::POSITION_HIGHT_TO_LOW, 'label' => __('Position: High to Low')],
            ['value' => self::POSITION_LOW_TO_HIGHT, 'label' => __('Position: Low to High')]
        ];
    }
}
