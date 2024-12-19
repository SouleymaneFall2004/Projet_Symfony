

// const currentUrl = window.location.href;
// const urlParts = new URL(currentUrl);
// const searchParams = urlParts.searchParams;
// const id = searchParams.get('id');

const currentUrl = window.location.href;
const match = currentUrl.match(/id=(\d+)/);
console.log(match);
const id = match ? match[1] : null;
let currentPage = 1;
let totalPages = 1;
function loadDettes(page) {
    fetch(`/api/client/dette/id=${id}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            console.log(data)

            displayArticles(data.dettes);
            displayClient(data)
            totalPages = data.pagination.total_pages;
            currentPage = data.pagination.current_page;
            updatePagination(currentPage, totalPages); // Mettre à jour la pagination avec la nouvelle page
        })
        .catch(error => console.error('Erreur:', error));
}


function displayArticles(dettes) {
    const tbody = document.getElementById('detaildettesBody');

    tbody.innerHTML = ''; // Réinitialiser le contenu du tableau

    dettes.forEach(dette => {
        const row = document.createElement('tr');
        row.classList.add('hover:bg-gray-100', 'border-b', 'border-gray-200'); // Style des lignes

        row.innerHTML = `
           
            <td class="py-3 px-6 text-left  text-red-700">${dette.id}</td>
            <td class="py-3 px-6 text-left  text-red-700">${dette.montant}</td>
            <td class="py-3 px-6 text-left  text-red-700">${dette.montantVerser}</td>
            <td class="py-3 px-6 text-left  text-red-700">${dette.dateCreation}</td>
            <td class="py-3 px-6 text-left  text-red-700">${dette.statut}</td>
             <td class="py-3 px-6 text-left  text-red-700">
                <button class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600" 
                        onclick="handleButtonClick(${dette.id})">
                    Action
                </button>
                 <button class="bg-blue-500 text-white py-1 px-3 rounded hover:bg-blue-600" 
                        onclick="handleButtonClick1(${dette.id})">
                    Action
                </button>

             </td>
          `;
        tbody.appendChild(row);
    });
}

function displayClient(data) {
    clientName = document.getElementById('clientName');
    Telephone = document.getElementById('telephone')
    adresse = document.getElementById('adresse')
    montantVerser = document.getElementById('montantVerser')
    clientName.textContent = data.dettes[0].client;
    Telephone.textContent = data.dettes[0].telephone
    adresse.textContent = data.dettes[0].adresse
    montantVerser.textContent = data.dettes[0].montantVerser

}

function handleButtonClick(id) {
    window.location.href = `/client/dette/paiement/id=${id}`;
}

function handleButtonClick1(id) {
    window.location.href = `/ListPaiement/id=${id}`;
}

function updatePagination(page, totalPages) {
    const prevButton = document.getElementById('prev');
    const nextButton = document.getElementById('next');

    prevButton.disabled = page === 1;
    nextButton.disabled = page >= totalPages;

    prevButton.onclick = () => {
        if (currentPage > 1) {

            currentPage--;
            loadDettes(currentPage);
        }
    };

    nextButton.onclick = () => {

        // console.log(totalPages);
        if (currentPage < totalPages) {

            currentPage++;
            loadDettes(currentPage);
        }
    };
}


// Charger les articles au démarrage
loadDettes(currentPage);

