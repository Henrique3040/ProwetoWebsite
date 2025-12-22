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

$(document).ready(function () {

    $('#rating i').on('click', function () {

        // 1️⃣ Waarde van aangeklikte ster
        let value = $(this).data('value');

        // 2️⃣ Cursus-ID uit parent container
        let course = $('#rating').data('course');

        // 3️⃣ Verstuur rating naar backend
        $.ajax({
            url: 'ajax/rate.php',
            method: 'POST',
            dataType: 'json',
            data: {
                course_id: course,
                rating: value
            },
            success: function (data) {
                if (data.success) {

                    // Reset alle sterren
                    $('#rating i')
                        .removeClass('bi-star-fill text-warning')
                        .addClass('bi-star');

                    // Vul sterren t/m aangeklikte waarde
                    $('#rating i').each(function () {
                        if ($(this).data('value') <= value) {
                            $(this).addClass('bi-star-fill text-warning');
                        }
                    });
                }
            }
        });

    });

});
