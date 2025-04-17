<x-app-layout>
    <x-slot name="title">Admin Panel</x-slot>

    <div class="py-12">
        <div class="profile-container">
            <div class="admin-main">
                <!-- Sidebar -->
                <div class="profile-header">
                    <h1 class="admin-info">Панель адміністратора</h1>
                    <p class="admin-subinfo">Вітаємо, {{ Auth::user()->username }}!</p>
                </div>

                <!-- Main Content -->
                <main class="admin-content">

                    <!-- Stats Section -->
                    <div class="grid grid-cols-1 md:grid-cols-3">
                        <div class="admin-user">
                            <h3 class="admin-h1">Користувачі</h3>
                            <p class="admin-p">Кількість: {{ $usersCount }}</p>
                            <!-- User Panel -->
                            <div class="mt-6">
                                <h3 class="admin-h1">Редагувати інформацію користувача</h3>
                                <input type="text" id="userSearch" placeholder="Пошук користувача" class="admin-input">
                                <div id="userSuggestions" class="search-suggestions"></div>

                                <form action="{{ route('admin.updateUser') }}" method="POST" id="editUserForm">
                                    @csrf
                                    <input type="hidden" name="id" id="editUserId">

                                    <label class="admin-label">Ім’я користувача</label>
                                    <input type="text" name="username" id="editUsername" class="admin-input" required>

                                    <label class="admin-label">Email</label>
                                    <input type="email" name="email" id="editEmail" class="admin-input" required>

                                    <label class="admin-label">Дата народження</label>
                                    <input type="date" name="birthday" id="editBirthday" class="admin-input">

                                    <label class="admin-label">Опис</label>
                                    <textarea name="description" id="editDescription" class="admin-input"></textarea>

                                    <label class="admin-label">Роль</label>
                                    <select name="role" id="editRole" class="admin-input">
                                        <option value="user">Користувач</option>
                                        <option value="moderator">Модератор</option>
                                        <option value="admin">Адміністратор</option>
                                    </select>

                                    <label class="admin-label">Дозволено 18+</label>
                                    <input type="checkbox" name="allow_adult" id="editAllowAdult">

                                    <button type="submit" class="admin-button">Зберегти зміни</button>
                                </form>
                            </div>
                        </div>
                        <div class="admin-artworks">
                            <h3 class="admin-h1">Артворки</h3>
                            <p class="admin-p">Завантажено: {{ $artworksCount }}</p>
                            <!-- Artworks Panel -->
                            <div class="mt-6">
                                <h3 class="admin-h1">Оновити артворки з Safebooru</h3>
                                <form action="{{ route('admin.fetchSafebooru') }}" method="POST">
                                    @csrf
                                    <label for="count" class="admin-label">Кількість</label>
                                    <input type="number" name="count" id="count" class="admin-input" required min="1">
                                    <button type="submit" class="admin-button">Завантажити</button>
                                </form>
                            </div>
                        </div>
                        <div class="admin-tags">
                            <h3 class="admin-h1">Теги</h3>
                            <p class="admin-p">Активних: {{ $tagsCount }}</p>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userSearchInput = document.getElementById('userSearch');
        const suggestionsBox = document.getElementById('userSuggestions');
        const form = document.getElementById('editUserForm');

        function showSuggestions(users) {
            suggestionsBox.innerHTML = '';

            if (users.length > 0) {
                suggestionsBox.classList.add('visible');
                users.forEach(user => {
                    const item = document.createElement('div');
                    item.className = 'p-2 hover:bg-gray-200 cursor-pointer';
                    item.innerText = user.username;
                    item.addEventListener('click', function () {
                        userSearchInput.value = user.username;
                        suggestionsBox.classList.remove('visible');
                        loadUser(user.id);
                    });
                    suggestionsBox.appendChild(item);
                });
            } else {
                suggestionsBox.classList.remove('visible');
            }
        }

        userSearchInput.addEventListener('input', async function () {
            const query = userSearchInput.value.trim();

            if (query.length > 1) {
                try {
                    const response = await fetch(`/admin/user-search?query=${encodeURIComponent(query)}`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const users = await response.json();
                    showSuggestions(users);
                } catch (error) {
                    console.error('Помилка при пошуку користувачів:', error);
                    suggestionsBox.classList.remove('visible');
                }
            } else {
                suggestionsBox.classList.remove('visible');
            }
        });

        async function loadUser(id) {
            try {
                const response = await fetch(`/admin/user-info/${id}`);
                if (!response.ok) throw new Error('Не вдалося отримати дані користувача');
                const user = await response.json();

                form.style.display = 'block';
                document.getElementById('editUserId').value = user.id;
                document.getElementById('editUsername').value = user.username ?? '';
                document.getElementById('editEmail').value = user.email ?? '';
                document.getElementById('editBirthday').value = user.birthday ?? '';
                document.getElementById('editDescription').value = user.description ?? '';
                document.getElementById('editAllowAdult').checked = !!user.allow_adult;
                document.getElementById('editRole').value = user.role ?? 'user';


                suggestionsBox.innerHTML = '';
            } catch (error) {
                console.error('Помилка при завантаженні користувача:', error);
            }
        }

        document.addEventListener('click', function (event) {
            if (!userSearchInput.contains(event.target) && !suggestionsBox.contains(event.target)) {
                suggestionsBox.classList.remove('visible');
            }
        });
    });
</script>

