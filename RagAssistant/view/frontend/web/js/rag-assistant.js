/**
 * Copyright © Sumesh. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/url'
], function ($, urlBuilder) {
    'use strict';

    return function (config) {
        $(document).ready(function () {
            const $form = $('#rag-assistant-form');
            const $response = $('#rag-response');
            const $responseContent = $('#response-content');
            const $loading = $('#rag-loading');
            const $askButton = $('#ask-button');
            const $questionInput = $('#question');
            const $chatContainer = $('#chat-container');

            $form.on('submit', function (e) {
                e.preventDefault();
                
                const question = $questionInput.val().trim();
                
                if (!question) {
                    alert('Please enter a question.');
                    return;
                }

                // Add user question to chat
                addMessageToChat('user', question);

                // Clear the textbox
                $questionInput.val('');

                // Show loading state
                $loading.show();
                $response.hide();
                $askButton.prop('disabled', true);

                // Make AJAX request
                $.ajax({
                    url: urlBuilder.build('rag-assistant/index/ask'),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        question: question
                    },
                    success: function (response) {
                        $loading.hide();
                        $askButton.prop('disabled', false);

                        if (response.success) {
                            // Add assistant response to chat
                            addMessageToChat('assistant', response.response);
                            
                            // Display confidence score
                            if (response.confidence !== undefined) {
                                const confidencePercent = Math.round(response.confidence * 100);
                                $('#confidence-value').text(confidencePercent + '%');
                                $('#response-meta').show();
                            }
                            
                            // Display sources if available
                            if (response.sources && response.sources.length > 0) {
                                const $sourcesList = $('#sources-list');
                                $sourcesList.empty();
                                
                                response.sources.forEach(function(source) {
                                    const similarityPercent = Math.round(source.similarity * 100);
                                    const listItem = $('<li>').html(
                                        '<a href="' + source.url + '" target="_blank">' + source.title + '</a>' +
                                        '<span class="similarity">(' + similarityPercent + '% match)</span>'
                                    );
                                    $sourcesList.append(listItem);
                                });
                                
                                $('#sources-section').show();
                            }
                            
                            $response.show();
                        } else {
                            addMessageToChat('assistant', 'Error: ' + (response.message || 'Unknown error occurred'));
                        }
                    },
                    error: function (xhr, status, error) {
                        $loading.hide();
                        $askButton.prop('disabled', false);
                        addMessageToChat('assistant', 'Error: ' + error);
                    }
                });
            });

            function addMessageToChat(sender, message) {
                const $chatContainer = $('#chat-container');
                const messageClass = sender === 'user' ? 'user-message' : 'assistant-message';
                const senderName = sender === 'user' ? 'You' : 'Assistant';
                const timestamp = new Date().toLocaleTimeString();
                
                const $messageDiv = $('<div>').addClass('chat-message ' + messageClass);
                const $messageContent = $('<div>').addClass('message-content').html(message);
                const $messageMeta = $('<div>').addClass('message-meta')
                    .html('<span class="sender">' + senderName + '</span>' +
                          '<span class="timestamp">' + timestamp + '</span>');
                
                $messageDiv.append($messageContent).append($messageMeta);
                $chatContainer.append($messageDiv);
                
                // Scroll to bottom
                $chatContainer.scrollTop($chatContainer[0].scrollHeight);
            }
        });
    };
}); 