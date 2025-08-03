# Magento RAG Assistant Module

A native Magento 2 module that provides AI-powered customer support through Retrieval-Augmented Generation (RAG). This module integrates with a FastAPI microservice to deliver intelligent, context-aware responses to customer queries.

## Features

- **AI-Powered Customer Support**: Intelligent responses using Google Gemini or OpenAI
- **Semantic Search**: TF-IDF vectorization for accurate document retrieval
- **Real-time Data Integration**: Live product information from Magento database
- **Chat-like Interface**: Modern, responsive design with conversation history
- **Admin Configuration**: Easy setup through Magento admin panel

## Requirements

- **Magento 2.4+** with PHP 8.1+
- **FastAPI RAG Service**: [GitHub Repository](https://github.com/sumeshpch/magento-rag-service)
- **Google Gemini API Key** (or OpenAI API Key)
- **MySQL 8.0+** database

## Installation

### 1. Setup RAG Service

First, set up the FastAPI RAG service:

```bash
# Clone the RAG service repository
git clone https://github.com/sumeshpch/magento-rag-service.git
cd magento-rag-service

# Install dependencies
pip install -r requirements.txt

# Configure environment
cp env.example .env
# Edit .env with your API keys

# Start the service
python main.py
```

### 2. Install Magento Module

```bash
# Navigate to Magento root directory
cd /path/to/magento

# Enable the module
php bin/magento module:enable Magento_RagAssistant

# Run setup commands
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento cache:clean
```

### 3. Configure Module

1. Go to **Admin Panel > Stores > Configuration**
2. Navigate to **General > RAG Assistant**
3. Configure settings:
   - **Enable RAG Assistant**: Set to "Yes"
   - **RAG Service URL**: Enter your FastAPI service URL (e.g., `http://localhost:8000`)
4. Click **Save Config**

## Configuration

### Admin Settings

| Setting | Description | Default |
|---------|-------------|---------|
| Enable RAG Assistant | Enable/disable functionality | Yes |
| RAG Service URL | URL of the FastAPI RAG service | http://localhost:8000 |

### Environment Variables (RAG Service)

```bash
# API Keys
GOOGLE_API_KEY=your_google_api_key_here
OPENAI_API_KEY=your_openai_api_key_here

# Service Configuration
RAG_SERVICE_HOST=0.0.0.0
RAG_SERVICE_PORT=8000

# Database Configuration
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=your_password
DB_NAME=magento
```

## Usage

### Frontend Integration

The module provides a chat-like interface that can be integrated into any Magento page:

- **Chat Container**: Displays conversation history
- **Input Field**: For user questions
- **Submit Button**: Sends questions to the AI service
- **Loading States**: Visual feedback during processing

### API Endpoint

- **POST** `/rag_assistant/index/ask`
  - **Parameters**: `question` (string), `provider` (string, optional)
  - **Response**: JSON with `answer`, `sources`, and `confidence`

### Example Usage

```javascript
$.ajax({
    url: '/rag_assistant/index/ask',
    method: 'POST',
    data: {
        question: 'What is your return policy?',
        provider: 'gemini'
    },
    success: function(response) {
        console.log('Answer:', response.answer);
    }
});
```

## Testing

### Test RAG Service

```bash
# Test the API directly
curl -X POST http://localhost:8000/query \
  -H "Content-Type: application/json" \
  -d '{"question":"What is your return policy?","provider":"gemini"}'
```

### Test Magento Module

1. Visit your Magento store
2. Look for the RAG Assistant chat interface
3. Ask questions and verify responses

## Troubleshooting

### Common Issues

1. **Module not loading**:
   ```bash
   php bin/magento module:status Magento_RagAssistant
   php bin/magento setup:upgrade
   php bin/magento cache:clean
   ```

2. **RAG service connection failed**:
   - Check if FastAPI service is running
   - Verify RAG Service URL in admin configuration
   - Check firewall settings

3. **AI responses not working**:
   - Verify API keys are configured
   - Check service logs for errors
   - Ensure data files exist

### Health Check

```bash
curl http://localhost:8000/health
```

## Security

- **Input Validation**: Question length limited to 1000 characters
- **HTTPS Enforcement**: Required in production
- **API Key Management**: Stored securely in environment variables
- **Error Handling**: Graceful degradation when services unavailable

## Support

- **Documentation**: Check this README and inline code comments
- **Issues**: Report bugs through the GitHub repository
- **RAG Service**: [GitHub Repository](https://github.com/sumeshpch/magento-rag-service)

---

**Version**: 1.0.0  
**Compatibility**: Magento 2.4+  
**PHP Version**: 8.1+  
**License**: MIT 