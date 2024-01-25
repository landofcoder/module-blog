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

class LatestPostsStyle implements \Magento\Framework\Option\ArrayInterface
{
    const STYLE1="style1";
    const STYLE2="style2";
    const STYLE3="style3";
    const STYLE4="style4";

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::STYLE1, 'label' => __('Style 1')],
            ['value' => self::STYLE2, 'label' => __('Style 2')],
            ['value' => self::STYLE3, 'label' => __('Style 3')],
            ['value' => self::STYLE4, 'label' => __('Style 4')]
        ];
    }
}
