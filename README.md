# Magento RAG Assistant Module

A native Magento 2 module that provides AI-powered customer support through Retrieval-Augmented Generation (RAG). This module integrates with a FastAPI microservice to deliver intelligent, context-aware responses to customer queries.

## 🚀 Features

- **AI-Powered Customer Support**: Intelligent responses using Google Gemini or OpenAI
- **Semantic Search**: TF-IDF vectorization for accurate document retrieval
- **Real-time Data Integration**: Live product information from Magento database
- **Chat-like Interface**: Modern, responsive design with conversation history
- **Admin Configuration**: Easy setup through Magento admin panel
- **Multi-AI Provider Support**: Google Gemini and OpenAI integration
- **Production Ready**: Security, monitoring, and error handling

## 📋 Requirements

- **Magento 2.4+** with PHP 8.1+
- **FastAPI RAG Service**: Running on localhost:8000 (or configured URL)
- **Google Gemini API Key** (or OpenAI API Key)
- **MySQL 8.0+** database
- **Composer** for dependency management

## 🛠️ Installation

### 1. Copy Module Files

Copy the module to your Magento installation:

```bash
# Copy module to app/code/Magento/RagAssistant/
cp -r app/code/Magento/RagAssistant/ /path/to/magento/app/code/Magento/
```

### 2. Enable Module

```bash
# Navigate to Magento root directory
cd /path/to/magento

# Enable the module
php bin/magento module:enable Magento_RagAssistant

# Run setup upgrade
php bin/magento setup:upgrade

# Compile dependencies
php bin/magento setup:di:compile

# Deploy static content
php bin/magento setup:static-content:deploy -f

# Clear cache
php bin/magento cache:clean
php bin/magento cache:flush
```

### 3. Configure Module

1. Go to **Admin Panel > Stores > Configuration**
2. Navigate to **General > RAG Assistant**
3. Configure the following settings:
   - **Enable RAG Assistant**: Set to "Yes"
   - **RAG Service URL**: Enter your FastAPI service URL (e.g., `http://localhost:8000`)
4. Click **Save Config**

## 🔧 Configuration

### Admin Configuration

The module provides the following configuration options:

| Setting | Description | Default |
|---------|-------------|---------|
| Enable RAG Assistant | Enable/disable the RAG Assistant functionality | Yes |
| RAG Service URL | URL of the FastAPI RAG service | http://localhost:8000 |

### Environment Variables

The FastAPI service requires the following environment variables:

```bash
# API Keys for AI providers
GOOGLE_API_KEY=your_google_api_key_here
OPENAI_API_KEY=your_openai_api_key_here

# RAG Service Configuration
RAG_SERVICE_HOST=0.0.0.0
RAG_SERVICE_PORT=8000

# Database Configuration (for data extraction)
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=your_password
DB_NAME=magento
```

## 📁 Module Structure

```
app/code/Magento/RagAssistant/
├── Controller/
│   └── Index/
│       └── Ask.php              # AJAX endpoint handler
├── view/frontend/
│   ├── templates/
│   │   └── form.phtml          # Chat interface template
│   ├── web/
│   │   ├── js/
│   │   │   └── rag-assistant.js # Frontend JavaScript
│   │   └── css/
│   │       └── rag-assistant.css # Chat styling
│   └── layout/
│       └── default.xml         # Layout configuration
├── etc/
│   ├── adminhtml/
│   │   └── system.xml         # Admin configuration
│   ├── default.xml            # Default settings
│   ├── di.xml                # Dependency injection
│   └── module.xml            # Module definition
├── registration.php           # Module registration
└── README.md                 # This file
```

## 🎯 Usage

### Frontend Integration

The RAG Assistant provides a chat-like interface that can be integrated into any Magento page. The interface includes:

- **Chat Container**: Displays conversation history
- **Input Field**: For user questions
- **Submit Button**: Sends questions to the AI service
- **Loading States**: Visual feedback during processing
- **Error Handling**: User-friendly error messages

### API Endpoints

The module exposes the following endpoint:

- **POST** `/rag_assistant/index/ask`
  - **Parameters**: `question` (string), `provider` (string, optional)
  - **Response**: JSON with `answer`, `sources`, and `confidence`

### Example Usage

```javascript
// Make a request to the RAG Assistant
$.ajax({
    url: '/rag_assistant/index/ask',
    method: 'POST',
    data: {
        question: 'What is your return policy?',
        provider: 'gemini'
    },
    success: function(response) {
        console.log('Answer:', response.answer);
        console.log('Sources:', response.sources);
        console.log('Confidence:', response.confidence);
    }
});
```

## 🔍 Testing

### Integration Tests

Run the module's integration tests:

```bash
# Run all RAG Assistant tests
vendor/bin/phpunit dev/tests/integration/testsuite/Magento/RagAssistant/

# Run specific test
vendor/bin/phpunit dev/tests/integration/testsuite/Magento/RagAssistant/Controller/Index/AskTest.php
```

### Manual Testing

1. **Start the RAG service**:
   ```bash
   cd rag_service
   python main.py
   ```

2. **Test the API directly**:
   ```bash
   curl -X POST http://localhost:8000/query \
     -H "Content-Type: application/json" \
     -d '{"question":"What is your return policy?","provider":"gemini"}'
   ```

3. **Test through Magento**:
   - Visit your Magento store
   - Look for the RAG Assistant chat interface
   - Ask questions and verify responses

## 🚀 Performance Optimization

### Caching

The module uses Magento's caching system for:
- Configuration values
- API responses (when appropriate)
- Static content

### Database Optimization

- **Indexed Queries**: Product data extraction uses optimized SQL
- **Connection Pooling**: Efficient database connectivity
- **Batch Processing**: Large datasets are processed in chunks

### Frontend Optimization

- **Minified Assets**: CSS and JavaScript are minified in production
- **Lazy Loading**: Chat interface loads on demand
- **Responsive Design**: Optimized for mobile and desktop

## 🔒 Security

### Input Validation

- **Question Length**: Limited to 1000 characters
- **Content Sanitization**: XSS protection
- **Rate Limiting**: Prevents abuse (configurable)

### HTTPS Enforcement

```php
// Ensure HTTPS in production
if (!$this->getRequest()->isSecure()) {
    return $result->setData(['error' => 'HTTPS required']);
}
```

### API Key Management

- **Environment Variables**: API keys stored securely
- **Configuration Validation**: Service checks at startup
- **Error Handling**: Graceful degradation when services unavailable

## 🐛 Troubleshooting

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
   - Ensure data files exist (`magento_dump.json`, `magento_products.json`)

4. **Frontend not displaying**:
   ```bash
   php bin/magento setup:static-content:deploy -f
   php bin/magento cache:clean
   ```

### Debug Mode

Enable debug logging:

```php
// In app/code/Magento/RagAssistant/Controller/Index/Ask.php
// Add logging for debugging
$this->_logger->info('RAG Assistant query: ' . $question);
```

### Health Check

Check service health:

```bash
curl http://localhost:8000/health
```

Expected response:
```json
{
  "status": "healthy",
  "documents_loaded": 150,
  "tfidf_ready": true,
  "google_api_configured": true,
  "openai_api_configured": false
}
```

## 📈 Monitoring

### Key Metrics

- **Response Time**: Average query processing time
- **Success Rate**: Percentage of successful responses
- **Error Rate**: Failed requests and error types
- **Usage Patterns**: Most common questions and topics

### Logging

The module logs:
- **API Requests**: All queries and responses
- **Errors**: Service failures and exceptions
- **Performance**: Response times and resource usage
- **Security**: Failed authentication attempts

## 🔄 Updates and Maintenance

### Updating the Module

1. **Backup current installation**:
   ```bash
   cp -r app/code/Magento/RagAssistant/ backup/
   ```

2. **Update module files**:
   ```bash
   # Copy new version
   cp -r new_version/app/code/Magento/RagAssistant/ app/code/Magento/
   ```

3. **Run setup commands**:
   ```bash
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento setup:static-content:deploy -f
   php bin/magento cache:clean
   ```

### Data Updates

To update product data:

```bash
cd rag_service
python generate_magento_product_json.py
```

This will regenerate the `magento_products.json` file with current product information.

## 🤝 Contributing

### Development Setup

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/new-feature`
3. **Make your changes**
4. **Run tests**: `vendor/bin/phpunit dev/tests/integration/testsuite/Magento/RagAssistant/`
5. **Submit a pull request**

### Code Standards

- Follow **PSR-12** coding standards
- Add **PHPDoc** comments for all methods
- Write **unit tests** for new functionality
- Update **documentation** for any changes

## 📄 License

This module is licensed under the [MIT License](LICENSE).

## 🆘 Support

### Getting Help

- **Documentation**: Check this README and inline code comments
- **Issues**: Report bugs and feature requests through the issue tracker
- **Community**: Join our developer community for discussions

### Contact

- **Email**: support@yourcompany.com
- **GitHub**: [Repository Issues](https://github.com/yourcompany/magento-rag-assistant/issues)
- **Documentation**: [Full Documentation](https://docs.yourcompany.com/rag-assistant)

## 🎯 Roadmap

### Planned Features

- [ ] **Multi-language Support**: Internationalization and localization
- [ ] **Advanced Analytics**: Query analytics and insights dashboard
- [ ] **Voice Integration**: Speech-to-text and text-to-speech
- [ ] **CRM Integration**: Customer data synchronization
- [ ] **Mobile App**: Native mobile application
- [ ] **Advanced AI Models**: Support for more AI providers

### Technical Improvements

- [ ] **Vector Database**: Migration to Pinecone or Weaviate
- [ ] **Real-time Updates**: WebSocket support for live responses
- [ ] **A/B Testing**: Framework for response optimization
- [ ] **Machine Learning**: Continuous model training pipeline

---

**Version**: 1.0.0  
**Last Updated**: 2024  
**Compatibility**: Magento 2.4+  
**PHP Version**: 8.1+  
**License**: MIT 