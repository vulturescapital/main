document.getElementById('theme-toggle').addEventListener('click', function(event) {
  if (event.target.checked) {
    document.body.classList.add('dark-mode');
    document.body.classList.remove('light-mode');
    localStorage.setItem('theme', 'dark');
  } else {
    document.body.classList.add('light-mode');
    document.body.classList.remove('dark-mode');
    localStorage.setItem('theme', 'light');
  }
});

window.onload = function() {
  var theme = localStorage.getItem('theme');
  var themeToggle = document.getElementById('theme-toggle');
  if (theme === 'dark') {
    themeToggle.checked = true;
    document.body.classList.add('dark-mode');
  } else {
    themeToggle.checked = false;
    document.body.classList.add('light-mode');
  }
};
