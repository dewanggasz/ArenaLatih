{{--
File: resources/views/chat/index.blade.php
Deskripsi: Halaman utama untuk fitur ruang diskusi (open chat).
--}}
<x-app-layout>
    {{-- Slot untuk CSRF token, dibutuhkan oleh AJAX --}}
    <x-slot name="meta">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </x-slot>

    <style>
        /* Custom scrollbar untuk tampilan yang lebih bersih pada browser berbasis WebKit */
        #chat-box::-webkit-scrollbar {
            width: 8px;
        }
        #chat-box::-webkit-scrollbar-track {
            background: #f1f5f9; /* slate-100 */
        }
        #chat-box::-webkit-scrollbar-thumb {
            background: #94a3b8; /* slate-400 */
            border-radius: 4px;
        }
        #chat-box::-webkit-scrollbar-thumb:hover {
            background: #64748b; /* slate-500 */
        }
        .modal-enter { transition: ease-out duration-300; }
        .modal-enter-start { opacity: 0; }
        .modal-enter-end { opacity: 1; }
        .modal-leave { transition: ease-in duration-200; }
        .modal-leave-start { opacity: 1; }
        .modal-leave-end { opacity: 0; }
        .modal-scale-enter { transition: ease-out duration-300; }
        .modal-scale-enter-start { opacity: 0; transform: scale(0.95); }
        .modal-scale-enter-end { opacity: 1; transform: scale(1); }
        .modal-scale-leave { transition: ease-in duration-200; }
        .modal-scale-leave-start { opacity: 1; transform: scale(1); }
        .modal-scale-leave-end { opacity: 0; transform: scale(0.95); }
    </style>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-indigo-100 rounded-lg">
                <svg class="w-6 h-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="font-bold text-2xl text-slate-800 leading-tight">
                {{ __('Ruang Diskusi') }}
            </h2>
        </div>
    </x-slot>

    {{-- Komponen utama yang di-manage oleh Alpine.js --}}
    <div x-data="{
            replyingTo: null,
            messageToDelete: null,
            setReplyingTo(message) {
                this.replyingTo = message;
                this.$nextTick(() => document.getElementById('message-input').focus());
            },
            cancelReply() {
                this.replyingTo = null;
            },
            confirmDelete(message) {
                this.messageToDelete = message;
            },
            cancelDelete() {
                this.messageToDelete = null;
            }
        }"
         @set-replying-to.window="setReplyingTo($event.detail)"
         @cancel-reply.window="cancelReply()"
         @confirm-delete.window="confirmDelete($event.detail)"
         @cancel-delete.window="cancelDelete()"
         class="py-0 sm:py-6 md:py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl shadow-slate-300/50 sm:rounded-2xl">
                <div class="relative flex flex-col" style="height: calc(100vh - 12rem);">
                    
                    {{-- Area untuk menampilkan semua pesan --}}
                    <div id="chat-box" class="flex-grow overflow-y-auto space-y-8 p-6 md:p-8 bg-slate-50">
                        {{-- Pesan akan dirender di sini oleh JavaScript --}}
                    </div>
                    
                    {{-- Indikator pesan baru saat user tidak di bawah --}}
                    <div id="new-message-indicator" class="absolute bottom-24 left-1/2 -translate-x-1/2 hidden cursor-pointer">
                        <button onclick="document.getElementById('chat-box').scrollTo({ top: document.getElementById('chat-box').scrollHeight, behavior: 'smooth' })" class="bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-full shadow-lg flex items-center gap-2 animate-bounce">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 rotate-180" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            <span id="new-message-text">Pesan Baru</span>
                        </button>
                    </div>

                    {{-- Area input pesan di bagian bawah --}}
                    <div class="p-4 bg-white border-t-2 border-slate-200">
                        {{-- Notifikasi Error --}}
                        <div id="error-notification" class="hidden items-center gap-2 text-sm font-semibold text-red-700 bg-red-100 p-3 rounded-lg mb-2">
                            <svg class="w-5 h-5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>
                            <span id="error-text"></span>
                        </div>
                        {{-- Indikator Upload --}}
                        <div id="upload-indicator" class="hidden items-center gap-2 text-sm text-slate-500 mb-2 px-2">
                            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Mengunggah gambar...</span>
                        </div>

                        {{-- Tampilan 'Membalas Kepada' --}}
                        <div x-show="replyingTo" x-transition class="mb-3 p-3 bg-slate-100 rounded-lg relative">
                            <button @click="cancelReply()" type="button" class="absolute top-1 right-1 text-slate-400 hover:text-slate-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                            </button>
                            <p class="text-xs font-bold text-slate-600">Membalas kepada <span x-text="replyingTo ? replyingTo.user.name : ''"></span></p>
                            <p class="text-sm text-slate-500 truncate italic" x-text="replyingTo ? (replyingTo.type === 'image' ? '[Gambar]' : replyingTo.message) : ''"></p>
                        </div>
                        
                        {{-- Form untuk mengirim pesan --}}
                        <form id="chat-form" action="{{ route('chat.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="parent_id" :value="replyingTo ? replyingTo.id : ''">
                            <div class="relative flex items-center">
                                <button type="button" id="upload-button" class="absolute left-1.5 flex-shrink-0 flex items-center justify-center w-10 h-10 text-slate-400 hover:text-indigo-600 transition-colors duration-200">
                                    <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.122 2.122l7.81-7.81" /></svg>
                                </button>
                                <input type="file" id="image-upload-input" name="image" class="hidden" accept="image/png, image/jpeg, image/gif">
                                <input type="text" id="message-input" name="message" class="flex-grow bg-slate-100 border-2 border-transparent rounded-xl shadow-inner focus:border-indigo-400 focus:ring-2 focus:ring-indigo-300 focus:ring-opacity-50 py-3 pl-12 pr-14 transition-colors duration-200" placeholder="Ketik pesan Anda..." autocomplete="off">
                                <button type="submit" class="absolute right-1.5 flex-shrink-0 flex items-center justify-center w-10 h-10 text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-all duration-200 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 shadow-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Konfirmasi Hapus -->
        <div x-show="messageToDelete" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
            <div x-show="messageToDelete" x-transition:enter="modal-enter" x-transition:enter-start="modal-enter-start" x-transition:enter-end="modal-enter-end" x-transition:leave="modal-leave" x-transition:leave-start="modal-leave-start" x-transition:leave-end="modal-leave-end" class="fixed inset-0 bg-black bg-opacity-60 transition-opacity" @click="cancelDelete()"></div>
            <div x-show="messageToDelete" x-transition:enter="modal-scale-enter" x-transition:enter-start="modal-scale-enter-start" x-transition:enter-end="modal-scale-enter-end" x-transition:leave="modal-scale-leave" x-transition:leave-start="modal-scale-leave-start" x-transition:leave-end="modal-scale-leave-end" class="relative z-10 w-full max-w-md p-6 mx-4 bg-white rounded-2xl shadow-xl">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" /></svg>
                    </div>
                    <div class="flex-grow mt-0 text-left">
                        <h3 class="text-lg font-bold text-slate-800" id="modal-title">Hapus Pesan</h3>
                        <div class="mt-2">
                            <p class="text-sm text-slate-500">Apakah Anda yakin ingin menghapus pesan ini? Tindakan ini tidak dapat diurungkan.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                    <button id="confirm-delete-button" type="button" @click="window.handleDelete(messageToDelete.id)" class="inline-flex justify-center w-full px-4 py-2 text-base font-semibold text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 sm:w-auto sm:text-sm">
                        Ya, Hapus
                    </button>
                    <button type="button" @click="cancelDelete()" class="inline-flex justify-center w-full px-4 py-2 mt-3 text-base font-semibold text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- DEKLARASI ELEMEN & STATE ---
            const alpineComponent = document.querySelector('[x-data]');
            const chatBox = document.getElementById('chat-box');
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');
            const uploadButton = document.getElementById('upload-button');
            const imageUploadInput = document.getElementById('image-upload-input');
            const uploadIndicator = document.getElementById('upload-indicator');
            const errorNotification = document.getElementById('error-notification');
            const errorText = document.getElementById('error-text');
            const newMessageIndicator = document.getElementById('new-message-indicator');
            const newMessageText = document.getElementById('new-message-text');

            let messages = {{ Js::from($messages) }};
            let lastMessageId = messages.length > 0 ? messages[messages.length - 1].id : 0;
            const currentUserId = {{ Auth::id() }};
            let newMessagesCount = 0;
            let isFetching = false;

            // --- FUNGSI-FUNGSI BANTUAN & UTAMA ---
            
            function sanitizeHTML(str) {
                if (!str) return '';
                const temp = document.createElement('div');
                temp.textContent = str;
                return temp.innerHTML;
            }
            
            const isScrolledToBottom = () => chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 150;
            const scrollToBottom = (behavior = 'smooth') => chatBox.scrollTo({ top: chatBox.scrollHeight, behavior });
            
            function showNotification(message, isError = true) {
                errorText.textContent = message;
                errorNotification.classList.toggle('bg-red-100', isError);
                errorNotification.classList.toggle('text-red-700', isError);
                errorNotification.classList.toggle('bg-green-100', !isError);
                errorNotification.classList.toggle('text-green-700', !isError);
                errorNotification.classList.remove('hidden');
                setTimeout(() => errorNotification.classList.add('hidden'), 4000);
            };

            function renderAllMessages() {
                chatBox.innerHTML = '';
                messages.forEach(renderMessage);
            }

            function renderMessage(message) {
                const messageWrapper = document.createElement('div');
                messageWrapper.id = `message-${message.id}`;
                
                if (message.user_id === null) {
                    messageWrapper.className = 'w-full flex justify-center';
                    messageWrapper.innerHTML = `<div class="px-4 py-2 bg-slate-200 text-slate-600 text-sm rounded-full">${sanitizeHTML(message.message)}</div>`;
                } else {
                    const isMe = message.user_id == currentUserId;
                    messageWrapper.className = `w-full flex flex-col group ${isMe ? 'items-end' : 'items-start'}`;
                    
                    let messageContentHtml;
                    if (message.type === 'image') {
                        const imageUrl = `{{ asset('storage') }}/${message.message}`;
                        messageContentHtml = `<a href="${imageUrl}" target="_blank" rel="noopener noreferrer"><img src="${imageUrl}" class="rounded-lg max-w-[200px] sm:max-w-xs h-auto block" alt="Gambar yang dikirim"></a>`;
                    } else {
                        const p = document.createElement('p');
                        p.className = 'text-base break-words';
                        p.textContent = message.message;
                        messageContentHtml = p.outerHTML;
                    }
                    
                    let parentHtml = '';
                    if (message.parent) {
                        const parentName = sanitizeHTML(message.parent.user.name);
                        const parentMessage = message.parent.type === 'image' ? '<i>[Gambar]</i>' : sanitizeHTML(message.parent.message);
                        parentHtml = `
                            <div class="border-l-2 ${isMe ? 'border-indigo-300' : 'border-slate-300'} pl-2 mb-2 text-xs">
                                <p class="font-bold ${isMe ? 'text-indigo-200' : 'text-slate-600'}">${parentName}</p>
                                <p class="${isMe ? 'text-indigo-100' : 'text-slate-500'} italic truncate">${parentMessage}</p>
                            </div>`;
                    }
                    
                    const buttonsHtml = `
                        <div class="absolute top-0 ${isMe ? 'left-0 -translate-x-full' : 'right-0 translate-x-full'} -translate-y-1/2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            
                            <button data-message-id="${message.id}" class="reply-button bg-white p-1.5 rounded-full shadow-md text-slate-500 hover:text-indigo-600 hover:bg-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    `;
                    
                    messageWrapper.innerHTML = `
                        ${!isMe ? `<span class="text-xs font-bold text-slate-500 ml-12 mb-1">${sanitizeHTML(message.user.name)}</span>` : ''}
                        <div class="flex items-end gap-2 max-w-lg relative">
                            ${!isMe ? `<div class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-300 flex items-center justify-center font-bold text-slate-600 text-xs">${sanitizeHTML(message.user.name).charAt(0).toUpperCase()}</div>` : ''}
                            ${buttonsHtml}
                            <div class="p-3 md:p-4 overflow-hidden ${isMe ? 'bg-indigo-600 text-white rounded-t-2xl rounded-l-2xl' : 'bg-white text-slate-800 rounded-t-2xl rounded-r-2xl'} shadow-md">
                                ${parentHtml}
                                ${messageContentHtml}
                            </div>
                        </div>
                        <p class="text-xs mt-1.5 ${isMe ? 'text-right' : 'text-left ml-11'} text-slate-400">${new Date(message.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</p>
                    `;
                }
                chatBox.appendChild(messageWrapper);
            }

            async function submitFormData(formData) {
                const isImageUpload = formData.has('image') && !!formData.get('image').name;
                const submitButton = chatForm.querySelector('button[type="submit"]');
                
                submitButton.disabled = true;
                if (isImageUpload) uploadIndicator.classList.remove('hidden');

                try {
                    const response = await fetch('{{ route('chat.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        },
                        body: formData
                    });
                    
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Terjadi kesalahan.');
                    
                    if(result.status === 'Pesan terkirim!') {
                        messageInput.value = '';
                        imageUploadInput.value = '';
                        window.dispatchEvent(new CustomEvent('cancel-reply')); // [PERBAIKAN] Targetkan 'window'
                        messages.push(result.message);
                        renderMessage(result.message);
                        lastMessageId = result.message.id;
                        scrollToBottom();
                    }
                } catch (error) {
                    console.error('Gagal mengirim:', error);
                    showNotification(error.message);
                } finally {
                    submitButton.disabled = false;
                    if (isImageUpload) uploadIndicator.classList.add('hidden');
                }
            }
            
            // [MODIFIKASI] - Fungsi fetchMessages telah diperbarui
            async function fetchMessages() {
                if(isFetching) return;
                isFetching = true;

                // Dapatkan semua ID pesan yang saat ini ditampilkan di chat box
                const visibleMessageElements = chatBox.querySelectorAll('[id^="message-"]');
                const visibleIds = Array.from(visibleMessageElements).map(el => parseInt(el.id.split('-')[1]));
                
                const wasAtBottom = isScrolledToBottom();
                try {
                    // Bangun URL dengan parameter last_id dan semua visible_ids
                    const params = new URLSearchParams({
                        last_id: lastMessageId
                    });
                    visibleIds.forEach(id => params.append('visible_ids[]', id));

                    const response = await fetch(`{{ route('chat.fetch') }}?${params.toString()}`);
                    const result = await response.json();
                    
                    const newMessages = result.new_messages || [];
                    const deletedIds = result.deleted_ids || [];

                    // [BARU] Proses pesan yang dihapus
                    if (deletedIds.length > 0) {
                        deletedIds.forEach(id => {
                            document.getElementById(`message-${id}`)?.remove();
                            messages = messages.filter(m => m.id !== id);
                        });
                    }

                    // Proses pesan baru (logika yang ada)
                    if (newMessages.length > 0) {
                        newMessages.forEach(message => {
                            if (!messages.find(m => m.id === message.id)) {
                                messages.push(message);
                                renderMessage(message);
                            }
                        });
                        lastMessageId = messages.length > 0 ? messages[messages.length - 1].id : lastMessageId;
                        if (wasAtBottom) {
                            scrollToBottom();
                        } else {
                            newMessagesCount += newMessages.length;
                            newMessageText.textContent = `${newMessagesCount} Pesan Baru`;
                            newMessageIndicator.classList.remove('hidden');
                        }
                    }
                } catch (error) { console.error('Gagal mengambil pesan:', error); }
                finally { isFetching = false; }
            }
            
            window.handleDelete = async function(messageId) {
                const confirmButton = document.getElementById('confirm-delete-button');
                confirmButton.disabled = true;
                confirmButton.textContent = 'Menghapus...';

                try {
                    const response = await fetch(`/diskusi/${messageId}/delete`, { // Tambahkan /delete di akhir
                        method: 'POST', // Ubah dari 'DELETE' menjadi 'POST'
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                        }
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.message || 'Gagal menghapus pesan.');
                    
                    document.getElementById(`message-${messageId}`)?.remove();
                    messages = messages.filter(m => m.id !== messageId);
                    
                    showNotification('Pesan berhasil dihapus.', false);

                } catch (error) {
                    console.error('Gagal menghapus:', error);
                    showNotification(error.message);
                } finally {
                    window.dispatchEvent(new CustomEvent('cancel-delete')); // [PERBAIKAN] Targetkan 'window'
                    confirmButton.disabled = false;
                    confirmButton.textContent = 'Ya, Hapus';
                }
            }

            // --- INISIALISASI & EVENT LISTENERS ---
            renderAllMessages();
            scrollToBottom('auto');
            setInterval(fetchMessages, 5000);

            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const formData = new FormData(chatForm);
                if (messageInput.value.trim() || (imageUploadInput.files.length > 0 && imageUploadInput.files[0].name)) {
                   submitFormData(formData);
                }
            });

             uploadButton.addEventListener('click', () => imageUploadInput.click());
            imageUploadInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 1 * 1024 * 1024) { 
                        showNotification('Ukuran gambar tidak boleh melebihi 1MB.');
                        return;
                    }
                    const formData = new FormData(chatForm);
                    submitFormData(formData);
                }
                event.target.value = null; 
            });

            // Event Delegation untuk tombol balas DAN hapus
            chatBox.addEventListener('click', function(e) {
                const replyButton = e.target.closest('.reply-button');
                const deleteButton = e.target.closest('.delete-button');

                if (replyButton) {
                    const messageId = parseInt(replyButton.dataset.messageId, 10);
                    const messageToReply = messages.find(m => m.id === messageId);
                    if (messageToReply) {
                        window.dispatchEvent(new CustomEvent('set-replying-to', { detail: messageToReply, bubbles: true }));
                    }
                }

                if (deleteButton) {
                    const messageId = parseInt(deleteButton.dataset.messageId, 10);
                    const messageToDelete = messages.find(m => m.id === messageId);
                    if(messageToDelete) {
                        window.dispatchEvent(new CustomEvent('confirm-delete', { detail: messageToDelete, bubbles: true }));
                    }
                }
            });
            
            chatBox.addEventListener('scroll', () => {
                if (isScrolledToBottom()) {
                    newMessageIndicator.classList.add('hidden');
                    newMessagesCount = 0;
                }
            });
        });
    </script>
</x-app-layout>
