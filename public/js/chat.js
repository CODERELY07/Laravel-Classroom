// Class chat. Messages arrive over the private "classroom.{id}" channel via
// Laravel Echo; when Echo is unavailable the log polls the history endpoint.
// All message text is inserted as text nodes, never as HTML.

import { avatar, el, errorMessage, toast } from './core.js';

const POLL_MS = 5000;

export function initChat() {
    const root = document.getElementById('chat');
    if (!root) return;

    const log = root.querySelector('[data-chat-log]');
    const form = root.querySelector('[data-chat-form]');
    const input = form.querySelector('textarea');
    const button = form.querySelector('button[type="submit"]');
    const connection = root.querySelector('[data-chat-connection]');
    const me = Number(root.dataset.userId);
    const seen = new Set();
    let lastSender = null;
    let pollTimer = null;
    let loaded = false;

    const timeFormat = new Intl.DateTimeFormat(undefined, { hour: 'numeric', minute: '2-digit' });

    const setStatus = (text) => {
        log.querySelector('[data-chat-status]')?.remove();
        if (text) log.append(el('p', { class: 'chat-status', 'data-chat-status': true }, text));
    };

    const nearBottom = () => log.scrollHeight - log.scrollTop - log.clientHeight < 80;

    const append = (chat) => {
        if (!chat || seen.has(chat.id)) return;
        seen.add(chat.id);
        setStatus(null);

        const mine = chat.user.id === me;
        const grouped = lastSender === chat.user.id;
        lastSender = chat.user.id;
        const stick = mine || nearBottom();

        log.append(
            el('div', { class: `msg-row${mine ? ' mine' : ''}${grouped ? ' grouped' : ''}` }, [
                avatar(chat.user, 'xs'),
                el('div', { class: 'min-w-0' }, [
                    !mine && !grouped ? el('span', { class: 'msg-name' }, chat.user.name) : null,
                    el('div', { class: 'msg-bubble' }, chat.message),
                    chat.created_at ? el('time', { class: 'msg-time', datetime: chat.created_at }, timeFormat.format(new Date(chat.created_at))) : null,
                ]),
            ])
        );

        if (stick) log.scrollTop = log.scrollHeight;
    };

    const loadHistory = () =>
        $.getJSON(root.dataset.historyUrl)
            .done(({ chats }) => {
                if (!loaded && chats.length === 0) setStatus('No messages yet. Say hello to the class!');
                chats.forEach(append);
                if (!loaded) log.scrollTop = log.scrollHeight;
                loaded = true;
            })
            .fail(() => !loaded && setStatus('Messages could not be loaded. Reload the page to try again.'));

    const startPolling = () => {
        if (pollTimer) return;
        connection.textContent = 'Refreshing every few seconds';
        pollTimer = setInterval(() => document.visibilityState === 'visible' && loadHistory(), POLL_MS);
    };

    const subscribe = () => {
        if (!window.Echo) {
            startPolling();
            return;
        }
        const channel = window.Echo.private(`classroom.${root.dataset.classId}`);
        channel.listen('MessageSent', append);
        channel.error?.(() => startPolling());

        const pusher = window.Echo.connector?.pusher?.connection;
        pusher?.bind('state_change', ({ current }) => {
            if (current === 'connected') {
                connection.textContent = 'Live';
                connection.classList.add('pill-accent');
                clearInterval(pollTimer);
                pollTimer = null;
            } else if (['unavailable', 'failed', 'disconnected'].includes(current)) {
                connection.classList.remove('pill-accent');
                startPolling();
            }
        });
        if (pusher?.state === 'connected') {
            connection.textContent = 'Live';
            connection.classList.add('pill-accent');
        }
    };

    const send = () => {
        const message = input.value.trim();
        if (!message || button.disabled) return;

        button.disabled = true;
        $.post(root.dataset.sendUrl, { class_id: root.dataset.classId, message })
            .done(({ chat }) => {
                append(chat);
                input.value = '';
                autoGrow();
            })
            .fail((xhr) => toast(errorMessage(xhr, 'Your message was not sent.'), 'error'))
            .always(() => {
                button.disabled = false;
                input.focus();
            });
    };

    const autoGrow = () => {
        input.style.height = 'auto';
        input.style.height = `${Math.min(input.scrollHeight, 140)}px`;
    };

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        send();
    });
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
            e.preventDefault();
            send();
        }
    });
    input.addEventListener('input', autoGrow);

    // The tab may be hidden on load; scroll to the newest message when it opens.
    document.addEventListener('tab:shown', ({ detail }) => {
        if (detail === 'chats') log.scrollTop = log.scrollHeight;
    });

    loadHistory();
    // Echo is created by the Vite bundle, which may finish loading after this module.
    if (window.Echo) subscribe();
    else window.addEventListener('load', subscribe, { once: true });
}
