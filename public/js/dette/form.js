function loadClients() {
    fetch(`/api/client`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('Client');
            data.clients.forEach(client => {
                const option = document.createElement('option');
                option.value = client.id;
                option.text = client.surnom;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur:', error));
}

function loadArticles() {
    fetch(`/api/article`)
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('Article');
            data.articles.forEach(article => {
                const option = document.createElement('option');
                option.value = article.id;
                option.text = article.libelle;
                select.appendChild(option);
            });
        })
        .catch(error => console.error('Erreur:', error));
}

function getArticleById(articleId) {
    fetch(`/api/article/${articleId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Article récupéré:', data);
            // Vous pouvez maintenant travailler avec les données de l'article
        })
        .catch(error => console.error('Erreur lors de la récupération de l\'article:', error));
}

async function loadPanier() {
    try {
        // Récupérer le panier via l'API
        const response = await fetch('/api/panier');
        if (!response.ok) throw new Error('Erreur lors du chargement du panier.');

        const panier = await response.json();

        // Générer les lignes HTML
        const dettesBody = document.getElementById('dettesBody');
        dettesBody.innerHTML = '';
        panier.forEach(item => {
            const row = `<tr>
                <td>${item.libelle}</td>
                <td>${item.quantity}</td>
                <td>${item.prix}</td>
            </tr>`;
            dettesBody.innerHTML += row;
        });
    } catch (error) {
        console.error(error);
        alert('Impossible de charger le panier.');
    }
}



// Sélectionner l'élément de formulaire
const form = document.getElementById('form');
// const formAddArticle = document.getElementById('formAddArticle');
// Ajouter un écouteur d'événement à l'élément de formulaire
form.addEventListener('submit', async (e) => {
    // Empêcher le comportement par défaut du formulaire (soumission du formulaire)
    e.preventDefault();

    // Récupérer les valeurs saisies par l'utilisateur
    const clientId = document.getElementById('Client').value;
    const articleId = document.getElementById('Article').value;
    const montant = document.getElementById('Montant').value;

    // Effectuer des actions avec les valeurs saisies
    console.log(clientId, articleId, montant);
    getArticleById(articleId);


    const formData = new FormData();
    formData.append('clientId', clientId);
    formData.append('articleId', articleId);
    formData.append('montant', montant);

    console.log("je suis la")
    console.log(formData);

    // Soumettre le formulaire à l'aide de l'API fetch
    try {
        const response = await fetch("http://127.0.0.1:8000/dettestore", {
            method: 'POST',
            body: formData
        })
        if (response.ok) {
            alert("dette ajouté avec succès !");
            loadPanier();
        } else {
            const errorText = await response.text();
            console.error("Server Error Response:", errorText);
            alert("Erreur lors de l'ajout du client: " + errorText);
        }
    }
    catch (error) {
        console.error('Erreur lors de la soumission du formulaire:', error);
    }
});




loadPanier();

loadArticles();


loadClients();