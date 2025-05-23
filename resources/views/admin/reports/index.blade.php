<x-app-layout>
    <x-slot name="title">Скарги на пости</x-slot>

    <div class="py-12">
        <div class="profile-container">
            <div class="admin-main">
                <!-- Sidebar -->
                <div class="profile-header">
                    <h1 class="admin-info">Скарги на пости</h1>
                    <p class="admin-subinfo">Перегляд та управління скаргами</p>
                    <a href="{{ route('admin.panel') }}" class="admin-button">Головна</a>
                </div>

                <!-- Main Content -->
                <main class="admin-content">
                    <!-- Status Filter -->
                    <div class="mb-6">
                        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex space-x-4">
                            <select name="status" class="admin-input">
                                <option value="">Всі скарги</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Очікують розгляду</option>
                                <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Розглянуті</option>
                                <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Відхилені</option>
                            </select>
                            <button type="submit" class="admin-button">Фільтрувати</button>
                        </form>
                    </div>

                    <!-- Reports Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-gray-800 border border-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-700 bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 border-b border-gray-700 bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Пост</th>
                                    <th class="px-6 py-3 border-b border-gray-700 bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Користувач</th>
                                    <th class="px-6 py-3 border-b border-gray-700 bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Причина</th>
                                    <th class="px-6 py-3 border-b border-gray-700 bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Статус</th>
                                    <th class="px-6 py-3 border-b border-gray-700 bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Дата</th>
                                    <th class="px-6 py-3 border-b border-gray-700 bg-gray-700 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">Дії</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reports as $report)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ substr($report->id, 0, 8) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <a href="{{ route('artwork.show', $report->artwork->slug) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">
                                                <img src="{{ Str::startsWith($report->artwork->thumbnail, 'http') ? $report->artwork->thumbnail : asset($report->artwork->thumbnail) }}"
                                                     alt="{{ $report->artwork->meta_title }}"
                                                     class="w-16 h-16 object-cover">
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ $report->user->username }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
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
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
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
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            {{ $report->created_at->format('d.m.Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                            <a href="{{ route('admin.reports.show', $report->id) }}" class="text-indigo-600 hover:text-indigo-900">Деталі</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-center">
                                            Скарг не знайдено
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
