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

use Magento\Framework\Data\OptionSourceInterface;

class User implements OptionSourceInterface
{
    /**
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @var \Ves\Blog\Model\Author
     */
    protected $_authorFactory;

    public function __construct(
      \Magento\User\Model\UserFactory $userFactory,
      \Ves\Blog\Model\Author $authorFactory
    ) {
        $this->_userFactory   = $userFactory;
        $this->_authorFactory = $authorFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $collection = $this->_authorFactory->getCollection();
        $options = [];

        $options[0] = __(' ');

        if($collection->count()){
            $collection = $this->_authorFactory->getCollection();
            foreach ($collection as $_author) {
                $options[$_author->getUserId()] = $_author->getNickName();
            }
        }else{
            $collection = $this->_userFactory->create()->getCollection();
            foreach ($collection as $_user) {
                $data = [
                  'user_name' => $_user->getUserName(),
                  'user_id' => $_user->getUserId(),
                  'email' => $_user->getEmail()
                ];
                $this->_authorFactory->setData($data)->save();
               $options[$_user->getUserId()] = $_user->getFirstname() . ' ' . $_user->getLastname();
           }
       }
       return $options;
   }
}
