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
namespace Ves\Blog\Controller\Comment;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Add extends \Magento\Framework\App\Action\Action
{
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

    protected $_comment;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Ves\Blog\Helper\Data
     */
    protected $_blogHelper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $resultPageFactory;
    protected $_coreRegistry;

    /**
     * [__construct description]
     * @param Context                                             $context
     * @param \Magento\Framework\View\Result\PageFactory          $resultPageFactory
     * @param \Ves\Blog\Helper\Data                               $blogHelper
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface  $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder   $transportBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface          $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ves\Blog\Helper\Data $blogHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
        ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_blogHelper = $blogHelper;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->inlineTranslation->suspend();
        $resultRedirect = $this->resultRedirectFactory->create();
        if($data = $this->getRequest()->getPostValue()) {
            $show_captcha = $this->_blogHelper->getConfig("post_page/show_captcha");
            if ($show_captcha) {
                if (!isset($_POST['g-recaptcha-response'])) {
                    $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                    return $resultRedirect->setRefererOrBaseUrl();
                }
                // reCaptcha
                if(((int)$_POST['g-recaptcha-response']) === 0) {
                    $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                    return $resultRedirect->setRefererOrBaseUrl();
                }
                $captcha=$_POST['g-recaptcha-response'];
                $secretKey = $this->_blogHelper->getConfig("post_page/captcha_privatekey");
                $ip = $_SERVER['REMOTE_ADDR'];
                $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretKey."&response=".$captcha."&remoteip=".$ip);
                $responseKeys = json_decode($response,true);
                if(intval($responseKeys["success"]) !== 1) {
                    $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                    return $resultRedirect->setRefererOrBaseUrl();
                }
            }

            $postObject = new \Magento\Framework\DataObject();
            $allowableTags = $this->_blogHelper->getConfig("post_page/allowable_tags");
            if (isset($data['content']) && $allowableTags) {
                $data['content'] = strip_tags($data['content'], $allowableTags);
            }
            $data['content'] = $this->_blogHelper->xss_clean($data['content']);

            $model = $this->_objectManager->create('Ves\Blog\Model\Comment');
            $store = $this->_storeManager->getStore();
            $data['parent_id'] = isset($data['comment_parent'])?(int)$data['comment_parent']:0;
            $data['stores'][] = $store->getId();
            $auto_public = $this->_blogHelper->getConfig("post_page/auto_public");
            if($auto_public){
                $data['is_active'] = 1;
            }else{
                $data['is_active'] = 0;
            }
            $model->setData($data);

            //Set data for email template
            $data['parent_user_name'] = "";
            $data['parent_user_email'] = "";
            $data['parent_content'] = "";
            $data['parent_creation_time'] = "";
            if($data['parent_id']) {
                $parent_model = $this->_objectManager->create('Ves\Blog\Model\Comment')->load((int)$data['parent_id']);
                if($parent_model->getId()){
                    $data['parent_user_name'] = $parent_model->getUserName();
                    $data['parent_user_email'] = $parent_model->getUserEmail();
                    $data['parent_content'] = $parent_model->getContent();
                    $data['parent_creation_time'] = $parent_model->getCreationTime();
                }
            }
            $data['store_name'] = $store->getName();
            $postObject->setData($data);
            try{
                $model->save();
                $enable_send_email = $this->_blogHelper->getConfig("post_page/enable_send_email"); //get config send mail
                if($enable_send_email) {
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
                    $fromEmail = $this->scopeConfig->getValue('vesblog/post_page/sender_email_identity', $storeScope);
                    $toEmail = $this->scopeConfig->getValue('vesblog/post_page/recipient_email', $storeScope);
                    if($fromEmail && $toEmail) {
                        $send_to_emails = [];
                        $send_to_emails[] = $toEmail;
                        if($data['parent_user_email']){
                            $send_to_emails[] = $data['parent_user_email'];
                        }
                        if($send_to_emails) {
                            foreach($send_to_emails as $to_email) {
                                $transport = $this->_transportBuilder
                                            ->setTemplateIdentifier($this->scopeConfig->getValue('vesblog/post_page/email_template', $storeScope))
                                            ->setTemplateOptions(
                                                [
                                                'area' => 'frontend',
                                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                                                ]
                                                )
                                            ->setTemplateVars(['data' => $postObject])
                                            ->setFrom($fromEmail)
                                            ->addTo($toEmail)
                                            ->setReplyTo($data['user_email'])
                                            ->getTransport();
                                try  {
                                    $transport->sendMessage();
                                    $this->inlineTranslation->resume();
                                } catch(\Exception $e) {
                                    $this->messageManager->addError(
                                        __('We can\'t send notify email right now. Sorry, that\'s all we know.')
                                        );
                                }
                            }
                        }
                    }
                }
                if($auto_public){
                    $this->messageManager->addSuccess(__('Your comment added'));
                }else{
                    $this->messageManager->addSuccess(__('Your comment added, it will be published soon.'));
                }
            }catch(\Exception $e){
                $this->inlineTranslation->resume();
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
            }
        }
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
