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

namespace Ves\Blog\Block\Adminhtml;

use Ves\All\Model\Config;

class Menu extends \Magento\Backend\Block\Template
{
    /**
     * @var null|array
     */
    protected $items = null;

    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Ves_All::menu.phtml';


    public function __construct(\Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context);

    }//end __construct()


    public function getMenuItems()
    {
        if ($this->items === null) {
            $items = [
                      'post' => [
                            'title' => __('Manage Posts'),
                            'url' => $this->getUrl('*/post/index'),
                            'resource' => 'Ves_Blog::post',
                            'child' => [
                                'post/new' => [
                                    'title' => __('New Post'),
                                    'url' => $this->getUrl('*/post/new'),
                                    'resource' => 'Ves_Blog::post_edit',
                                ]
                            ]
                        ],
                        'category' => [
                            'title' => __('Manage Categories'),
                            'url' => $this->getUrl('*/category/index'),
                            'resource' => 'Ves_Blog::category',
                            'child' => [
                                'category/new' => [
                                    'title' => __('New Category'),
                                    'url' => $this->getUrl('*/category/new'),
                                    'resource' => 'Ves_Blog::category_edit',
                                ]
                            ]
                        ],
                        'comment' => [
                            'title' => __('Manage Comments'),
                            'url' => $this->getUrl('*/comment/index'),
                            'resource' => 'Ves_Blog::comment'
                        ],
                        'author' => [
                            'title' => __('Manage Authors'),
                            'url' => $this->getUrl('*/author/index'),
                            'resource' => 'Ves_Blog::author'
                        ],
                        'myprofile' => [
                            'title' => __('My Profile'),
                            'url' => $this->getUrl('*/author/edit'),
                            'resource' => 'Ves_Blog::author_myprofile'
                        ],
                        'import' => [
                            'title' => __('Import'),
                            'url' => $this->getUrl('*/import/index'),
                            'resource' => 'Ves_Blog::import'
                        ],
                      'settings' => [
                                     'title'    => __('Settings'),
                                     'url'      => $this->getUrl('adminhtml/system_config/edit/section/vesblog'),
                                     'resource' => 'Ves_Blog::config_blog',
                                    ],
                      'guide'   => [
                                     'title'     => __('Guide'),
                                     'url'       => Config::BLOG_GUIDE,
                                     'attr'      => ['target' => '_blank'],
                                     'separator' => true,
                                    ],
                      'support'  => [
                                     'title' => __('Get Support'),
                                     'url'   => Config::LANDOFCODER_TICKET,
                                     'attr'  => ['target' => '_blank'],
                                    ],
                     ];
            foreach ($items as $index => $item) {
                if (array_key_exists('resource', $item)) {
                    if (!$this->_authorization->isAllowed($item['resource'])) {
                        unset($items[$index]);
                    }
                }
            }

            $this->items = $items;
        }//end if

        return $this->items;

    }//end getMenuItems()


    /**
     * @return array
     */
    public function getCurrentItem()
    {
        $items          = $this->getMenuItems();
        $controllerName = $this->getRequest()->getControllerName();
        $actionName     = $this->getRequest()->getActionName();

        $key = $controllerName . '/' . $actionName;
        if (array_key_exists($key, $items)) {
            return $items[$key];
        }

        if (array_key_exists($controllerName, $items)) {
            return $items[$controllerName];
        }

        return isset($items['page'])?$items['page']:'';

    }//end getCurrentItem()


    /**
     * @param array $item
     * @return string
     */
    public function renderAttributes(array $item)
    {
        $result = '';
        if (isset($item['attr'])) {
            foreach ($item['attr'] as $attrName => $attrValue) {
                $result .= sprintf(' %s=\'%s\'', $attrName, $attrValue);
            }
        }

        return $result;

    }//end renderAttributes()


    /**
     * @param $itemIndex
     * @return bool
     */
    public function isCurrent($itemIndex)
    {
        $controllerName = $this->getRequest()->getControllerName();
        $actionName     = $this->getRequest()->getActionName();
        $key = $controllerName . '/' . $actionName;
        if ($key == $itemIndex) {
            return true;
        }
        return $itemIndex == $this->getRequest()->getControllerName();

    }//end isCurrent()


}//end class
