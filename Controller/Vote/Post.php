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
namespace Ves\Blog\Controller\Vote;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Display Hello on screen
 */
class Post extends \Magento\Framework\App\Action\Action
{
    protected $_cacheTypeList;
	/**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Ves\Blog\Helper\Data
     */
    protected $_blogHelper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    protected $resultPageFactory;
    protected $_coreRegistry;

    protected $_post;
    protected $_vote;
    protected $_customerSession;

    /**
     * @param Context                                             $context
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory
     * @param \Ves\Blog\Helper\Data                               $blogHelper
     * @param \Ves\Blog\Model\Vote                               $vote
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry                         $registry
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ves\Blog\Helper\Data $blogHelper,
        \Ves\Blog\Model\Post $post,
        \Ves\Blog\Model\Vote $vote,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->resultPageFactory    = $resultPageFactory;
        $this->_blogHelper          = $blogHelper;
        $this->_post                = $post;
        $this->_vote                = $vote;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry        = $registry;
        $this->_cacheTypeList       = $cacheTypeList;
        $this->_customerSession     = $customerSession;
        $this->_request             = $context->getRequest();
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $params = $this->_request->getPostValue();
        if(!empty($params)){
            $voteCollection = $this->_vote->getCollection();
            $disllike = $like = 0;
            if($params['like']){
                $like = 1;
                $disllike = 0;
            }else{
                $like = 0;
                $disllike = 1;
            }
            $customer = $this->_customerSession->getCustomer();
            $data = [
            'ip'             => $_SERVER['REMOTE_ADDR'],
            'customer_email' => $customer->getName(),
            'customer_name'  => $customer->getEmail(),
            'like'           => $like,
            'disllike'       => $disllike,
            'post_id'        => $params['postId']
            ];
            $collection = $voteCollection->addFieldToFilter('ip', $this->getUserIP())
            ->addFieldToFilter('post_id', $params['postId']);
            $vote = $this->_vote->load($this->getUserIP(), 'ip');
            $post = $this->_post->load($params['postId']);
            $postData = $responseData = [];
            if(empty($collection->getData())){
                $like = (int)$post->getLike() + $like;
                $disllike = (int)$post->getDisklike() + $disllike;
                $post->setLike($like);
                $post->setDisklike($disllike);

                $responseData['like'] = $like;
                $responseData['disklike'] = $disllike;
                try{
                    $message = __('Thanks for your vote!');
                    $status = 1;
                    $vote->setData($data)->save();
                    $post->save();
                    //$this->_cacheTypeList->cleanType('full_page');
                }catch(\Exception $e){
                    $this->messageManager->addError(
                        __('We can\'t process your request right now. Sorry, that\'s all we know.')
                        );
                    return;
                }
            }else{
                $status = 0;
                $message = __('Already voted!');
            }
            $responseData['message'] = $message;
            $responseData['status'] = $status;
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($responseData)
                );
            return;
        }
    }

        public function getUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }
        return $ip;
    }

}
