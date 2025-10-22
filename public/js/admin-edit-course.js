$(document).ready(function() {
    console.log('Edit Course jQuery geladen');

    // ----------------------------
    // 1️ Init variabelen
    // ----------------------------
    let faqs = window.existingFaqs || []; // deze kun je via PHP injecteren
    let deletedFaqIds = [];

    // ----------------------------
    // 2️ Quill initialiseren + bestaande content
    // ----------------------------
    var quill = new Quill('#quilleditor', {
        theme: 'snow',
        modules: {
            toolbar: '#quilltoolbar'
        }
    });

    // Bij opslaan: zet de HTML-inhoud in de verborgen input
    $('#submitCourseBtn, #updateCourseBtn').on('click', function(e) {
        $('#beschrijving').val(quill.root.innerHTML);
    });

    // ----------------------------
    // 3️ Submit-knop logica
    // ----------------------------
    $('#updateCourseBtn').on('click', function(e) {
        e.preventDefault();

        let form = $('#editCourseForm');

        // Zet Quill HTML in hidden input
        $('#beschrijving').val(quill.root.innerHTML);

        // FAQ's updaten
        form.find('input[name="deletedFaqs"]').remove();
        form.find('input[name="faqs"]').remove();
    
        // Voeg toe aan form
        form.append(`<input type="hidden" name="deletedFaqs" value='${JSON.stringify(deletedFaqIds)}'>`);
        form.append(`<input type="hidden" name="faqs" value='${JSON.stringify(faqs)}'>`);

        // Validatie
        let titel = $('input[name="titel"]').val();
        let korteBeschrijving = $('textarea[name="korte_beschrijving"]').val();
        let categorie = $('select[name="categorie_id"]').val();

        let errors = [];
        if (!titel.trim()) errors.push('Titel is verplicht.');
        if (!korteBeschrijving.trim()) errors.push('Korte beschrijving is verplicht.');
        if (!categorie) errors.push('Selecteer een categorie.');

        if (errors.length > 0) {
            $('#errorMessage').remove();
            form.prepend(`
                <div id="errorMessage" class="alert alert-danger mt-2" role="alert">
                    <strong>Fouten gevonden:</strong><br>${errors.join('<br>')}
                </div>
            `);
            $('html, body').animate({ scrollTop: form.offset().top - 50 }, 400);
            return;
        }

        // Versturen
        form.off('submit');
        form.submit();
    });

    // ----------------------------
    // 4️ FAQ logica (zelfde als create)
    // ----------------------------
    $('#saveFaqBtn').on('click', function() {
        let vraag = $('#faqQuestion').val().trim();
        let antwoord = $('#faqAnswer').val().trim();

        if (!vraag || !antwoord) {
            alert('Vul zowel de vraag als het antwoord in.');
            return;
        }

        faqs.push({
            FAQID: null, // nieuwe FAQ
            vraag: $('#faqQuestion').val().trim(),
            antwoord: $('#faqAnswer').val().trim()
        });
        

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
        $('#faqList').append(faqHtml);
        bootstrap.Modal.getInstance(document.getElementById('addQuestion')).hide();
        $('#faqQuestion').val('');
        $('#faqAnswer').val('');
    });

    // FAQ verwijderen
    $(document).on('click', '.delete-faq', function () {
        const faqId = $(this).data('id');
        if (faqId) deletedFaqIds.push(faqId); // markeer voor backend
    
        // Verwijder visueel uit lijst en faqs-array
        const index = $(this).closest('.col-12').index();
        faqs.splice(index, 1);
        $(this).closest('.col-12').remove();
    });
    
});
