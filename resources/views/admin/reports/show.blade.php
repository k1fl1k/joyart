<x-app-layout>
    <x-slot name="title">Деталі скарги</x-slot>

    <div class="py-12">
        <div class="profile-container">
            <div class="admin-main">
                <!-- Header -->
                <div class="profile-header">
                    <h1 class="admin-info">Деталі скарги</h1>
                    <a href="{{ route('admin.reports.index') }}" class="admin-button">← Назад до списку</a>
                </div>

                <!-- Main Content -->
                <main class="admin-content">
                    <div class="bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium text-white">
                                Інформація про скаргу
                            </h3>
                            <p class="mt-1 max-w-2xl text-sm text-gray-500">
                                ID: {{ substr($report->id, 0, 8) }}
                            </p>
                        </div>
                        <div class="border-t border-gray-800">
                            <dl>
                                <div class="bg-gray-750 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Пост
                                    </dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        <a href="{{ route('artwork.show', $report->artwork->slug) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                            <img src="{{ Str::startsWith($report->artwork->thumbnail, 'http') ? $report->artwork->thumbnail : asset($report->artwork->thumbnail) }}"
                                                 alt="{{ $report->artwork->meta_title }}"
                                                 class="w-32 h-32 object-cover">
                                            <span class="ml-2">{{ $report->artwork->meta_title }}</span>
                                        </a>
                                    </dd>
                                </div>
                                <div class="bg-gray-750 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Автор поста
                                    </dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        {{ $report->artwork->user->username }}
                                    </dd>
                                </div>
                                <div class="bg-gray-750 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Скаржник
                                    </dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        {{ $report->user->username }}
                                    </dd>
                                </div>
                                <div class="bg-gray-750 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Причина
                                    </dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        @switch($report->reason)
                                            @case('inappropriate_content')
                                                Неприйнятний вміст
                                                @break
                                            @case('copyright_violation')
                                                Порушення авторських прав
                                                @break
                                            @case('offensive')
                                                Образливий вміст
                                                @break
                                            @case('spam')
                                                Спам
                                                @break
                                            @default
                                                Інше
                                        @endswitch
                                    </dd>
                                </div>
                                <div class="bg-gray-750 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Опис
                                    </dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        {{ $report->description ?? 'Не вказано' }}
                                    </dd>
                                </div>
                                <div class="bg-gray-750 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Статус
                                    </dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $report->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $report->status === 'reviewed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $report->status === 'dismissed' ? 'bg-red-100 text-red-800' : '' }}">
                                            @switch($report->status)
                                                @case('pending')
                                                    Очікує розгляду
                                                    @break
                                                @case('reviewed')
                                                    Розглянуто
                                                    @break
                                                @case('dismissed')
                                                    Відхилено
                                                    @break
                                            @endswitch
                                        </span>
                                    </dd>
                                </div>
                                <div class="bg-gray-750 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                    <dt class="text-sm font-medium text-gray-500">
                                        Дата створення
                                    </dt>
                                    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                        {{ $report->created_at->format('d.m.Y H:i') }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Update Status Form -->
                    <div class="mt-6 bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                        <div class="px-4 py-5 sm:px-6">
                            <h3 class="text-lg leading-6 font-medium bh-white">
                                Оновити статус
                            </h3>
                        </div>
                        <div class="border-t border-gray-200 px-4 py-5">
                            <form action="{{ route('admin.reports.updateStatus', $report->id) }}" method="POST">
                                @csrf
                                <div class="flex items-center space-x-4">
                                    <select name="status" class="admin-input">
                                        <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Очікує розгляду</option>
                                        <option value="reviewed" {{ $report->status === 'reviewed' ? 'selected' : '' }}>Розглянуто</option>
                                        <option value="dismissed" {{ $report->status === 'dismissed' ? 'selected' : '' }}>Відхилено</option>
                                    </select>
                                    <button type="submit" class="admin-button">Оновити статус</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
