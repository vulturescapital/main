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



$(document).ready(function() {
    $('.custom-select-trigger').on('click', function(e) {
        e.stopPropagation(); // Prevent bubbling to avoid unintended closes
        // Toggle 'show' class to control visibility and transformations
        $('.custom-options').toggleClass('show').css({
            transform: $('.custom-options').hasClass('show') ? 'scaleY(1)' : 'scaleY(0)',
            visibility: $('.custom-options').hasClass('show') ? 'visible' : 'hidden',
            maxHeight: $('.custom-options').hasClass('show') ? '500px' : '0'
        });
    });

    $('.custom-option').on('click', function(e) {
        e.stopPropagation(); // Prevent bubbling
        $('.custom-option').removeClass('selected');
        $(this).addClass('selected');
        // Update display and close dropdown
        updateSelectedCategoryDisplay();
        $('.custom-options').removeClass('show').css('transform', 'scaleY(0)');
    });

    // Close dropdown if clicking outside
    $(document).on('click', function() {
        $('.custom-options').removeClass('show').css('transform', 'scaleY(0)');
    });

    function updateSelectedCategoryDisplay() {
        var selected = $('.custom-option.selected').not('[data-value="all"]').text();
        $('#selected-category').text(selected ? 'Selected category: ' + selected : 'Selected category: None');
    }
});

