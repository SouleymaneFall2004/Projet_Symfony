
let currentPage = 1;
let totalPages = 1; // Définir un total initial

// Fonction pour charger les articles depuis le backend
function loadDette(page) {
    fetch(`/api/dette?page=${page}`)
        .then(response => response.json())
        .then(data => {

            displaydettes(data.dettes);

            totalPages = data.pagination.total_pages;
            currentPage = data.pagination.current_page;
            updatePagination(currentPage, totalPages); // Mettre à jour la pagination avec la nouvelle page
        })
        .catch(error => console.error('Erreur:', error));
}


function displaydettes(dettes) {
    const tbody = document.getElementById('dettesBody');

    tbody.innerHTML = ''; // Réinitialiser le contenu du tableau

    dettes.forEach(dette => {
        const row = document.createElement('tr');
        row.classList.add('hover:bg-gray-100', 'border-b', 'border-gray-200'); // Style des lignes

        row.innerHTML = `
           
            <td class="py-3 px-6 text-left  text-red-700">${dette.id}</td>
            <td class="py-3 px-6 text-left  text-red-700">${dette.montant}</td>
            <td class="py-3 px-6 text-left  text-red-700">${dette.client}</td>
            <td class="py-3 px-6 text-left  text-red-700">${dette.dateCreation}</td>
            <td class="py-3 px-6 text-left  text-red-700">${dette.statut}</td>
          `;
        tbody.appendChild(row);
    });
}

loadDette(currentPage)