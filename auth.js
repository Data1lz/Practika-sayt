// Функція для реєстрації
async function handleRegister(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'register');
    formData.append('name', document.getElementById('registerName').value);
    formData.append('email', document.getElementById('registerEmail').value);
    formData.append('password', document.getElementById('registerPassword').value);
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Реєстрація успішна!');
            closeModal('registerModal');
            openLoginModal();
        } else {
            alert(data.message || 'Помилка реєстрації');
        }
    } catch (error) {
        console.error('Помилка:', error);
        alert('Помилка при реєстрації');
    }
}

// Функція для входу
async function handleLogin(event) {
    event.preventDefault();
    
    const formData = new FormData();
    formData.append('action', 'login');
    formData.append('email', document.getElementById('loginEmail').value);
    formData.append('password', document.getElementById('loginPassword').value);
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Вхід успішний!');
            closeModal('loginModal');
            updateUserInterface(data.user);
        } else {
            alert(data.message || 'Помилка входу');
        }
    } catch (error) {
        console.error('Помилка:', error);
        alert('Помилка при вході');
    }
}

// Функція для оновлення інтерфейсу після входу
function updateUserInterface(user) {
    const authButtons = document.querySelector('.auth-buttons');
    authButtons.innerHTML = `
        <span class="text-white mr-4">Вітаємо, ${user.name}!</span>
        <button onclick="handleLogout()" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100">
            Вийти
        </button>
    `;
}

// Функція для виходу
async function handleLogout() {
    const formData = new FormData();
    formData.append('action', 'logout');
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Помилка:', error);
        alert('Помилка при виході');
    }
}
