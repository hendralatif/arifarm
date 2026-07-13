@extends('layouts.admin')

@section('content')
<div class="h-[calc(100vh-140px)] flex bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm">
    
    {{-- Left Sidebar: Active Conversations List --}}
    <div class="w-full md:w-96 flex flex-col border-r border-slate-200 dark:border-slate-800 shrink-0" id="chat-sidebar">
        <!-- Sidebar Header / Search -->
        <div class="p-4 border-b border-slate-100 dark:border-slate-850 space-y-3">
            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center">
                <i class="fa-solid fa-comments mr-2 text-[#09422a] dark:text-emerald-450"></i>Obrolan Pelanggan
            </h2>
            <div class="relative">
                <input type="text" id="conversation-search" placeholder="Cari pelanggan..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-250 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-[#09422a]/20 focus:border-[#09422a] text-slate-800 dark:text-slate-200">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
            </div>
        </div>
        
        <!-- Conversations Scrollable Area -->
        <div class="flex-grow overflow-y-auto divide-y divide-slate-100/60 dark:divide-slate-850 bg-white dark:bg-slate-900" id="conversations-list-container">
            <!-- Loading indicator -->
            <div class="p-6 text-center text-slate-400 space-y-2" id="conversations-loading">
                <i class="fa-solid fa-circle-notch fa-spin text-lg text-[#09422a] dark:text-emerald-450"></i>
                <p class="text-xs">Memuat daftar obrolan...</p>
            </div>
            <!-- Empty state -->
            <div class="hidden p-6 text-center text-slate-400" id="conversations-empty">
                <i class="fa-solid fa-message-slash text-2xl mb-2 opacity-30"></i>
                <p class="text-xs font-medium">Belum ada obrolan masuk.</p>
            </div>
            <!-- List wrapper -->
            <div class="divide-y divide-slate-100 dark:divide-slate-850" id="conversations-list"></div>
        </div>
    </div>

    {{-- Right Pane: Conversation Window --}}
    <div class="hidden md:flex flex-col flex-grow bg-slate-50 dark:bg-slate-950/20" id="chat-window">
        <!-- Default Empty State -->
        <div class="flex-grow flex flex-col items-center justify-center text-center p-8 space-y-4" id="chat-window-empty">
            <div class="w-20 h-20 rounded-full bg-slate-100 dark:bg-slate-850 flex items-center justify-center text-slate-400 text-3xl shadow-inner">
                <i class="fa-solid fa-comments"></i>
            </div>
            <div>
                <h4 class="text-base font-extrabold text-slate-700 dark:text-slate-200">Pilih Obrolan Pelanggan</h4>
                <p class="text-xs text-slate-400 max-w-sm mt-1">Pilih salah satu kontak di sebelah kiri untuk melihat riwayat pesan dan mulai membalas obrolan pelanggan.</p>
            </div>
        </div>

        <!-- Chat Frame (Hidden initially) -->
        <div class="flex-grow flex flex-col h-full hidden" id="chat-window-active">
            <!-- Chat Window Header -->
            <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 p-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center space-x-3">
                    <button onclick="backToSidebar()" class="md:hidden p-2 text-slate-500 hover:text-slate-800 transition mr-1">
                        <i class="fa-solid fa-arrow-left text-base"></i>
                    </button>
                    <div class="w-10 h-10 rounded-full bg-[#09422a]/10 text-[#09422a] dark:text-emerald-450 dark:bg-emerald-950/40 flex items-center justify-center font-bold text-base" id="active-user-avatar">
                        U
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white" id="active-user-name">Nama Pelanggan</h4>
                        <p class="text-[10px] text-slate-400" id="active-user-email">customer@gmail.com</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 animate-pulse"></span> Aktif
                    </span>
                </div>
            </div>

            <!-- Messages Stream -->
            <div class="flex-grow p-5 overflow-y-auto space-y-3 flex flex-col" id="active-chat-messages"></div>

            <!-- Chat Send Box -->
            <div class="p-4 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800">
                <form id="active-chat-form" onsubmit="sendAdminMessage(event)" class="flex items-center gap-3">
                    <input type="text" id="active-chat-input" placeholder="Tulis balasan Anda..." class="flex-1 px-4 py-2.5 text-xs rounded-xl border border-slate-250 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-[#09422a]/20 focus:border-[#09422a] text-slate-800 dark:text-slate-200" required autocomplete="off">
                    <button type="submit" class="px-5 py-2.5 flex items-center justify-center rounded-xl bg-[#09422a] text-white hover:bg-[#073321] transition font-bold text-xs gap-1.5 shrink-0 shadow-md shadow-[#09422a]/10">
                        <span>Kirim</span>
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Force side-by-side layout on desktop */
    @media (min-width: 768px) {
        #chat-sidebar {
            display: flex !important;
            width: 360px !important;
            min-width: 360px !important;
            flex-shrink: 0 !important;
        }
        #chat-window {
            display: flex !important;
            flex-grow: 1 !important;
        }
    }
    
    /* Mobile responsive toggling rules */
    @media (max-width: 767px) {
        #chat-sidebar.hidden {
            display: none !important;
        }
        #chat-window.hidden {
            display: none !important;
        }
        #chat-window.flex {
            display: flex !important;
        }
    }

    #active-chat-messages {
        background-color: #efeae2;
        background-image: url("https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png");
        background-blend-mode: overlay;
    }
    .dark #active-chat-messages {
        background-color: #0b141a;
        background-image: url("https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png");
        background-blend-mode: overlay;
        opacity: 0.95;
    }
</style>

<script>
    let activeCustomerId = null;
    let conversations = [];
    let conversationsInterval = null;
    let messagesInterval = null;
    let lastMsgCount = 0;

    document.addEventListener('DOMContentLoaded', () => {
        // Initial Fetch
        loadConversations();
        conversationsInterval = setInterval(loadConversations, 4000);

        // Search Filter Event
        document.getElementById('conversation-search').addEventListener('input', (e) => {
            filterConversations(e.target.value.toLowerCase());
        });
    });

    function loadConversations() {
        fetch('{{ route('admin.chats.conversations') }}?t=' + new Date().getTime())
            .then(res => res.json())
            .then(data => {
                conversations = data;
                renderConversationsList();
            })
            .catch(err => console.error('Error fetching conversations:', err));
    }

    function renderConversationsList() {
        const loading = document.getElementById('conversations-loading');
        const empty = document.getElementById('conversations-empty');
        const list = document.getElementById('conversations-list');
        
        loading.classList.add('hidden');
        
        if (conversations.length === 0) {
            empty.classList.remove('hidden');
            list.innerHTML = '';
            return;
        }

        empty.classList.add('hidden');
        const searchQuery = document.getElementById('conversation-search').value.toLowerCase();
        
        let html = '';
        conversations.forEach(c => {
            // Apply search filter in client side rendering
            if (searchQuery && !c.name.toLowerCase().includes(searchQuery)) return;

            const activeClass = activeCustomerId === c.id 
                ? 'bg-slate-100/80 dark:bg-slate-800' 
                : 'hover:bg-slate-50 dark:hover:bg-slate-850';
                
            const badgeHtml = c.unread_count > 0 
                ? `<span class="bg-rose-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full">${c.unread_count}</span>` 
                : '';
                
            const initials = c.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

            html += `
                <div onclick="selectConversation(${c.id}, '${c.name}', '${c.email}')" class="p-3.5 flex items-center justify-between cursor-pointer transition ${activeClass}">
                    <div class="flex items-center space-x-3 min-w-0 flex-grow">
                        <div class="w-10 h-10 rounded-full bg-[#09422a]/10 text-[#09422a] dark:text-emerald-450 dark:bg-emerald-950/40 flex items-center justify-center font-extrabold text-xs shrink-0">
                            ${initials}
                        </div>
                        <div class="min-w-0 flex-grow">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate pr-2">${c.name}</h4>
                                <span class="text-[9px] text-slate-400 whitespace-nowrap">${c.last_message_time}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">${c.last_message}</p>
                        </div>
                    </div>
                    <div class="ml-2 shrink-0">
                        ${badgeHtml}
                    </div>
                </div>
            `;
        });

        list.innerHTML = html;
    }

    function filterConversations(query) {
        renderConversationsList();
    }

    function selectConversation(customerId, name, email) {
        activeCustomerId = customerId;
        lastMsgCount = 0;
        
        // Show frames
        document.getElementById('chat-window-empty').classList.add('hidden');
        const activeWindow = document.getElementById('chat-window-active');
        activeWindow.classList.remove('hidden');
        
        // Update user meta
        document.getElementById('active-user-name').innerText = name;
        document.getElementById('active-user-email').innerText = email;
        const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
        document.getElementById('active-user-avatar').innerText = initials;

        // Show/hide panes for responsiveness
        document.getElementById('chat-sidebar').classList.add('hidden', 'md:flex');
        document.getElementById('chat-window').classList.remove('hidden');
        document.getElementById('chat-window').classList.add('flex');

        // Render loading state inside message area
        document.getElementById('active-chat-messages').innerHTML = `
            <div class="flex flex-col items-center justify-center h-full text-slate-400 space-y-2">
                <i class="fa-solid fa-circle-notch fa-spin text-xl text-[#09422a]"></i>
                <span class="text-xs">Memuat obrolan...</span>
            </div>
        `;

        // Start message polling
        if (messagesInterval) clearInterval(messagesInterval);
        loadMessages();
        messagesInterval = setInterval(loadMessages, 3000);

        // Immediately update left pane selection style
        loadConversations();
    }

    function loadMessages() {
        if (!activeCustomerId) return;

        fetch(`/admin/chats/messages/${activeCustomerId}?t=` + new Date().getTime())
            .then(res => res.json())
            .then(data => {
                if (Array.isArray(data)) {
                    const container = document.getElementById('active-chat-messages');
                    let html = '';
                    let currentDate = '';

                    data.forEach(msg => {
                        if (msg.date !== currentDate) {
                            currentDate = msg.date;
                            html += `
                                <div class="text-center my-4">
                                    <span class="bg-slate-200 text-slate-650 dark:bg-slate-800 dark:text-slate-400 text-[10px] px-2.5 py-1 rounded-full font-bold">
                                        ${msg.date}
                                    </span>
                                </div>
                            `;
                        }

                        if (msg.is_sender) {
                            html += `
                                <div class="flex justify-end items-end space-x-1 max-w-[80%] ml-auto">
                                    <div class="bg-[#09422a] text-white text-xs px-4 py-2.5 rounded-2xl rounded-tr-none shadow-sm font-medium">
                                        <p class="whitespace-pre-wrap">${msg.message}</p>
                                        <span class="block text-[8px] text-emerald-200/80 text-right mt-1 font-mono">${msg.time}</span>
                                    </div>
                                </div>
                            `;
                        } else {
                            html += `
                                <div class="flex justify-start items-end space-x-2.5 max-w-[80%]">
                                    <div class="w-8 h-8 rounded-full bg-[#09422a]/10 text-[#09422a] dark:text-emerald-450 dark:bg-emerald-950/40 flex items-center justify-center font-bold text-xs shrink-0">
                                        ${document.getElementById('active-user-avatar').innerText}
                                    </div>
                                    <div class="bg-white dark:bg-slate-850 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-800 text-xs px-4 py-2.5 rounded-2xl rounded-tl-none shadow-sm font-medium">
                                        <p class="whitespace-pre-wrap">${msg.message}</p>
                                        <span class="block text-[8px] text-slate-450 text-right mt-1 font-mono">${msg.time}</span>
                                    </div>
                                </div>
                            `;
                        }
                    });

                    container.innerHTML = html;

                    // Scroll to bottom if new messages arrived
                    if (data.length > lastMsgCount) {
                        container.scrollTop = container.scrollHeight;
                        lastMsgCount = data.length;
                    }
                }
            })
            .catch(err => console.error('Error fetching messages:', err));
    }

    function sendAdminMessage(e) {
        e.preventDefault();
        if (!activeCustomerId) return;

        const input = document.getElementById('active-chat-input');
        const message = input.value.trim();
        if (!message) return;

        input.value = '';

        fetch(`/admin/chats/messages/${activeCustomerId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                loadMessages();
                loadConversations();
            }
        })
        .catch(err => console.error('Error sending message:', err));
    }

    function backToSidebar() {
        activeCustomerId = null;
        if (messagesInterval) clearInterval(messagesInterval);
        
        document.getElementById('chat-sidebar').classList.remove('hidden');
        document.getElementById('chat-window').classList.add('hidden');
        document.getElementById('chat-window').classList.remove('flex');
        
        document.getElementById('chat-window-active').classList.add('hidden');
        document.getElementById('chat-window-empty').classList.remove('hidden');
        
        loadConversations();
    }
</script>
@endsection
