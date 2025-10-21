$(document).ready(function() {
    console.log('jQuery status:', typeof jQuery);
    $('form').on('submit', function(e) 
    {
        let jsonFaqs = JSON.stringify(faqs);
        $(this).append(`<input type="hidden" name="faqs" value='${jsonFaqs}'>`);

        let categorie = $('select[name="categorie_id"]').val();
        let titel = $('input[name="titel"]').val();
        let beschrijving = $('textarea[name="korte_beschrijving"]').val();
        let foto = $('input[name="foto"]').val();

        let errors = [];

        if (!titel.trim()) {
            errors.push('Titel is verplicht.');
        }

        if (!beschrijving.trim()) {
            errors.push('Korte beschrijving is verplicht.');
        }

        if (!categorie || categorie.trim() === '') {
            errors.push('Selecteer een categorie.');
            $('.choices__inner').css('border', '2px solid red');
        } else {
            $('.choices__inner').css('border', '');
        }

        if (!foto.trim()) {
            errors.push('Kies een foto.');
        }

        if (errors.length > 0) {
            e.preventDefault();

            // Verwijder vorige foutmelding (als die er is)
            $('#errorMessage').remove();

            // Voeg een melding toe boven het formulier
            $('form').prepend(`
                <div id="errorMessage" class="alert alert-danger mt-2" role="alert">
                    <strong>Er zijn fouten gevonden:</strong><br>
                    ${errors.join('<br>')}
                </div>
            `);

            // Scroll naar boven van het formulier
            $('html, body').animate({ scrollTop: $('form').offset().top - 50 }, 400);

            return false;
        }
      

    });



    //Quill initialiseren
    var quill = new Quill('#quilleditor', {
        theme: 'snow',
        modules: {
            toolbar: '#quilltoolbar'
        }
    });

    //Bij formulier verzenden: inhoud van Quill in verborgen input zetten
    $('form').on('submit', function () {
        var html = quill.root.innerHTML;
        $('#beschrijving').val(html); // <-- zet inhoud in hidden input
    });


    // ----------------------------
    //  FAQ LOGICA
    // ----------------------------
    let faqs = [];

    // Voeg nieuwe FAQ toe
    $('#saveFaqBtn').on('click', function() {
        let vraag = $('#faqQuestion').val().trim();
        let antwoord = $('#faqAnswer').val().trim();

        if (!vraag || !antwoord) {
            alert('Vul zowel de vraag als het antwoord in.');
            return;
        }

        // Voeg toe aan array
        faqs.push({ vraag, antwoord });

        // Maak FAQ HTML
        let faqHtml = `
            <div class="col-12">
                <div class="bg-body p-3 p-sm-4 border rounded">
                    <div class="d-sm-flex justify-content-sm-between align-items-center mb-2">
                        <h6 class="mb-0">${vraag}</h6>
                        <div class="align-middle">
                            <button type="button" class="btn btn-sm btn-danger-soft btn-round mb-0 deleteFaq">
                                <i class="fas fa-fw fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <p>${antwoord}</p>
                </div>
            </div>
        `;

        // Voeg visueel toe
        $('.bg-light.border.rounded.p-2.p-sm-4 .row.g-4').append(faqHtml);

        // Modal sluiten
        let modal = bootstrap.Modal.getInstance(document.getElementById('addQuestion'));
        modal.hide();

        // Reset form
        $('#faqQuestion').val('');
        $('#faqAnswer').val('');
    });

    // Verwijder FAQ
    $(document).on('click', '.deleteFaq', function() {
        let index = $(this).closest('.col-12').index();
        faqs.splice(index, 1); // verwijder uit array
        $(this).closest('.col-12').remove(); // verwijder uit DOM
    });

    // Voeg alle faqs toe als JSON bij form-submit
    $('form').on('submit', function() {
        let jsonFaqs = JSON.stringify(faqs);
        $(this).append(`<input type="hidden" name="faqs" value='${jsonFaqs}'>`);
    });

});
