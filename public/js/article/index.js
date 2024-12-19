let currentPage = 1;
let totalPages = 1; // Définir un total initial

// Fonction pour charger les articles depuis le backend
function loadArticles(page) {
    fetch(`/api/article?page=${page}`)
        .then(response => response.json())
        .then(data => {

            displayArticles(data.articles);

            totalPages = data.pagination.total_pages;
            currentPage = data.pagination.current_page;
            updatePagination(page, totalPages, currentPage); // Mettre à jour la pagination avec la nouvelle page
        })
        .catch(error => console.error('Erreur:', error));
}

// Fonction pour afficher les articles dans le tableau
function displayArticles(articles) {
    const tbody = document.getElementById('articlesBody');
    tbody.innerHTML = ''; // Réinitialiser le contenu du tableau

    articles.forEach(article => {
        const row = document.createElement('tr');
        console.log(article);
        row.innerHTML = `
            <td class="py-3 px-6 text-left text-gray-700">${article.libelle}</td>
            <td class="py-3 px-6 text-left text-gray-700">${article.prix}</td>
            <td class="py-3 px-6 text-left text-gray-700">${article.qteStock}</td>
        `;
        tbody.appendChild(row);
    });
}

// Fonction pour mettre à jour la pagination
function updatePagination(page, totalPages, currentPpage) {
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');

    prevButton.disabled = page === 1;
    nextButton.disabled = page >= totalPages;

    prevButton.onclick = () => {
        if (currentPage > 1) {

            currentPage--;
            loadArticles(currentPage);
        }
    };

    nextButton.onclick = () => {
        console.log("hello");
        console.log(totalPages);
        if (currentPage < totalPages) {

            currentPage++;
            loadArticles(currentPage);
        }
    };
}

// Charger les articles au démarrage
loadArticles(currentPage);