<x-filament-panels::page>
    <div class="flex bg-white shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10"
        style="height: calc(100vh - 16rem);">
        <!-- Left Sidebar (Chat List) -->
        <livewire:chat-list :conversationId="$selectedConversation" />
        <!-- Right Section (Chat Conversation) -->
        <livewire:chat-box :conversationId="$selectedConversation" />
    </div>
</x-filament-panels::page>
