<x-app-layout>
    <div class="py-12">
        <div class="settings-container">
            <h2 class="text-white text-xl font-semibold mb-4">Profile Settings</h2>

            <!-- Форма оновлення інформації -->
            <div class="settings-section">
                <livewire:profile.update-profile-information-form />
            </div>

            <!-- Форма оновлення дати народження -->
            <div class="settings-section">
                <livewire:profile.update-birthday-form />
            </div>

            <!-- Форма оновлення аватарки -->
            <div class="settings-section">
                <livewire:profile.update-avatar-form />
            </div>

            <!-- Форма оновлення пароля -->
            <div class="settings-section">
                <livewire:profile.update-password-form />
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('profile.show', auth()->user()->username) }}" class="profile-settings-btn">
                    Back to Profile
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
