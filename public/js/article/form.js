
const form = document.getElementById('form');

form.addEventListener('submit', async (e) => {
    // Empêcher le comportement par défaut du formulaire (soumission du formulaire)
    e.preventDefault();
    const libelle = document.getElementById('libelle').value;
    const prix = document.getElementById('prix').value;
    const qteStock = document.getElementById('qte').value;
    const formData = new FormData();
    formData.append('libelle', libelle);
    formData.append('prix', prix);
    formData.append('qteStock', qteStock);
    try {
        const response = await fetch('http://127.0.0.1:8000/article/add', {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            alert('Article ajoute avec succes !');
            window.location.href = 'http://127.0.0.1:8000/article';
        } else {
            const errorText = await response.text();
            console.error('Server Error Response:', errorText);
            alert('Erreur lors de l\'ajout de l\'article: ' + errorText);
        }
    } catch (error) {
        console.error(error);
    }
});