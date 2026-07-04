const form = document.getElementById("registroForm");
const successMsg = document.getElementById("successMsg");
const termsError = document.getElementById("termsError");

form.addEventListener("submit", function(e) {
    e.preventDefault();

    let valido = true;

    
    if (form.nombre.value.trim().length <= 2) {
        form.querySelector('[data-field="nombre"]').classList.add("has-error");
        valido = false;
    }

  
    const email = form.email.value.trim();
    if (!email.includes("@") || !email.includes(".")) {
        form.querySelector('[data-field="email"]').classList.add("has-error");
        valido = false;
    }

    if (form.password.value.length < 8) {
        form.querySelector('[data-field="password"]').classList.add("has-error");
        valido = false;
    }

    
    if (form.password.value !== form.password2.value) {
        form.querySelector('[data-field="password2"]').classList.add("has-error");
        valido = false;
    }

  
    if (!form.terms.checked) {
        termsError.style.display = "block";
        valido = false;
    } else {
        termsError.style.display = "none";
    }

    if (valido) {
        successMsg.classList.add("show");
        form.reset();
    }
});