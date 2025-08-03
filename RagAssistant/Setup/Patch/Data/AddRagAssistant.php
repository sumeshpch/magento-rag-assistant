<?php
/**
 * Copyright © Sumesh. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\RagAssistant\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Cms\Model\PageFactory;

class AddRagAssistant implements DataPatchInterface
{
    /**
     * @param PageFactory $pageFactory
     */
    public function __construct(
        private readonly PageFactory $pageFactory
    ) {
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $cmsPageData = [
            'title' => 'RAG Assistant', // cms page title
            'page_layout' => '1column', // cms page layout
            'meta_keywords' => 'RAG Assistant', // cms page meta keywords
            'meta_description' => 'RAG Assistant', // cms page description
            'identifier' => 'rag-assistant', // cms page url identifier
            'content_heading' => 'RAG Assistant', // Page heading
            'content' => '{{block class="Magento\RagAssistant\Block\RagForm" template="Magento_RagAssistant::form.phtml"}}', // page content
            'is_active' => 1, // define active status
            'stores' => [0], // assign to stores
            'sort_order' => 0 // page sort order
        ];
        // create page
        $this->pageFactory->create()->setData($cmsPageData)->save();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
