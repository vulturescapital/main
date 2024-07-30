$(document).ready(function () {
    function updateSelectedCategoryDisplay() {
        var selected = $('.custom-option.selected').not('[data-value="all"]').text();
        $('#selected-category').text(selected ? 'Selected category: ' + selected : 'Selected category: None');
    }

    function filterArticles(categoryId) {
        $('#article-container .col-lg-3').hide(); // Hide all articles

        if (categoryId === 'all') {
            $('#article-container .col-lg-3').show(); // Show all articles if "all" is selected
        } else {
            $('#article-container .col-lg-3[data-category="' + categoryId + '"]').show(); // Show articles of the selected category
        }
    }
});
