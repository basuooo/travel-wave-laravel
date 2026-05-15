@php
    $chatbotSettings = $siteSettings;
    $chatbotEnabled = $chatbotSettings?->shouldRenderChatbot() ?? false;
    $chatbotLocale = in_array(app()->getLocale(), ['ar', 'en'], true) ? app()->getLocale() : ($chatbotSettings?->chatbot_primary_language ?: 'ar');
    $chatbotIsRtl = $chatbotLocale === 'ar';
    $chatbotSuggested = $chatbotSettings?->chatbotSuggestedQuestions() ?? [];
    $botName = $chatbotSettings?->chatbotBotName() ?: 'AI Assistant';
@endphp

@if($chatbotEnabled)
    <style>
        :root {
            --cb-primary: #6366f1;
            --cb-bg: rgba(255, 255, 255, 0.85);
            --cb-text: #1e293b;
            --cb-bot-bubble: #f1f5f9;
            --cb-user-bubble: #6366f1;
            --cb-user-text: #ffffff;
            --cb-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .tw-chatbot {
            position: fixed;
            bottom: 25px;
            {{ $chatbotIsRtl ? 'left: 25px;' : 'right: 25px;' }}
            z-index: 9999;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Toggle Button */
        .tw-chatbot-toggle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--cb-primary), #4f46e5);
            box-shadow: var(--cb-shadow);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .tw-chatbot-toggle:hover {
            transform: scale(1.1) rotate(5deg);
        }

        .tw-chatbot-toggle svg {
            width: 30px;
            height: 30px;
        }

        /* Panel */
        .tw-chatbot-panel {
            position: absolute;
            bottom: 80px;
            {{ $chatbotIsRtl ? 'left: 0;' : 'right: 0;' }}
            width: 380px;
            max-width: 90vw;
            height: 580px;
            max-height: 80vh;
            background: var(--cb-bg);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: var(--cb-shadow);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform-origin: bottom {{ $chatbotIsRtl ? 'left' : 'right' }};
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            visibility: hidden;
            transform: scale(0.8) translateY(20px);
        }

        .tw-chatbot.is-open .tw-chatbot-panel {
            opacity: 1;
            visibility: visible;
            transform: scale(1) translateY(0);
        }

        /* Header */
        .tw-chatbot-header {
            padding: 20px;
            background: linear-gradient(135deg, #1e293b, #334155);
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tw-chatbot-header-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .tw-chatbot-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .tw-chatbot-header h3 {
            font-size: 1rem;
            margin: 0;
            font-weight: 600;
        }

        .tw-chatbot-header p {
            font-size: 0.75rem;
            margin: 0;
            opacity: 0.8;
        }

        /* Messages Area */
        .tw-chatbot-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
            scroll-behavior: smooth;
        }

        .tw-chatbot-message {
            max-width: 85%;
            display: flex;
            flex-direction: column;
        }

        .tw-chatbot-message--bot {
            align-self: flex-start;
        }

        .tw-chatbot-message--user {
            align-self: flex-end;
        }

        .tw-chatbot-bubble {
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 0.9rem;
            line-height: 1.5;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .tw-chatbot-message--bot .tw-chatbot-bubble {
            background: var(--cb-bot-bubble);
            color: var(--cb-text);
            border-bottom-{{ $chatbotIsRtl ? 'right' : 'left' }}-radius: 4px;
        }

        .tw-chatbot-message--user .tw-chatbot-bubble {
            background: var(--cb-user-bubble);
            color: var(--cb-user-text);
            border-bottom-{{ $chatbotIsRtl ? 'left' : 'right' }}-radius: 4px;
        }

        /* Suggestions */
        .tw-chatbot-suggestions {
            padding: 10px 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .tw-chatbot-suggestion {
            background: white;
            border: 1px solid #e2e8f0;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--cb-primary);
            font-weight: 500;
        }

        .tw-chatbot-suggestion:hover {
            background: var(--cb-primary);
            color: white;
            border-color: var(--cb-primary);
        }

        /* Input Form */
        .tw-chatbot-footer {
            padding: 15px 20px;
            background: white;
            border-top: 1px solid #f1f5f9;
        }

        .tw-chatbot-form {
            display: flex;
            gap: 10px;
        }

        .tw-chatbot-input {
            flex: 1;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            border-radius: 12px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .tw-chatbot-input:focus {
            border-color: var(--cb-primary);
        }

        .tw-chatbot-send {
            background: var(--cb-primary);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .tw-chatbot-send:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Typing Animation */
        .tw-typing-dots {
            display: flex;
            gap: 4px;
            padding: 4px 0;
        }

        .tw-typing-dots span {
            width: 6px;
            height: 6px;
            background: #94a3b8;
            border-radius: 50%;
            animation: tw-typing 1.4s infinite ease-in-out;
        }

        .tw-typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .tw-typing-dots span:nth-child(3) { animation-delay: 0.4s; }

        @keyframes tw-typing {
            0%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-6px); }
        }

        /* Handoff Links */
        .tw-chatbot-handoff {
            margin-top: 10px;
            padding: 10px;
            background: rgba(99, 102, 241, 0.05);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .tw-chatbot-handoff-link {
            display: block;
            padding: 8px;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: var(--cb-primary);
            font-size: 0.85rem;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            text-align: center;
        }

        .tw-chatbot-handoff-link:hover {
            background: #f8fafc;
        }
    </style>

    <div class="tw-chatbot" data-chatbot dir="{{ $chatbotIsRtl ? 'rtl' : 'ltr' }}">
        <button class="tw-chatbot-toggle" data-chatbot-toggle>
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 3C7.03 3 3 6.69 3 11.25c0 2.03.8 3.89 2.13 5.31L4 21l4.91-1.52A9.8 9.8 0 0 0 12 19.5c4.97 0 9-3.69 9-8.25S16.97 3 12 3Z" fill="currentColor"/>
            </svg>
        </button>

        <div class="tw-chatbot-panel" data-chatbot-panel>
            <header class="tw-chatbot-header">
                <div class="tw-chatbot-header-info">
                    <div class="tw-chatbot-avatar">🤖</div>
                    <div>
                        <h3>{{ $botName }}</h3>
                        <p>{{ __('admin.chatbot_status') }}: {{ __('admin.active') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" style="font-size: 0.7rem;" data-chatbot-close></button>
            </header>

            <div class="tw-chatbot-messages" data-chatbot-messages>
                <div class="tw-chatbot-message tw-chatbot-message--bot">
                    <div class="tw-chatbot-bubble">{{ $chatbotSettings?->chatbotWelcomeMessage() }}</div>
                </div>
            </div>

            @if(!empty($chatbotSuggested))
                <div class="tw-chatbot-suggestions">
                    @foreach($chatbotSuggested as $question)
                        <button class="tw-chatbot-suggestion" data-chatbot-question="{{ $question }}">{{ $question }}</button>
                    @endforeach
                </div>
            @endif

            <div class="tw-chatbot-footer">
                <form class="tw-chatbot-form" data-chatbot-form>
                    <input type="text" class="tw-chatbot-input" data-chatbot-input placeholder="{{ __('ui.chatbot_placeholder') }}" autocomplete="off">
                    <button type="submit" class="tw-chatbot-send" data-chatbot-send>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px;"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.querySelector('[data-chatbot]');
            const toggle = root.querySelector('[data-chatbot-toggle]');
            const panel = root.querySelector('[data-chatbot-panel]');
            const close = root.querySelector('[data-chatbot-close]');
            const form = root.querySelector('[data-chatbot-form]');
            const input = root.querySelector('[data-chatbot-input]');
            const messages = root.querySelector('[data-chatbot-messages]');
            const endpoint = "{{ route('chatbot.ask') }}";
            const csrf = "{{ csrf_token() }}";

            const appendMessage = (text, role, isTyping = false) => {
                const wrap = document.createElement('div');
                wrap.className = `tw-chatbot-message tw-chatbot-message--${role}`;
                
                const bubble = document.createElement('div');
                bubble.className = 'tw-chatbot-bubble';
                
                if (isTyping) {
                    bubble.innerHTML = `<div class="tw-typing-dots"><span></span><span></span><span></span></div>`;
                } else {
                    bubble.innerHTML = text.replace(/\n/g, '<br>');
                }

                wrap.appendChild(bubble);
                messages.appendChild(wrap);
                messages.scrollTop = messages.scrollHeight;
                return wrap;
            };

            const toggleChat = () => {
                root.classList.toggle('is-open');
                if (root.classList.contains('is-open')) input.focus();
            };

            toggle.addEventListener('click', toggleChat);
            close.addEventListener('click', () => root.classList.remove('is-open'));

            root.querySelectorAll('[data-chatbot-question]').forEach(btn => {
                btn.addEventListener('click', () => {
                    input.value = btn.dataset.chatbotQuestion;
                    form.dispatchEvent(new Event('submit'));
                });
            });

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                const question = input.value.trim();
                if (!question) return;

                appendMessage(question, 'user');
                input.value = '';
                input.disabled = true;

                // Show typing indicator
                const typingMsg = appendMessage('', 'bot', true);

                try {
                    const res = await fetch(endpoint, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf},
                        body: JSON.stringify({question, locale: "{{ app()->getLocale() }}"})
                    });
                    
                    const data = await res.json();
                    
                    // Human delay simulation on frontend (min 800ms)
                    await new Promise(r => setTimeout(r, 800));
                    
                    typingMsg.remove();
                    
                    if (data.answer) {
                        const botMsg = appendMessage(data.answer, 'bot');
                        
                        // Handoff links
                        if (data.handoff?.whatsapp_url || (data.sources && data.sources.length)) {
                            const handoffWrap = document.createElement('div');
                            handoffWrap.className = 'tw-chatbot-handoff';
                            
                            if (data.handoff?.whatsapp_url) {
                                const waLink = document.createElement('a');
                                waLink.href = data.handoff.whatsapp_url;
                                waLink.target = '_blank';
                                waLink.className = 'tw-chatbot-handoff-link';
                                waLink.textContent = "💬 {{ __('ui.chatbot_whatsapp_handoff') }}";
                                handoffWrap.appendChild(waLink);
                            }
                            
                            botMsg.appendChild(handoffWrap);
                        }
                    }
                } catch (err) {
                    typingMsg.remove();
                    appendMessage("{{ __('ui.chatbot_error') }}", 'bot');
                } finally {
                    input.disabled = false;
                    input.focus();
                    messages.scrollTop = messages.scrollHeight;
                }
            });
        });
    </script>
@endif
