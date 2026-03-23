@php
    $chatbotSettings = $siteSettings;
    $chatbotEnabled = $chatbotSettings?->shouldRenderChatbot() ?? false;
    $chatbotLocale = in_array(app()->getLocale(), ['ar', 'en'], true) ? app()->getLocale() : ($chatbotSettings?->chatbot_primary_language ?: 'ar');
    $chatbotIsRtl = $chatbotLocale === 'ar';
    $chatbotSuggested = $chatbotSettings?->chatbotSuggestedQuestions() ?? [];
@endphp

@if($chatbotEnabled)
    <div
        class="tw-chatbot"
        dir="{{ $chatbotIsRtl ? 'rtl' : 'ltr' }}"
        data-chatbot
        data-locale="{{ $chatbotLocale }}"
        data-endpoint="{{ route('chatbot.ask') }}"
        data-title="{{ $chatbotSettings?->chatbotBotName() }}"
        data-typing="{{ __('ui.chatbot_typing') }}"
        data-error="{{ __('ui.chatbot_error') }}"
        data-sources-label="{{ __('ui.chatbot_sources') }}"
        data-whatsapp-label="{{ __('ui.chatbot_whatsapp_handoff') }}"
        data-contact-label="{{ __('ui.chatbot_contact_handoff') }}"
    >
        <button type="button" class="tw-chatbot-toggle" data-chatbot-toggle aria-expanded="false" aria-controls="tw-chatbot-panel">
            <span class="tw-chatbot-toggle__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 3C7.03 3 3 6.69 3 11.25c0 2.03.8 3.89 2.13 5.31L4 21l4.91-1.52A9.8 9.8 0 0 0 12 19.5c4.97 0 9-3.69 9-8.25S16.97 3 12 3Zm0 14.25c-.96 0-1.9-.15-2.8-.45l-.48-.16-1.95.6.48-1.75-.33-.35A5.84 5.84 0 0 1 5.25 11.25c0-3.31 3.02-6 6.75-6s6.75 2.69 6.75 6-3.02 6-6.75 6Zm-3.38-5.1h1.56v1.58H8.62v-1.58Zm2.6 0h1.56v1.58h-1.56v-1.58Zm2.6 0h1.56v1.58h-1.56v-1.58Zm-5.2-3.03h6.76v1.41H8.62V9.12Z" fill="currentColor"/>
                </svg>
            </span>
            <span class="tw-chatbot-toggle__text">{{ $chatbotSettings?->chatbotBotName() }}</span>
        </button>

        <section class="tw-chatbot-panel" id="tw-chatbot-panel" data-chatbot-panel hidden>
            <header class="tw-chatbot-panel__header">
                <div>
                    <strong>{{ $chatbotSettings?->chatbotBotName() }}</strong>
                    <p>{{ $chatbotSettings?->chatbotWelcomeMessage() }}</p>
                </div>
                <div class="tw-chatbot-panel__actions">
                    <button type="button" class="tw-chatbot-icon-button" data-chatbot-minimize aria-label="{{ __('ui.chatbot_minimize') }}">−</button>
                    <button type="button" class="tw-chatbot-icon-button" data-chatbot-close aria-label="{{ __('ui.chatbot_close') }}">×</button>
                </div>
            </header>

            @if(!empty($chatbotSuggested))
                <div class="tw-chatbot-suggestions" data-chatbot-suggestions>
                    @foreach($chatbotSuggested as $question)
                        <button type="button" class="tw-chatbot-chip" data-chatbot-question="{{ $question }}">{{ $question }}</button>
                    @endforeach
                </div>
            @endif

            <div class="tw-chatbot-messages" data-chatbot-messages>
                <article class="tw-chatbot-message tw-chatbot-message--bot">
                    <div class="tw-chatbot-bubble">{{ $chatbotSettings?->chatbotWelcomeMessage() }}</div>
                </article>
            </div>

            <div class="tw-chatbot-handoff" data-chatbot-handoff hidden></div>

            <form class="tw-chatbot-form" data-chatbot-form>
                <input type="text" class="form-control" data-chatbot-input placeholder="{{ __('ui.chatbot_placeholder') }}" autocomplete="off">
                <button type="submit" class="btn btn-primary">{{ __('ui.chatbot_send') }}</button>
            </form>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.querySelector('[data-chatbot]');
            if (!root) {
                return;
            }

            const toggle = root.querySelector('[data-chatbot-toggle]');
            const panel = root.querySelector('[data-chatbot-panel]');
            const closeButton = root.querySelector('[data-chatbot-close]');
            const minimizeButton = root.querySelector('[data-chatbot-minimize]');
            const form = root.querySelector('[data-chatbot-form]');
            const input = root.querySelector('[data-chatbot-input]');
            const messages = root.querySelector('[data-chatbot-messages]');
            const handoff = root.querySelector('[data-chatbot-handoff]');
            const locale = root.dataset.locale || 'ar';
            const endpoint = root.dataset.endpoint;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const setOpen = function (isOpen) {
                panel.hidden = !isOpen;
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                root.classList.toggle('is-open', isOpen);

                if (isOpen) {
                    input?.focus();
                }
            };

            const escapeHtml = function (value) {
                const div = document.createElement('div');
                div.textContent = value || '';
                return div.innerHTML;
            };

            const appendMessage = function (text, role) {
                const article = document.createElement('article');
                article.className = 'tw-chatbot-message tw-chatbot-message--' + role;

                const bubble = document.createElement('div');
                bubble.className = 'tw-chatbot-bubble';
                bubble.innerHTML = escapeHtml(text).replace(/\n/g, '<br>');

                article.appendChild(bubble);
                messages.appendChild(article);
                messages.scrollTop = messages.scrollHeight;
                return article;
            };

            const renderHandoff = function (payload) {
                handoff.innerHTML = '';

                const links = [];
                if (payload?.handoff?.whatsapp_url) {
                    links.push({ href: payload.handoff.whatsapp_url, label: root.dataset.whatsappLabel });
                }
                if (payload?.handoff?.contact_url) {
                    links.push({ href: payload.handoff.contact_url, label: root.dataset.contactLabel });
                }

                if (payload?.sources?.length) {
                    const heading = document.createElement('div');
                    heading.className = 'tw-chatbot-handoff__label';
                    heading.textContent = root.dataset.sourcesLabel || '';
                    handoff.appendChild(heading);

                    const sourceWrap = document.createElement('div');
                    sourceWrap.className = 'tw-chatbot-handoff__links';
                    payload.sources.forEach(function (source) {
                        const link = document.createElement('a');
                        link.href = source.url;
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer';
                        link.className = 'tw-chatbot-handoff__link';
                        link.textContent = source.title;
                        sourceWrap.appendChild(link);
                    });
                    handoff.appendChild(sourceWrap);
                }

                if (links.length) {
                    const actionWrap = document.createElement('div');
                    actionWrap.className = 'tw-chatbot-handoff__links';
                    links.forEach(function (item) {
                        const link = document.createElement('a');
                        link.href = item.href;
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer';
                        link.className = 'tw-chatbot-handoff__link tw-chatbot-handoff__link--accent';
                        link.textContent = item.label;
                        actionWrap.appendChild(link);
                    });
                    handoff.appendChild(actionWrap);
                }

                handoff.hidden = handoff.childElementCount === 0;
            };

            toggle?.addEventListener('click', function () {
                setOpen(panel.hidden);
            });

            closeButton?.addEventListener('click', function () {
                setOpen(false);
            });

            minimizeButton?.addEventListener('click', function () {
                setOpen(false);
            });

            root.querySelectorAll('[data-chatbot-question]').forEach(function (button) {
                button.addEventListener('click', function () {
                    input.value = button.dataset.chatbotQuestion || '';
                    form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                });
            });

            form?.addEventListener('submit', async function (event) {
                event.preventDefault();

                const question = input.value.trim();
                if (!question || !endpoint) {
                    return;
                }

                setOpen(true);
                appendMessage(question, 'user');
                input.value = '';
                input.disabled = true;
                const loadingNode = appendMessage(root.dataset.typing || '...', 'bot');
                handoff.hidden = true;
                handoff.innerHTML = '';

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({
                            question: question,
                            locale: locale
                        }),
                        credentials: 'same-origin'
                    });

                    const payload = await response.json();
                    loadingNode.remove();

                    if (!response.ok || !payload.answer) {
                        appendMessage(root.dataset.error || 'Error', 'bot');
                        return;
                    }

                    appendMessage(payload.answer, 'bot');
                    renderHandoff(payload);
                } catch (error) {
                    loadingNode.remove();
                    appendMessage(root.dataset.error || 'Error', 'bot');
                } finally {
                    input.disabled = false;
                    input.focus();
                }
            });
        });
    </script>
@endif
