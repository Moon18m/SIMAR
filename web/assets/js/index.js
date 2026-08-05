    const barra = document.getElementById("lifeProgress");
    const texto = document.getElementById("lifeText");

    let vida = 100; 

    function actualizarVida() {

        barra.style.width = vida + "%";
        texto.textContent = Math.round(vida) + "%";

        if (vida > 60) {
        barra.style.background = "#40ffdc";   // --c1 cyan
        } else if (vida > 30) {
            barra.style.background = "#0e587d";   // --c2 azul
        } else {
            barra.style.background = "#240047";   // --c4 morado
        }

    }

    actualizarVida();

    setInterval(() => {

        vida--;

        if (vida < 0) {
            vida = 100;
        }

        actualizarVida();

    }, 1000);