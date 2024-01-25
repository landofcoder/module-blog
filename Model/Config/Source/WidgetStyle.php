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

class WidgetStyle implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = [
            'label' => 'Style1',
            'value' => 'style1'
        ];
        $options[] = [
            'label' => 'Style2',
            'value' => 'style2'
        ];
        $options[] = [
            'label' => 'Style3',
            'value' => 'style3'
        ];
        $options[] = [
            'label' => 'Style4',
            'value' => 'style4'
        ];
        $options[] = [
            'label' => 'Style5',
            'value' => 'style5'
        ];
        $options[] = [
            'label' => 'Tab',
            'value' => 'tab'
        ];
        $options[] = [
            'label' => 'Timeline',
            'value' => 'timeline'
        ];
        return $options;
    }
}
