import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggle-button');
    const postTitle = document.getElementById('posts-title');
    const userPosts = document.getElementById('user-posts');
    const likedPosts = document.getElementById('liked-posts');

    toggleButton.addEventListener('click', function () {
        if (this.dataset.current === 'user') {
            this.dataset.current = 'liked';
            this.textContent = '❤️';
            postTitle.textContent = 'LIKED POSTS:';
            userPosts.style.display = 'none';
            likedPosts.style.display = 'block';
        } else {
            this.dataset.current = 'user';
            this.textContent = '👤';
            postTitle.textContent = 'USER POSTS:';
            userPosts.style.display = 'block';
            likedPosts.style.display = 'none';
        }
    });
});
