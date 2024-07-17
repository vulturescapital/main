// main.js
document.addEventListener('DOMContentLoaded', (event) => {
  const themeToggleButton = document.getElementById('theme-toggle');
  themeToggleButton.addEventListener('click', () => {
    const isDarkMode = document.body.classList.toggle('dark-mode');
    document.body.classList.toggle('light-mode', !isDarkMode);
    themeToggleButton.textContent = isDarkMode ? 'Light Mode' : 'Dark Mode';
    localStorage.setItem('theme', isDarkMode ? 'dark' : 'light');
  });

  // Check for saved user preference, if any, on page load
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme) {
    document.body.classList.add(savedTheme === 'dark' ? 'dark-mode' : 'light-mode');
    themeToggleButton.textContent = savedTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
  } else {
    // If there is no saved preference, default to light mode
    document.body.classList.add('light-mode');
  }
});
