document.addEventListener('DOMContentLoaded', function () {
    const searchToggle = document.getElementById('search-toggle');
    const searchOverlay = document.getElementById('search-overlay');
    const closeSearch = document.getElementById('close-search');
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');

    searchToggle.addEventListener('click', function () {
        searchOverlay.classList.remove('d-none');
        searchInput.focus();
    });

    closeSearch.addEventListener('click', function () {
        searchOverlay.classList.add('d-none');
    });

    searchInput.addEventListener('input', function () {
        const query = searchInput.value.trim();

        if (query.length > 2) {
            fetch(`processes/search.php?query=${encodeURIComponent(query)}`, {
                headers: {
                    'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(text);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        searchResults.innerHTML = `
                                <h2 class="results-header">${data.length} RESULT${data.length > 1 ? 'S' : ''} FOR "${query}"</h2>
                                <h3 class="results-section">ARTICLES</h3>
                                <hr>
                            `;
                        data.forEach(article => {
                            const resultItem = document.createElement('div');
                            resultItem.classList.add('search-result-item');
                            resultItem.innerHTML = `
                                    <a href="article.php?id=${article.id}">
                                        <h4 class="article-title-over">${article.name}</h4>
                                        <p class="article-author">BY ${article.author}</p>
                                    </a>
                                `;
                            searchResults.appendChild(resultItem);
                        });
                    } else {
                        searchResults.innerHTML = '<p>No results found</p>';
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                    searchResults.innerHTML = `<p>Error fetching results: ${error.message}</p>`;
                });
        } else {
            searchResults.innerHTML = '';
        }
    });

    // Category selector handling
    const customSelectTrigger = document.querySelector('.custom-select-trigger');
    const customOptions = document.querySelector('.custom-options');
    const customOptionsArray = document.querySelectorAll('.custom-option');

    customSelectTrigger.addEventListener('click', function (e) {
        e.stopPropagation();
        customOptions.classList.toggle('open');
    });

    customOptionsArray.forEach(function(option) {
        option.addEventListener('click', function () {
            customSelectTrigger.textContent = this.textContent;
            customOptions.classList.remove('open');
        });
    });

    document.addEventListener('click', function (e) {
        if (!customOptions.contains(e.target) && customOptions.classList.contains('open')) {
            customOptions.classList.remove('open');
        }
    });
});