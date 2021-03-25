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
namespace Ves\Blog\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;

class ImporterActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'vesblog/importer/edit';
    const URL_PATH_DELETE = 'vesblog/importer/delete';
    const URL_PATH_IMPORT = 'vesblog/importer/runImport';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param ContextInterface
     * @param UiComponentFactory
     * @param UrlInterface
     * @param array
     * @param array
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        \Ves\Blog\Helper\Data $dataHelper,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->dataHelper       = $dataHelper;
    }

    /**
     * @param array $items
     * @return array
     */
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $item[$name] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'importer_id' => $item['entity_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'entity_id' => $item['entity_id']
                                ]
                            ),
                            'label' => __('Delete')
                        ],
                        'import' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_IMPORT,
                                [
                                    'importer_id' => $item['entity_id']
                                ]
                            ),
                            'label' => __('Run Import')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}