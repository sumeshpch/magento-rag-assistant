<?php
/**
 * Copyright © Sumesh. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\RagAssistant\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Ask implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * RAG Service configuration
     */
    private const RAG_SERVICE_TIMEOUT = 30;

    /**
     * @param JsonFactory $jsonFactory
     * @param RequestInterface $request
     * @param Curl $curl
     * @param Json $json
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly RequestInterface $request,
        private readonly Curl $curl,
        private readonly Json $json,
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Handle RAG assistant requests
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $result = $this->jsonFactory->create();
        
        try {
            // Check if RAG Assistant is enabled
            $isEnabled = $this->scopeConfig->getValue(
                'rag_assistant/general/enabled',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            
            if (!$isEnabled) {
                return $result->setData([
                    'success' => false,
                    'message' => 'RAG Assistant is currently disabled. Please contact the administrator.'
                ]);
            }
            
            $question = $this->request->getParam('question', '');
            $provider = $this->request->getParam('provider', 'gemini');
            
            if (empty($question)) {
                return $result->setData([
                    'success' => false,
                    'message' => 'Question is required'
                ]);
            }

            // Call RAG service
            $ragResponse = $this->callRagService($question, $provider);

            if ($ragResponse['success']) {
                return $result->setData([
                    'success' => true,
                    'response' => $ragResponse['answer'],
                    'sources' => $ragResponse['sources'],
                    'confidence' => $ragResponse['confidence']
                ]);
            } else {
                return $result->setData([
                    'success' => false,
                    'message' => $ragResponse['error'] ?? 'Failed to get response from RAG service'
                ]);
            }

        } catch (\Exception $e) {
            return $result->setData([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Call the RAG service via HTTP
     *
     * @param string $question
     * @param string $provider
     * @return array
     */
    private function callRagService(string $question, string $provider): array
    {
        try {
            // Get RAG service URL from configuration
            $ragServiceUrl = $this->scopeConfig->getValue(
                'rag_assistant/general/rag_service_url',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            
            if (empty($ragServiceUrl)) {
                return [
                    'success' => false,
                    'error' => 'RAG Service URL is not configured. Please configure it in Admin > Stores > Configuration > RAG Assistant.'
                ];
            }
            
            // Ensure URL ends with /query
            $ragServiceUrl = rtrim($ragServiceUrl, '/') . '/query';
            
            // Prepare request data
            $requestData = [
                'question' => $question,
                'provider' => $provider
            ];

            // Set cURL options
            $this->curl->setOption(CURLOPT_URL, $ragServiceUrl);
            $this->curl->setOption(CURLOPT_POST, true);
            $this->curl->setOption(CURLOPT_POSTFIELDS, $this->json->serialize($requestData));
            $this->curl->setOption(CURLOPT_TIMEOUT, self::RAG_SERVICE_TIMEOUT);
            $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
            $this->curl->setOption(CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);

            // Make the request
            $this->curl->post($ragServiceUrl, $this->json->serialize($requestData));
            
            $responseCode = $this->curl->getStatus();
            $responseBody = $this->curl->getBody();

            if ($responseCode === 200) {
                $responseData = $this->json->unserialize($responseBody);
                return [
                    'success' => true,
                    'answer' => $responseData['answer'] ?? 'No answer received',
                    'sources' => $responseData['sources'] ?? [],
                    'confidence' => $responseData['confidence'] ?? 0.0
                ];
            } else {
                return [
                    'success' => false,
                    'error' => "HTTP Error {$responseCode}: {$responseBody}"
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to connect to RAG service: ' . $e->getMessage()
            ];
        }
    }
} 