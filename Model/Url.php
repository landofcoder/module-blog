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

namespace Ves\Blog\Model;

/**
 * Blog url model
 */
class Url
{
    /**
     * Permalink Types
     */
    const PERMALINK_TYPE_DEFAULT = 'default';
    const PERMALINK_TYPE_SHORT = 'short';

    /**
     * Objects Types
     */
    const CONTROLLER_POST = 'post';
    const CONTROLLER_CATEGORY = 'category';
    const CONTROLLER_ARCHIVE = 'archive';
    const CONTROLLER_AUTHOR = 'author';
    const CONTROLLER_SEARCH = 'search';
    const CONTROLLER_RSS = 'rss';
    const CONTROLLER_TAG = 'tag';


    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store id
     * @var int | null
     */
    protected $storeId;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_registry = $registry;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve permalink type
     * @return string
     */
    public function getPermalinkType()
    {
        return $this->_getConfig('type');
    }

    /**
     * Retrieve route name by controller
     * @param  string  $controllerName
     * @param  boolean $skip
     * @return string || null
     */
    public function getRoute($controllerName = null, $skip = true)
    {
        if ($controllerName) {
            $controllerName .= '_';
        }

        if ($route = $this->_getConfig($controllerName . 'route')) {
            return $route;
        } else {
            return $skip ? $controllerName : null;
        }
    }

    /**
     * Retrieve controller name by route
     * @param  string  $route
     * @param  boolean $skip
     * @return string || null
     */
    public function getControllerName($route, $skip = true)
    {
        foreach ([
            self::CONTROLLER_POST,
            self::CONTROLLER_CATEGORY,
            self::CONTROLLER_ARCHIVE,
            self::CONTROLLER_AUTHOR,
            self::CONTROLLER_TAG,
            self::CONTROLLER_SEARCH
        ] as $controllerName) {
            if ($this->getRoute($controllerName) == $route) {
                return $controllerName;
            }
        }

        return $skip ? $route : null;
    }

    /**
     * Retrieve blog base url
     * @return string
     */
    public function getBaseUrl()
    {
        $url = $this->_url->getUrl($this->getBasePath());
        $url = $this->trimSlash($url);
        return $url;
    }

    /**
     * Retrieve blog page url
     * @param  string $identifier
     * @param  string $controllerName
     * @return string
     */
    public function getUrl($identifier, $controllerName)
    {
        $url = $this->_url->getUrl('', [
            '_direct' => $this->getUrlPath($identifier, $controllerName)
        ]);

        return $url;
    }

    /**
     * Retrieve canonical url
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return string
     */
    public function getCanonicalUrl(\Magento\Framework\Model\AbstractModel $object)
    {
        $storeIds = $object->getStoreIds();
        $useDefaultStore = false;
        $currentStore = $this->_storeManager->getStore($object->getStoreId());

        if (is_array($storeIds)) {
            if (0 == array_values($storeIds)[0]) {
                $useDefaultStore = true;
            } elseif (count($storeIds) > 1) {
                foreach ($storeIds as $storeId) {
                    if ($storeId != $currentStore->getId()) {
                        $store = $this->_storeManager->getStore($storeId);
                        if ($store->getGroupId() == $currentStore->getGroupId()) {
                            $useDefaultStore = true;
                            break;
                        }
                    }
                }
            }
        }

        $storeChanged = false;

        if ($useDefaultStore) {
            $newStore = $currentStore->getGroup()->getDefaultStore();
            $origStore = $this->_url->getScope();
            if ($newStore->getId() != $origStore->getId()) {
                $this->_url->setScope($newStore);
                $storeChanged = true;
            }
        }

        $url = $this->getUrl($object, $object->getControllerName());

        if ($storeChanged) {
            $this->_url->setScope($origStore);
        }

        return $url;
    }

    /**
     * Retrieve blog base path
     * @return string
     */
    public function getBasePath()
    {
        return $this->getRoute();
    }

    /**
     * Retrieve blog url path
     * @param  string $identifier
     * @param  string $controllerName
     * @return string
     */
    public function getUrlPath($identifier, $controllerName)
    {
        $identifier = $this->getExpandedItentifier($identifier);
        switch ($this->getPermalinkType()) {
            case self::PERMALINK_TYPE_DEFAULT:
                $path = $this->getRoute() . '/' . $this->getRoute($controllerName) . '/' . $identifier . ( $identifier ? '/' : '');
                break;
            case self::PERMALINK_TYPE_SHORT:
                if ($controllerName == self::CONTROLLER_SEARCH
                    || $controllerName == self::CONTROLLER_AUTHOR
                    || $controllerName == self::CONTROLLER_TAG
                    || $controllerName == self::CONTROLLER_RSS
                ) {
                    $path = $this->getRoute() . '/' . $this->getRoute($controllerName) . '/' . $identifier . ( $identifier ? '/' : '');
                } else {
                    $path = $this->getRoute() . '/' . $identifier . ( $identifier ? '/' : '');
                }
                break;
        }

        $path = $this->addUrlSufix($path, $controllerName);
        $path = $this->trimSlash($path);

        return $path;
    }

    /**
     * Retrieve itentifier what include parent categories itentifier
     * @param  \Magento\Framework\Model\AbstractModel || string $identifier
     * @return string
     */
    protected function getExpandedItentifier($identifier)
    {
        if (is_object($identifier)) {
            $object = $identifier;
            $identifier = $identifier->getIdentifier();

            $controllerName = $object->getControllerName();
            if ($this->_getConfig($controllerName . '_use_categories')
            ) {
                if ($parentCategory = $object->getParentCategory()) {
                    if ($parentIdentifier = $this->getExpandedItentifier($parentCategory)) {
                        $identifier = $parentIdentifier . '/' . $identifier;
                    }
                }
            }
        }

        return $identifier;
    }

    /**
     * Add url sufix
     * @param string $url
     * @param string $controllerName
     * @return string
     */
    protected function addUrlSufix($url, $controllerName)
    {
        if (in_array($controllerName, [
            self::CONTROLLER_POST,
            self::CONTROLLER_CATEGORY,
            self::CONTROLLER_AUTHOR,
            self::CONTROLLER_TAG
        ])) {
            if ($sufix = $this->getUrlSufix($controllerName)) {
                $char = false;
                foreach (['#', '?'] as $ch) {
                    if (false !== strpos($url, $ch)) {
                        $char = $ch;
                    }
                }
                if ($char) {
                    $data = explode($char, $url);
                    $data[0] = trim($data[0], '/')  . $sufix;
                    $url = implode($char, $url);
                } else {
                    $url = trim($url, '/') . $sufix;
                }
            }
        }

        return $url;
    }

    /**
     * Remove slash from the end of URL
     * @param $url
     * @return string
     */
    protected function trimSlash($url)
    {
        if ($this->_getConfig('redirect_to_no_slash')) {
            $url = trim($url, '/');
        }
        return $url;
    }

    /**
     * Retrieve trimmed url without sufix
     * @param  string $identifier
     * @param  string $sufix
     * @return string
     */
    public function trimSufix($identifier, $sufix)
    {
        if ($sufix) {
            $p = mb_strrpos($identifier, $sufix);
            if (false !== $p) {
                $li = mb_strlen($identifier);
                $ls = mb_strlen($sufix);
                if ($p + $ls == $li) {
                    $identifier = mb_substr($identifier, 0, $p);
                }
            }
        }

        return $identifier;
    }

    /**
     * Retrieve post url sufix
     * @return string
     */
    public function getUrlSufix($controllerName)
    {
        return trim($this->_getConfig($controllerName . '_sufix'));
    }

    /**
     * Retrieve media url
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        return $this->_storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $file;
    }

    /**
     * Retrieve blog permalink config value
     * @param  string $key
     * @return string || null || int
     */
    protected function _getConfig($key)
    {
        return $this->_scopeConfig->getValue(
            'vesblog/permalink/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeId
        );
    }
}
