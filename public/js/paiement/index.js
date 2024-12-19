
let currentPage = 1;
let totalPages = 1; // Définir un total initial

const currentUrl = window.location.href;
const match = currentUrl.match(/id=(\d+)/);
console.log(match);
const id = match ? match[1] : null;
function LoadPaiements(page) {
    fetch(`/api/paiement/id=${id}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            displayPaiements(data.paiements);
            totalPages = data.pagination.total_pages;
            currentPage = data.pagination.current_page;
            updatePagination(currentPage, totalPages); // Mettre à jour la pagination avec la nouvelle page
        })
        .catch(error => console.error('Erreur:', error));
}

function displayPaiements(paiements) {
    const paiementsBody = document.getElementById('paiementsBody');
    paiementsBody.innerHTML = '';

    paiements.forEach(paiement => {
        const row = document.createElement('tr');
        row.classList.add('hover:bg-gray-100', 'border-b', 'border-gray-200'); // Style des lignes

        row.innerHTML = `
            <td class="py-3 px-6 text-left text-gray-700">${paiement.id}</td>
            <td class="py-3 px-6 text-left text-gray-700">${paiement.montant}</td>
            <td class="py-3 px-6 text-left text-gray-700">${paiement.client}</td>
            <td class="py-3 px-6 text-left text-gray-700">${paiement.dateCreation}</td>
    
        `;
        paiementsBody.appendChild(row);
    });
}

LoadPaiements(currentPage);