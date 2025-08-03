<?php
/**
 * Copyright © Sumesh. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\RagAssistant\Block;

use Magento\Framework\View\Element\Template;

class RagForm extends Template
{
    /**
     * Get the form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('rag-assistant/index/ask');
    }
} 