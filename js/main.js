const themeToggleButton = document.getElementById('theme-toggle');
const iconMoon = themeToggleButton.querySelector('.fa-moon');
const iconSun = themeToggleButton.querySelector('.fa-sun');

const updateIcons = () => {
    if (document.body.classList.contains('dark-mode')) {
        iconSun.style.display = 'inline';
        iconMoon.style.display = 'none';
    } else {
        iconSun.style.display = 'none';
        iconMoon.style.display = 'inline';
    }
};

themeToggleButton.addEventListener('click', function() {
    document.body.classList.toggle('dark-mode');
    document.body.classList.toggle('light-mode');
    updateIcons(); // Update icons each time the button is clicked

    // Save the current theme to localStorage
    if (document.body.classList.contains('dark-mode')) {
        localStorage.setItem('theme', 'dark');
    } else {
        localStorage.setItem('theme', 'light');
    }
});

// Check for saved theme preference and apply it
const currentTheme = localStorage.getItem('theme') || 'light';
if (currentTheme === 'dark') {
    document.body.classList.add('dark-mode');
    document.body.classList.remove('light-mode');
} else {
    document.body.classList.add('light-mode');
    document.body.classList.remove('dark-mode');
}
updateIcons();
