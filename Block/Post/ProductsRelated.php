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
namespace Ves\Blog\Block\Post;

class ProductsRelated extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_blogHelper;

    /**
     * @var \Ves\Blog\Model\Post
     */
    protected $_postFactory;
    protected $_postsBlock;
    protected $_collection;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Ves\Blog\Model\Post
     * @param \Ves\Blog\Helper\Data
     * @param array
     */
    public function __construct(
    	\Magento\Catalog\Block\Product\Context $context,
    	\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    	\Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
    	\Ves\Blog\Helper\Data $blogHelper,
    	array $data = []
    ) {
		$this->_blogHelper              = $blogHelper;
		$this->productCollectionFactory = $productCollectionFactory;
		$this->catalogProductVisibility = $catalogProductVisibility;
    	parent::__construct($context, $data);
    }

	/**
     * Set brand collection
     * @param \Ves\Blog\Model\Post
     */
    public function setCollection($collection)
    {
    	$this->_collection = $collection;
    	return $this->_collection;
    }

    public function getCollection(){
    	return $this->_collection;
    }

    public function _toHtml(){
    	$post = $this->getPost();
        if(!$this->_blogHelper->getConfig('general_settings/enable') || !$post->getIsActive()) return;
        if(!$this->_blogHelper->getConfig('post_page/enable_related_products')) return;

    	$productsIds = $post->getProducts();
    	$collection = $this->productCollectionFactory->create();
    	$collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
        	->addAttributeToFilter('entity_id', ['in' => $productsIds])
            ->setCurPage(1);
        $this->setCollection($collection);
    	return parent::_toHtml();
    }

    public function getPost(){
        $post = $this->_coreRegistry->registry('current_post');
        return $post;
    }

    /**
     * Return HTML block with price
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
	public function getVesProductPriceHtml(
		\Magento\Catalog\Model\Product $product,
		$priceType = null,
		$renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
		array $arguments = []
		) {
		if (!isset($arguments['zone'])) {
			$arguments['zone'] = $renderZone;
		}
		$arguments['price_id'] = isset($arguments['price_id'])
		? $arguments['price_id']
		: 'old-price-' . $product->getId() . '-' . $priceType;
		$arguments['include_container'] = isset($arguments['include_container'])
		? $arguments['include_container']
		: true;
		$arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
		? $arguments['display_minimal_price']
		: true;
		$priceRender = $this->getLayout()->getBlock('product.price.render.default');

		$price = '';
		if ($priceRender) {
			$price = $priceRender->render(
				\Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
				$product,
				$arguments
				);
		}
		return $price;
	}
}
