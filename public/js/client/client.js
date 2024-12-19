let currentPage = 1;
let totalPages = 1; // Définir un total initial

let search = document.getElementById("form-search")
let searchClient = ""
// Fonction pour charger les articles depuis le backend
function loadClients(page) {
    fetch(`/api/client?page=${page}&search=${searchClient}`)
        .then(response => response.json())
        .then(data => {

            displayArticles(data.clients);

            totalPages = data.pagination.total_pages;
            currentPage = data.pagination.current_page;
            updatePagination(currentPage, totalPages); // Mettre à jour la pagination avec la nouvelle page
        })
        .catch(error => console.error('Erreur:', error));
}

// Fonction pour afficher les articles dans le tableau
function displayArticles(clients) {
    const tbody = document.getElementById('articlesBody');
    tbody.innerHTML = ''; // Réinitialiser le contenu du tableau

    clients.forEach(client => {
        const row = document.createElement('tr');
        row.classList.add('hover:bg-gray-100', 'border-b', 'border-gray-200'); // Style des lignes

        row.innerHTML = `
            <td class="py-3 px-6 text-left text-gray-700">
                ${client.image ? `<img src="image/${client.image}" alt="user photo" class="w-10 h-10 object-cover ">` : ''}
            </td>
            <td class="py-3 px-6 text-left  text-red-700">${client.id}</td>
            <td class="py-3 px-6 text-left text-gray-700">${client.telephone}</td>
            <td class="py-3 px-6 text-left text-gray-700">${client.adresse}</td>
            <td class="py-3 px-6 text-left text-gray-700">${client.surnom}</td>
            <td class="py-3 px-6 text-left text-gray-700">${client.email}</td>
            <td class="py-3 px-6 text-left text-gray-700">${client.users ? client.users.login : ''}</td>
            <td class="py-3 mx-auto px-6 text-center">
                <button class="bg-red-500 text-white py-1 px-3 rounded hover:bg-blue-600" 
                        onclick="handleButtonClick(${client.id})">
                    Action
                </button>
            </td>
            `;
        tbody.appendChild(row);
    });
}


function handleButtonClick(id) {
    window.location.href = `/client/dette/id=${id}`;
}

// Fonction pour mettre à jour la pagination
function updatePagination(page, totalPages) {
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');

    prevButton.disabled = page === 1;
    nextButton.disabled = page >= totalPages;

    prevButton.onclick = () => {
        if (currentPage > 1) {

            currentPage--;
            loadClients(currentPage);
        }
    };

    nextButton.onclick = () => {

        // console.log(totalPages);
        if (currentPage < totalPages) {

            currentPage++;
            loadClients(currentPage);
        }
    };
}

search.addEventListener("input", function () {
    searchClient = search.value;
    loadClients(1)
})




// Charger les articles au démarrage
loadClients(currentPage);
