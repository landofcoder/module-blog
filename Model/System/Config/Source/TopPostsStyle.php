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
 * @package    Ves_Themesettings
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Blog\Model\System\Config\Source;

class TopPostsStyle implements \Magento\Framework\Option\ArrayInterface
{
	public function toOptionArray()
	{
		return [
				['value' => 'grid1', 'label' => __('Grid 1')],
				['value' => 'grid2', 'label' => __('Grid 2')],
				['value' => 'grid3', 'label' => __('Grid 3')],
				['value' => 'grid4', 'label' => __('Grid 4')],
				['value' => 'grid5', 'label' => __('Grid 5')],
				['value' => 'grid6', 'label' => __('Grid 6')],
				['value' => 'grid7', 'label' => __('Grid 7')],
				['value' => 'grid8', 'label' => __('Grid 8')],
				['value' => 'grid9', 'label' => __('Grid 9')],
				['value' => 'grid10', 'label' => __('Grid 10')],
				['value' => 'grid11', 'label' => __('Grid 11')],
				['value' => 'grid12', 'label' => __('Grid 12')],
				['value' => 'grid13', 'label' => __('Grid 13')],
				['value' => 'grid14', 'label' => __('Grid 14')],
				['value' => 'grid15', 'label' => __('Grid 15')],
				['value' => 'grid16', 'label' => __('Grid 16')]
			];
	}
}