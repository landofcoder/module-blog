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

class FilterType implements \Magento\Framework\Option\ArrayInterface {

    protected $_categoryFactory;

    /**
     * @param \Ves\Blog\Model\Category
     */
    public function __construct(
        \Ves\Blog\Model\Category $categoryFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
            'value' => 'categories',
            'label' => __('Categories name'),
            ],
            [
            'value' => 'tags',
            'label' => __('Tags name'),
            ],
            [
            'value' => 'authors',
            'label' => __('Authors'),
            ],
        ];
        return $options;
    }

}
