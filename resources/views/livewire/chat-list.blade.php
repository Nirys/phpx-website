<div class="flex flex-col w-16 md:w-1/3 border-r border-gray-200 dark:border-gray-800/50">
    <div class="flex items-center h-20 gap-2 px-5 border-b dark:border-gray-800/60 border-gray-200/90">
        <p class="text-lg font-bold hidden md:flex">{{ __('Active Conversations') }}</p>
        <x-filament::badge>
            {{ $conversations->count() }}
        </x-filament::badge>
    </div>

    <!-- Conversations -->
    <div class="flex-1 overflow-y-auto">
        @if ($conversations->count() > 0)
            <div x-init="Echo.channel('filachat')
                .listen('FilaChatMessageEvent', e => {
                    Livewire.dispatch('load-conversations');
                });" class="grid w-full">
                @foreach ($conversations as $conversation)
                    <a wire:key="{{ $conversation->id }}" wire:navigate
                        href="{{ route('filament.admin.pages.messages.{id?}', $conversation->id) }}"
                        @class([
                            'p-2 md:p-5 mx-1 my-0.5 rounded-xl',
                            'hover:bg-gray-100 hover:dark:bg-gray-800/20' =>
                                $conversation->id != $selectedConversation?->id,
                            'bg-gray-200/60 dark:bg-gray-800' =>
                                $conversation->id == $selectedConversation?->id,
                        ])>
                        <div class="flex items-start justify-start w-full gap-2">
                            <x-filament::avatar src="{!! $conversation->avatar !!}" alt="Profile" size="lg" />
                            <div class="hidden md:grid w-full grid-cols-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold truncate">{{ $conversation->displayName }}
                                    </p>
                                    <p class="text-sm font-light text-gray-600 dark:text-gray-500">
                                        {{ \Carbon\Carbon::parse($conversation->lastMessage->sentAt)->shortAbsoluteDiffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between gap-1">
                                    <p class="text-sm text-gray-600 truncate dark:text-gray-400">
                                        @if ($conversation->lastMessage->isSender)
                                            <span class="text-primary-600 dark:text-primary-400 font-bold">You: </span>
                                        @endif
                                        {{ $conversation->lastMessage->text }}
                                    </p>
                                    @if ($conversation->unreadCount > 0)
                                        <x-filament::badge>
                                            {{ $conversation->unreadCount }}
                                        </x-filament::badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center h-full">
                <div class="p-3 mb-4 bg-gray-100 rounded-full dark:bg-gray-500/20">
                    <x-filament::icon icon="heroicon-m-x-mark" class="w-6 h-6 text-gray-500 dark:text-gray-400" />
                </div>
                <p class="text-base text-gray-600 dark:text-gray-400 hidden md:block">
                    {{ __('No conversations yet') }}
                </p>
            </div>
        @endif
    </div>
    <x-filament-actions::modals />
</div>
