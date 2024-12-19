document.addEventListener("DOMContentLoaded", function () {
    const userForm = document.getElementById('UserForm');
    const checkbox = document.getElementById('CreateUser');
    const emailField = document.getElementById('email');
    const loginField = document.getElementById('login');
    const passwordField = document.getElementById('password');
    const FileKeyField = document.getElementById('FileKey');
    const form = document.getElementById('form');



    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append("surnom", document.getElementById("surnom").value);
        formData.append("telephone", document.getElementById("telephone").value);
        formData.append("adresse", document.getElementById("adresse").value);
        formData.append("CreateUser", document.getElementById("CreateUser").checked);

        if (document.getElementById("CreateUser").checked) {
            formData.append("email", emailField.value);
            formData.append("login", loginField.value);
            formData.append("password", passwordField.value);
            formData.append("fileKey", FileKeyField.files[0]); // Ajout du fichier
        }

        try {
            console.log(formData);
            const response = await fetch("http://127.0.0.1:8000/clientstore", {
                method: "POST",
                body: formData,

            });

            if (response.ok) {
                alert("Client ajouté avec succès !");
                window.location.href = "http://127.0.0.1:8000/clientList";
            } else {
                const errorText = await response.text();
                console.error("Server Error Response:", errorText);
                alert("Erreur lors de l'ajout du client: " + errorText);
            }
        } catch (error) {
            console.error("Erreur :", error);
            alert("Une erreur s'est produite.");
        }
    });


    function toggleUser(checkbox) {
        if (checkbox.checked) {

            userForm.classList.remove('hidden');
            emailField.required = true;
            loginField.required = true;
            passwordField.required = true;
        } else {
            userForm.classList.add('hidden');
            emailField.removeAttribute('required');
            emailField.removeAttribute('data-val-required');
            loginField.removeAttribute('required');
            passwordField.removeAttribute('required');
        }
    }
    toggleUser(checkbox);

    checkbox.addEventListener('change', function () {
        toggleUser(checkbox);
    });
});
