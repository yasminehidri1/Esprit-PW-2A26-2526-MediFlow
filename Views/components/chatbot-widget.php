<?php
/**
 * Floating Chatbot Widget
 * Include this in your layout footer to add chatbot to all pages
 */
?>

<style>
/* ═══════════════════════════════════════════ */
/* CHATBOT WIDGET STYLES */
/* ═══════════════════════════════════════════ */

.chatbot-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

/* Chat bubble button */
.chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #004d99 0%, #1565c0 100%);
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0, 77, 153, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    transition: all 0.3s ease;
    overflow: hidden;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(0, 77, 153, 0.6);
}

.chatbot-toggle.open {
    display: none;
}

/* Chat window */
.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    height: 550px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    opacity: 0;
    transform: translateY(20px);
    pointer-events: none;
    transition: all 0.3s ease;
}

.chatbot-window.open {
    opacity: 1;
    transform: translateY(0);
    pointer-events: all;
}

/* Header */
.chatbot-header {
    background: linear-gradient(135deg, #004d99 0%, #1565c0 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.chatbot-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.chatbot-close {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    cursor: pointer;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.chatbot-close:hover {
    background: rgba(255,255,255,0.3);
}

/* Messages area */
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.chatbot-message {
    display: flex;
    gap: 8px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chatbot-message.user {
    justify-content: flex-end;
}

.chatbot-message.bot {
    justify-content: flex-start;
}

.chatbot-bubble {
    max-width: 80%;
    padding: 12px 16px;
    border-radius: 12px;
    line-height: 1.4;
    font-size: 14px;
}

.chatbot-message.user .chatbot-bubble {
    background: #004d99;
    color: white;
    border-bottom-right-radius: 4px;
}

.chatbot-message.bot .chatbot-bubble {
    background: white;
    color: #111827;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}

/* Typing indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 12px 16px;
}

.typing-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #9ca3af;
    animation: typing 1.4s infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        opacity: 0.5;
        transform: translateY(0);
    }
    30% {
        opacity: 1;
        transform: translateY(-10px);
    }
}

/* Input area */
.chatbot-input-area {
    padding: 16px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 8px;
    background: white;
    flex-shrink: 0;
}

.chatbot-input {
    flex: 1;
    padding: 10px 12px;
    border: 1.5px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    outline: none;
    transition: all 0.2s ease;
    resize: none;
    max-height: 80px;
}

.chatbot-input:focus {
    border-color: #004d99;
    box-shadow: 0 0 0 3px rgba(0, 77, 153, 0.1);
}

.chatbot-send {
    background: #004d99;
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    font-size: 18px;
}

.chatbot-send:hover:not(:disabled) {
    background: #003d7a;
    transform: scale(1.05);
}

.chatbot-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Responsive */
@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 40px);
        height: calc(100vh - 100px);
        bottom: 80px;
        right: 20px;
    }
    
    .chatbot-bubble {
        max-width: 85%;
    }
}
</style>

<div class="chatbot-container">
    <!-- Toggle button -->
    <button class="chatbot-toggle" id="chatbotToggle" title="Open Chat">
        <span class="material-symbols-outlined">smart_toy</span>
    </button>
    
    <!-- Chat window -->
    <div class="chatbot-window" id="chatbotWindow">
        <!-- Header -->
        <div class="chatbot-header">
            <h3>MediFlow Assistant</h3>
            <button class="chatbot-close" id="chatbotClose">
                <span class="material-symbols-outlined" style="font-size: 20px;">close</span>
            </button>
        </div>
        
        <!-- Messages -->
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message bot">
                <div class="chatbot-bubble">
                    👋 Hello! I'm your MediFlow Assistant. How can I help you today?
                </div>
            </div>
        </div>
        
        <!-- Input -->
        <div class="chatbot-input-area">
            <input 
                type="text" 
                class="chatbot-input" 
                id="chatbotInput" 
                placeholder="Type your message..."
                maxlength="2000"
            >
            <button class="chatbot-send" id="chatbotSend">
                <span class="material-symbols-outlined">send</span>
            </button>
        </div>
    </div>
</div>

<script>
// ═══════════════════════════════════════════
// CHATBOT WIDGET JAVASCRIPT
// ═══════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('chatbotToggle');
    const window_ = document.getElementById('chatbotWindow');
    const closeBtn = document.getElementById('chatbotClose');
    const input = document.getElementById('chatbotInput');
    const sendBtn = document.getElementById('chatbotSend');
    const messagesDiv = document.getElementById('chatbotMessages');
    
    let isLoading = false;
    
    // Toggle chat window
    toggle.addEventListener('click', () => {
        window_.classList.add('open');
        toggle.classList.add('open');
        input.focus();
    });
    
    closeBtn.addEventListener('click', () => {
        window_.classList.remove('open');
        toggle.classList.remove('open');
    });
    
    // Send message on Enter
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey && !isLoading) {
            sendMessage();
        }
    });
    
    // Send button click
    sendBtn.addEventListener('click', () => {
        if (!isLoading) {
            sendMessage();
        }
    });
    
    // Send message function
    async function sendMessage() {
        const message = input.value.trim();
        if (!message) return;
        
        // Add user message
        addMessage('user', message);
        input.value = '';
        sendBtn.disabled = true;
        isLoading = true;
        
        // Show typing indicator
        showTypingIndicator();
        
        try {
            const response = await fetch('/integration/api/gemini-chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ message: message })
            });
            
            const data = await response.json();
            
            if (data.success) {
                removeTypingIndicator();
                addMessage('bot', data.reply);
            } else {
                removeTypingIndicator();
                addMessage('bot', '❌ ' + (data.error || 'Unable to process your message. Please try again.'));
            }
        } catch (error) {
            removeTypingIndicator();
            console.error('Chat error:', error);
            addMessage('bot', '❌ Connection error. Please check your internet and try again.');
        } finally {
            sendBtn.disabled = false;
            isLoading = false;
            input.focus();
        }
    }
    
    // Add message to chat
    function addMessage(role, text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = 'chatbot-message ' + role;
        
        const bubble = document.createElement('div');
        bubble.className = 'chatbot-bubble';
        bubble.textContent = text;
        
        msgDiv.appendChild(bubble);
        messagesDiv.appendChild(msgDiv);
        
        // Auto-scroll to bottom
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
    
    // Show typing indicator
    function showTypingIndicator() {
        const typing = document.createElement('div');
        typing.className = 'chatbot-message bot';
        typing.id = 'typingIndicator';
        typing.innerHTML = '<div class="typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>';
        messagesDiv.appendChild(typing);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
    
    // Remove typing indicator
    function removeTypingIndicator() {
        const typing = document.getElementById('typingIndicator');
        if (typing) typing.remove();
    }
});
</script>
