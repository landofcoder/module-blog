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
namespace Ves\Blog\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Image extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_filesystem;

    protected $_processor;
    protected $_imageFactory;
    protected $_storeManager;

    protected $_keepAspectRatio = true;

    protected $_keepFrame = true;

    protected $_keepTransparency = true;

    protected $_constrainOnly = true;

    protected $_backgroundColor = [255, 255, 255];
    /** \Magento\Catalog\Helper\Image */
    protected $_imageHelper;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\Filesystem
     * @param \Magento\Framework\App\Helper\Context
     * @param \Magento\Framework\Image\Factory
     * @param \Magento\Catalog\Helper\Image
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Catalog\Helper\Image $imageHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_filesystem = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_imageHelper = $imageHelper;

        parent::__construct($context);
    }

    public function getBaseMediaDirPath() {
        return $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
    }

    public function getBaseMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * @param bool $keep
     * @return $this
     */
    public function setKeepAspectRatio($keep)
    {
        $this->_keepAspectRatio = (bool)$keep;
        return $this;
    }

    /**
     * @param bool $keep
     * @return $this
     */
    public function setKeepFrame($keep)
    {
        $this->_keepFrame = (bool)$keep;
        return $this;
    }

    /**
     * @param bool $keep
     * @return $this
     */
    public function setKeepTransparency($keep)
    {
        $this->_keepTransparency = (bool)$keep;
        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setConstrainOnly($flag)
    {
        $this->_constrainOnly = (bool)$flag;
        return $this;
    }

    /**
     * @param int[] $rgbArray
     * @return $this
     */
    public function setBackgroundColor(array $rgbArray)
    {
        $this->_backgroundColor = $rgbArray;
        return $this;
    }
    /**
     * @return MagentoImage
     */
    public function getImageProcessor($_imageUrl = null, $qualtity = 100, $keep_ratio = true, $keep_frame = false)
    {
        $this->_processor = $this->_imageFactory->create($_imageUrl);
        $this->_processor->keepAspectRatio($keep_ratio);
        $this->_processor->keepFrame($keep_frame);
        $this->_processor->keepTransparency($this->_keepTransparency);
        $this->_processor->constrainOnly($this->_constrainOnly);
        $this->_processor->backgroundColor($this->_backgroundColor);
        $this->_processor->quality($qualtity);

        return $this->_processor;
    }

    public function resizeImage($image, $width = 100, $height = 0, $qualtity = 100, $keep_ratio = true, $keep_frame = false)
    {
        if($image=='') return;
        $tmp_image = $image;
        $media_base_url = $this->getBaseMediaUrl();
        $image = str_replace($media_base_url, "", $image);
        $media_base_url = str_replace("https://","http://", $media_base_url);
        $image = str_replace($media_base_url, "", $image);
        $parsed = parse_url($image);
        if (!empty($parsed['scheme'])) { //return external image link
            return $tmp_image;
        }
        $_imageUrl = $this->getBaseMediaDirPath().DIRECTORY_SEPARATOR.$image;
        $_imageResized = $this->getBaseMediaDirPath().DIRECTORY_SEPARATOR."resized".DIRECTORY_SEPARATOR.(int)$width."x".(int)$height.DIRECTORY_SEPARATOR.$image;
        if (!file_exists($_imageResized)&&file_exists($_imageUrl)){
            try{
                $imageObj = $this->getImageProcessor($_imageUrl, $qualtity, $keep_ratio, $keep_frame);
                if($height == 0){
                    $imageObj->resize($width);
                }else{
                    $imageObj->resize($width, $height);
                }
                $imageObj->save($_imageResized);
            } catch(\Exception $e) {

            }
        }
        if(file_exists($_imageResized)){
            return $this->getBaseMediaUrl()."resized/".(int)$width."x".(int)$height."/".$image;
        } else {
            return $this->getBaseMediaUrl().$image;
        }

    }

    /**
     * Get image URL of the given product
     *
     * @param \Magento\Catalog\Model\Product    $product        Product
     * @param int                               $w              Image width
     * @param int                               $h              Image height
     * @param string                            $imgVersion     Image version: image, small_image, thumbnail
     * @param mixed                             $file           Specific file
     * @return string
     */
    public function getImg($product, $w = 300, $h = 300, $imgVersion='image', $file=NULL)
    {
        if ($h <= 0){
            $image = $this->_imageHelper
            ->init($product, $imgVersion)
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepFrame(false);
            if($file){
                $image->setImageFile($file);
            }
            $image->resize($w);
            return $image;
        }else{
            $image = $this->_imageHelper
            ->init($product, $imgVersion);
            if($file){
                $image->setImageFile($file);
            }
            $image->resize($w, $h);
            return $image;
        }
    }

    /**
     * Get alternative image HTML of the given product
     *
     * @param \Magento\Catalog\Model\Product    $product        Product
     * @param int                               $w              Image width
     * @param int                               $h              Image height
     * @param string                            $imgVersion     Image version: image, small_image, thumbnail
     * @return string
     */
    public function getAltImgHtml($product, $w, $h, $imgVersion='small_image', $column = 'position', $value = 1)
    {
        $product->load('media_gallery');
        if ($images = $product->getMediaGalleryImages())
        {
            $image = $images->getItemByColumnValue($column, $value);
            if(isset($image) && $image->getUrl()){
                $imgAlt = $this->getImg($product, $w, $h, $imgVersion , $image->getFile());
                if(!$imgAlt) return '';
                return $imgAlt;
            }else{
                return '';
            }
        }
        return '';
    }

}
