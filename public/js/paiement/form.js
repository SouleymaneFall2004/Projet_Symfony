const currentUrl = window.location.href;
const match = currentUrl.match(/id=(\d+)/);
console.log(match);
const id = match ? match[1] : null;
const form = document.getElementById('form');

form.addEventListener('submit', async (e) => {
    // Empêcher le comportement par défaut du formulaire (soumission du formulaire)
    e.preventDefault();
    const montant = document.getElementById('Montant').value;
    const formData = new FormData();
    formData.append('montant', montant);
    try {
        const response = await fetch(`http://127.0.0.1:8000/paiement/store/id=${id}`, {
            method: 'POST',
            body: formData
        });
        if (response.ok) {
            alert('Paiement ajoute avec succes !');
            // window.location.href = 'http://127.0.0.1:8000/paiementList';
        } else {
            const errorText = await response.text();
            console.error('Server Error Response:', errorText);
            alert('Erreur lors de l\'ajout du paiement: ' + errorText);
        }
    } catch (error) {
        console.error(error);
    }
});