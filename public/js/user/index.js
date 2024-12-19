let currentPage = 1;
let totalPages = 1; // Définir un total initial

// Fonction pour charger les articles depuis le backend
function loadusers(page) {
    fetch(`/api/user?page=${page}`)
        .then(response => response.json())
        .then(data => {

            displayUsers(data.users);

            totalPages = data.pagination.total_pages;
            currentPage = data.pagination.current_page;
            updatePagination(page, totalPages, currentPage); // Mettre à jour la pagination avec la nouvelle page
        })
        .catch(error => console.error('Erreur:', error));
}

// Fonction pour afficher les articles dans le tableau
function displayUsers(users) {
    const tbody = document.getElementById('usersBody');
    tbody.innerHTML = ''; // Réinitialiser le contenu du tableau

    users.forEach(user => {
        const row = document.createElement('tr');
        console.log(user);
        row.innerHTML = `
            <td class="py-3 px-6 text-left text-gray-700">${user.id}</td>
            <td class="py-3 px-6 text-left text-gray-700">${user.email}</td>
            <td class="py-3 px-6 text-left text-gray-700">${user.login}</td>
           
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
            loadusers(currentPage);
        }
    };

    nextButton.onclick = () => {
        console.log("hello");
        console.log(totalPages);
        if (currentPage < totalPages) {

            currentPage++;
            loadusers(currentPage);
        }
    };
}

// Charger les articles au démarrage
loadusers(currentPage);