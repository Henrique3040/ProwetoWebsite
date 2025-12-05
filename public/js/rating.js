/**
 * Script: Cursusbeoordeling (sterretjes)
 *
 * Functionaliteiten:
 *  - Klikken op een ster verstuurt de rating naar de backend
 *  - Na succesvolle submit wordt de pagina herladen
 *
 * Vereisten:
 *  - #rating element met data-course attribuut
 *  - Ster-elementen <i> binnen #rating, elk met data-value attribuut (1-5)
 */

document.querySelectorAll('#rating i').forEach(star => {

    star.addEventListener('click', function () {
        // 1️⃣ Waarde van aangeklikte ster ophalen
        let value = this.dataset.value;

        // 2️⃣ Cursus-ID ophalen uit parent container
        let course = document.querySelector('#rating').dataset.course;

        // 3️⃣ Verstuur rating naar backend via POST
        fetch("rate.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `course_id=${course}&rating=${value}`
        })
        .then(res => res.json())
        .then(() => {
            // 4️⃣ Pagina herladen om nieuwe gemiddelde rating te tonen
            location.reload();
        });
    });

});
