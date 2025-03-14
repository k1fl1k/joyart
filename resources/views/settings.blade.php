<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6">
                <h2 class="text-white text-xl font-semibold mb-4">Profile Settings</h2>

                <!-- Форма оновлення інформації -->
                <livewire:profile.update-profile-information-form />

                <!-- Форма оновлення пароля -->
                <livewire:profile.update-password-form />
            </div>
        </div>
    </div>
</x-app-layout>
