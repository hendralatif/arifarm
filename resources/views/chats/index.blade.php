@extends('layouts.public')

@section('title', 'Chat Room - ARI FARM')

@section('content')
<section class="py-6 bg-slate-50 dark:bg-slate-900/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Main Chat Box Wrapper -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xl flex overflow-hidden" style="height: 720px; max-height: 80vh;">
            
            <!-- LEFT PANEL: Conversations list -->
            <div id="left-pane" class="w-full md:w-96 flex flex-col border-r border-slate-100 dark:border-slate-800 shrink-0">
                <!-- User Profile Header -->
                <div class="p-4 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-[#09422a] text-white flex items-center justify-center font-bold text-base shadow-sm">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-bold text-slate-850 dark:text-slate-200 truncate">{{ Auth::user()->name }}</h4>
                            <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <!-- Start Conversation Icon -->
                    <button onclick="openSearchModal()" class="p-2 text-slate-500 hover:text-[#09422a] hover:bg-slate-100 dark:hover:bg-slate-850 rounded-xl transition" title="Mulai Chat Baru">
                        <i class="fa-solid fa-message-plus text-lg"></i>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="p-3 border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <div class="relative">
                        <input type="text" id="conversation-search" placeholder="Cari percakapan..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-250 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-[#09422a]/20 focus:border-[#09422a] text-slate-800 dark:text-slate-250">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <!-- Conversations list container -->
                <div id="conversations-list" class="flex-grow overflow-y-auto divide-y divide-slate-100 dark:divide-slate-850 bg-white dark:bg-slate-900">
                    <!-- Loading state -->
                    <div class="p-8 text-center text-slate-400 space-y-2">
                        <i class="fa-solid fa-circle-notch fa-spin text-xl text-[#09422a]"></i>
                        <p class="text-xs">Memuat daftar pesan...</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL: Active conversation -->
            <div id="right-pane" class="hidden md:flex flex-col flex-grow bg-slate-50 dark:bg-slate-950/20">
                <!-- Welcome/Empty State -->
                <div id="chat-empty-state" class="flex-grow flex flex-col items-center justify-center text-center p-8 space-y-4">
                    <div class="w-20 h-20 rounded-full bg-[#09422a]/5 text-[#09422a] dark:text-emerald-450 dark:bg-emerald-950/20 flex items-center justify-center text-3xl shadow-sm">
                        <i class="fa-solid fa-comments"></i>
                    </div>
                    <div class="max-w-sm">
                        <h4 class="text-base font-bold text-slate-800 dark:text-slate-200">ARI FARM Chat</h4>
                        <p class="text-xs text-slate-400 mt-1">Pilih salah satu percakapan di sebelah kiri atau klik tombol tambah pesan untuk memulai obrolan baru.</p>
                    </div>
                </div>

                <!-- Chat Room Area (Hidden by default) -->
                <div id="chat-active-state" class="flex-grow flex flex-col overflow-hidden hidden">
                    <!-- Header -->
                    <div class="p-4 bg-white dark:bg-slate-900 flex items-center justify-between border-b border-slate-100 dark:border-slate-800">
                        <div class="flex items-center space-x-3 min-w-0">
                            <!-- Mobile back button -->
                            <button onclick="backToPaneList()" class="md:hidden p-2 -ml-2 text-slate-500 hover:text-slate-800 dark:hover:text-white rounded-xl transition">
                                <i class="fa-solid fa-arrow-left text-base"></i>
                            </button>
                            <div class="w-10 h-10 rounded-full bg-[#09422a]/10 text-[#09422a] dark:text-emerald-450 dark:bg-emerald-950/40 flex items-center justify-center font-bold text-base shadow-sm" id="active-user-avatar">
                                U
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate" id="active-user-name">Nama Pengguna</h4>
                                <div class="flex items-center space-x-1.5 mt-0.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 hidden" id="active-user-online-dot"></span>
                                    <span class="text-[10px] text-slate-400 font-medium" id="active-user-status">Offline</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Stream -->
                    <div id="messages-container" class="flex-grow p-4 overflow-y-auto space-y-4 bg-slate-50 dark:bg-slate-950 flex flex-col">
                        <!-- Message bubbles render here -->
                    </div>

                    <!-- Message Input area -->
                    <div class="p-4 bg-white dark:bg-slate-900 border-t border-slate-150 dark:border-slate-800">
                        <form id="chat-form" onsubmit="sendChatMessage(event)" class="flex items-center gap-3">
                            <input type="text" id="chat-input" placeholder="Tulis pesan Anda di sini..." class="flex-1 px-4 py-3 text-sm rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-[#09422a]/20 focus:border-[#09422a] text-slate-800 dark:text-slate-200" required autocomplete="off">
                            <button type="submit" class="h-11 w-11 flex items-center justify-center rounded-2xl bg-[#09422a] hover:bg-[#073321] text-white transition shrink-0 shadow-lg shadow-[#09422a]/15">
                                <i class="fa-solid fa-paper-plane text-base"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- SEARCH USER MODAL -->
<div id="search-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeSearchModal()"></div>
    
    <!-- Content container -->
    <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 z-10 mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Mulai Obrolan Baru</h3>
            <button onclick="closeSearchModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-800 dark:hover:text-white">
                <i class="fa-solid fa-times text-lg"></i>
            </button>
        </div>

        <div class="relative mb-4">
            <input type="text" id="search-user-input" placeholder="Cari username atau email..." class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-250 dark:border-slate-850 bg-slate-50 dark:bg-slate-950 focus:outline-none focus:ring-2 focus:ring-[#09422a]/20 focus:border-[#09422a] text-slate-800 dark:text-slate-250">
            <i class="fa-solid fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
        </div>

        <!-- Search results list -->
        <div id="search-results" class="max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-850">
            <div class="p-4 text-center text-xs text-slate-400">
                Ketik nama atau email pengguna untuk mencari.
            </div>
        </div>
    </div>
</div>

<style>
    #conversations-list::-webkit-scrollbar,
    #messages-container::-webkit-scrollbar,
    #search-results::-webkit-scrollbar {
        width: 6px;
    }
    #conversations-list::-webkit-scrollbar-thumb,
    #messages-container::-webkit-scrollbar-thumb,
    #search-results::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.3);
        border-radius: 9999px;
    }
    #live-chat-btn {
        display: none !important; /* Hide floating chat on dedicated chat index page */
    }

    /* Force side-by-side split pane on desktop */
    @media (min-width: 768px) {
        #left-pane {
            display: flex !important;
            width: 360px !important;
            min-width: 360px !important;
            flex-shrink: 0 !important;
        }
        #right-pane {
            display: flex !important;
            flex-grow: 1 !important;
        }
    }
    
    /* Mobile responsive toggling rules */
    @media (max-width: 767px) {
        #left-pane.hidden {
            display: none !important;
        }
        #right-pane.hidden {
            display: none !important;
        }
        #right-pane.flex {
            display: flex !important;
        }
    }

    /* WhatsApp Doodle style background */
    #messages-container {
        background-color: #efeae2;
        background-image: url("https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png");
        background-blend-mode: overlay;
    }
    .dark #messages-container {
        background-color: #0b141a;
        background-image: url("https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png");
        background-blend-mode: overlay;
        opacity: 0.95;
    }
</style>

<script>
    let activeConversationId = null;
    let pollingTimer = null;
    let lastMsgCount = 0;
    let conversations = [];
    let initialSelect = '{{ request()->query("select") }}';

    document.addEventListener('DOMContentLoaded', function() {
        loadConversations();
        
        // Setup Polling
        pollingTimer = setInterval(() => {
            loadConversations();
            if (activeConversationId) {
                loadMessages();
            }
        }, 3000);

        // Search conversations listener
        const convSearch = document.getElementById('conversation-search');
        if (convSearch) {
            convSearch.addEventListener('input', renderConversations);
        }

        // Search users listener
        const userSearch = document.getElementById('search-user-input');
        if (userSearch) {
            userSearch.addEventListener('input', handleUserSearch);
        }
    });

    function loadConversations() {
        fetch('{{ route("chats.conversations") }}?t=' + new Date().getTime())
            .then(res => {
                if (!res.ok) {
                    throw new Error('Gagal memuat percakapan dari server.');
                }
                return res.json();
            })
            .then(data => {
                conversations = data;
                renderConversations();
                
                // If requested conversation is pre-selected, open it automatically
                if (initialSelect) {
                    const found = conversations.find(c => c.id == initialSelect);
                    if (found) {
                        selectConversation(found.id, found.user.name, found.user.email, found.user.initials, found.user.is_online);
                    }
                    initialSelect = null; // Clear so it only triggers once
                }
            })
            .catch(err => {
                console.error(err);
                const list = document.getElementById('conversations-list');
                if (list) {
                    list.innerHTML = `
                        <div class="p-6 text-center text-rose-500">
                            <i class="fa-solid fa-circle-exclamation text-xl mb-2"></i>
                            <p class="text-xs font-semibold">Gagal memuat percakapan.</p>
                            <p class="text-[10px] text-slate-400 mt-1">${err.message}</p>
                        </div>
                    `;
                }
            });
    }

    function renderConversations() {
        const list = document.getElementById('conversations-list');
        if (!list) return;

        const query = document.getElementById('conversation-search').value.toLowerCase();
        let html = '';
        let count = 0;

        conversations.forEach(c => {
            if (query && !c.user.name.toLowerCase().includes(query)) return;
            count++;

            const unreadDot = c.unread_count > 0 
                ? `<span class="bg-rose-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full shrink-0">${c.unread_count}</span>` 
                : '';
                
            const onlineStatusDot = c.user.is_online 
                ? `<span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></span>` 
                : '';

            const isActive = c.id === activeConversationId ? 'bg-slate-50 dark:bg-slate-850' : '';

            html += `
                <div onclick="selectConversationById(${c.id})" class="p-3.5 flex items-center justify-between cursor-pointer transition hover:bg-slate-50 dark:hover:bg-slate-850 ${isActive}">
                    <div class="flex items-center space-x-3 min-w-0 flex-grow">
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full bg-[#09422a]/10 text-[#09422a] dark:text-emerald-450 dark:bg-emerald-950/40 flex items-center justify-center font-extrabold text-xs">
                                ${c.user.initials}
                            </div>
                            ${onlineStatusDot}
                        </div>
                        <div class="min-w-0 flex-grow">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-850 dark:text-slate-200 truncate pr-2">${c.user.name}</h4>
                                <span class="text-[9px] text-slate-400 whitespace-nowrap">${c.last_message_time}</span>
                            </div>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mt-0.5">${c.last_message}</p>
                        </div>
                    </div>
                    <div class="ml-2 shrink-0">
                        ${unreadDot}
                    </div>
                </div>
            `;
        });

        if (count === 0) {
            list.innerHTML = `
                <div class="p-6 text-center text-slate-400">
                    <i class="fa-solid fa-message-slash text-xl mb-2 opacity-30"></i>
                    <p class="text-xs">Tidak ada percakapan ditemukan.</p>
                </div>
            `;
        } else {
            list.innerHTML = html;
        }
    }

    function selectConversationById(conversationId) {
        const c = conversations.find(x => x.id == conversationId);
        if (c) {
            selectConversation(c.id, c.user.name, c.user.email, c.user.initials, c.user.is_online);
        }
    }

    function selectConversation(conversationId, name, email, initials, isOnline) {
        activeConversationId = conversationId;
        lastMsgCount = 0;

        // Toggle layout classes (especially for mobile responsive view)
        document.getElementById('left-pane').classList.add('hidden', 'md:flex');
        document.getElementById('right-pane').classList.remove('hidden');
        document.getElementById('right-pane').classList.add('flex');

        // Set Active Header Info
        document.getElementById('active-user-name').innerText = name;
        document.getElementById('active-user-avatar').innerText = initials;
        
        const statusText = document.getElementById('active-user-status');
        const onlineDot = document.getElementById('active-user-online-dot');
        if (isOnline) {
            statusText.innerText = 'Online';
            statusText.classList.remove('text-slate-450');
            statusText.classList.add('text-emerald-500');
            onlineDot.classList.remove('hidden');
        } else {
            statusText.innerText = 'Offline';
            statusText.classList.remove('text-emerald-500');
            statusText.classList.add('text-slate-450');
            onlineDot.classList.add('hidden');
        }

        // Show active chat states
        document.getElementById('chat-empty-state').classList.add('hidden');
        document.getElementById('chat-active-state').classList.remove('hidden');

        // Force select styling re-render
        renderConversations();

        // Load Messages
        loadMessages();
    }

    function backToPaneList() {
        activeConversationId = null;
        document.getElementById('left-pane').classList.remove('hidden');
        document.getElementById('right-pane').classList.add('hidden');
        document.getElementById('right-pane').classList.remove('flex');
        
        // Remove select highlight
        renderConversations();
    }

    function loadMessages() {
        if (!activeConversationId) return;

        fetch(`/chats/messages/${activeConversationId}?t=` + new Date().getTime())
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('messages-container');
                if (!container) return;

                let html = '';
                let currentGroupDate = '';

                data.forEach(msg => {
                    if (msg.date !== currentGroupDate) {
                        currentGroupDate = msg.date;
                        html += `<div class="text-center my-3"><span class="bg-slate-200 text-slate-650 dark:bg-slate-800 dark:text-slate-400 text-[10px] px-2.5 py-1 rounded-full font-bold">${msg.date}</span></div>`;
                    }

                    if (msg.is_sender) {
                        const checkIcon = msg.is_read 
                            ? '<i class="fa-solid fa-check-double text-[10px] text-blue-500 ml-1.5"></i>' 
                            : '<i class="fa-solid fa-check text-[10px] text-slate-350 ml-1.5"></i>';
                            
                        html += `
                            <div class="flex justify-end items-end space-x-1 max-w-[85%] self-end ml-auto">
                                <div class="bg-[#09422a] text-white text-xs px-3.5 py-2 rounded-2xl rounded-tr-none shadow-sm font-medium">
                                    <p class="leading-relaxed">${msg.message}</p>
                                    <span class="flex items-center justify-end text-[8px] text-emerald-200/80 text-right mt-1 font-mono">
                                        ${msg.time}
                                        ${checkIcon}
                                    </span>
                                </div>
                            </div>
                        `;
                    } else {
                        html += `
                            <div class="flex justify-start items-end space-x-2 max-w-[85%]">
                                <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-850 flex items-center justify-center font-bold text-xs shrink-0 text-slate-600 dark:text-slate-350">
                                    ${document.getElementById('active-user-avatar').innerText}
                                </div>
                                <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 text-slate-800 dark:text-slate-150 text-xs px-3.5 py-2 rounded-2xl rounded-tl-none shadow-sm font-medium">
                                    <p class="leading-relaxed">${msg.message}</p>
                                    <span class="block text-[8px] text-slate-450 mt-1 font-mono">${msg.time}</span>
                                </div>
                            </div>
                        `;
                    }
                });

                container.innerHTML = html;

                // Auto scroll to bottom if message length changed
                if (data.length > lastMsgCount) {
                    container.scrollTop = container.scrollHeight;
                    lastMsgCount = data.length;
                }
            })
            .catch(err => console.error(err));
    }

    function sendChatMessage(e) {
        e.preventDefault();
        if (!activeConversationId) return;

        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        if (!message) return;

        input.value = '';

        fetch(`/chats/messages/${activeConversationId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
        .catch(err => console.error(err));
    }

    // Modal Search User actions
    function openSearchModal() {
        document.getElementById('search-modal').classList.remove('hidden');
        document.getElementById('search-user-input').focus();
    }

    function closeSearchModal() {
        document.getElementById('search-modal').classList.add('hidden');
        document.getElementById('search-user-input').value = '';
        document.getElementById('search-results').innerHTML = `
            <div class="p-4 text-center text-xs text-slate-400">
                Ketik nama atau email pengguna untuk mencari.
            </div>
        `;
    }

    let searchTimer = null;
    function handleUserSearch() {
        const query = document.getElementById('search-user-input').value.trim();
        const resultsContainer = document.getElementById('search-results');
        
        if (searchTimer) clearTimeout(searchTimer);
        
        if (!query) {
            resultsContainer.innerHTML = `
                <div class="p-4 text-center text-xs text-slate-400">
                    Ketik nama atau email pengguna untuk mencari.
                </div>
            `;
            return;
        }

        resultsContainer.innerHTML = `
            <div class="p-4 text-center text-xs text-slate-400">
                <i class="fa-solid fa-spinner fa-spin text-[#09422a] mr-1"></i> Mencari...
            </div>
        `;

        searchTimer = setTimeout(() => {
            fetch(`/chats/search-users?query=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(users => {
                    if (users.length === 0) {
                        resultsContainer.innerHTML = `
                            <div class="p-4 text-center text-xs text-slate-400">
                                Tidak ada pengguna dengan kecocokan nama/email.
                            </div>
                        `;
                        return;
                    }

                    let html = '';
                    users.forEach(u => {
                        html += `
                            <div onclick="createNewChatWithUser(${u.id})" class="p-3 flex items-center space-x-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-850 transition rounded-xl">
                                <div class="w-9 h-9 rounded-full bg-[#09422a]/15 text-[#09422a] dark:text-emerald-450 dark:bg-emerald-950/40 flex items-center justify-center font-bold text-xs shrink-0">
                                    ${u.initials}
                                </div>
                                <div class="min-w-0 flex-grow">
                                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">${u.name}</h4>
                                    <p class="text-[10px] text-slate-450 truncate">${u.email}</p>
                                </div>
                            </div>
                        `;
                    });
                    resultsContainer.innerHTML = html;
                })
                .catch(err => console.error(err));
        }, 300);
    }

    function createNewChatWithUser(userId) {
        closeSearchModal();
        
        fetch(`/chats/create-by-user/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Reload list and select it
                initialSelect = data.conversation_id;
                loadConversations();
            }
        })
        .catch(err => console.error(err));
    }
</script>
@endsection
