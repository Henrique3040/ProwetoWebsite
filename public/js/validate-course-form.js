/**
 * Script: Cursus aanmaken / bewerken
 *
 * Functionaliteiten:
 *  - Quill editor initialiseren voor rijke tekstbeschrijving
 *  - Submit-knop: validatie + FAQ toevoegen + Quill HTML naar hidden input
 *  - FAQ logica: toevoegen en verwijderen van veelgestelde vragen
 *  - Voorkom dat step-knoppen (wizard) formulier submitten
 *
 * Vereisten:
 *  - jQuery
 *  - Bootstrap 5 (voor modals)
 *  - Quill editor
 */

$(document).ready(function () {
  console.log("jQuery status:", typeof jQuery);

  // ----------------------------
  // 1️⃣ Variabelen
  // ----------------------------
  let faqs = [];

  // ----------------------------
  // 2️⃣ Quill initialiseren
  // ----------------------------
  var quill = new Quill("#quilleditor", {
    theme: "snow",
    modules: {
      toolbar: "#quilltoolbar",
    },
  });

  // ----------------------------
  // 3️⃣ Submit-knop click event
  // ----------------------------
  $("#submitCourseBtn").on("click", function (e) {
    e.preventDefault();
    let form = $(this).closest("form");

    // Haal data op
    let categorie = $('select[name="categorie_id"]').val();
    let titel = $('input[name="titel"]').val();
    let beschrijving = $('textarea[name="korte_beschrijving"]').val();
    let foto = $('input[name="foto"]').val();

    // Zet Quill HTML in hidden input
    $("#beschrijving").val(quill.root.innerHTML);

    // Voeg FAQ's toe
    form.find('input[name="faqs"]').remove(); // verwijder bestaande hidden inputs
    let jsonFaqs = JSON.stringify(faqs)
      .replace(/'/g, "&apos;")
      .replace(/"/g, "&quot;");
    form.append(`<input type="hidden" name="faqs" value="${jsonFaqs}">`);

    // Validatie
    let errors = [];
    if (!titel.trim()) errors.push("Titel is verplicht.");
    if (!beschrijving.trim()) errors.push("Korte beschrijving is verplicht.");
    if (!categorie || categorie.trim() === "") {
      errors.push("Selecteer een categorie.");
      $(".choices__inner").css("border", "2px solid red");
    } else {
      $(".choices__inner").css("border", "");
    }
    if (!foto.trim()) errors.push("Kies een foto.");

    // Toon errors of submit
    if (errors.length > 0) {
      $("#errorMessage").remove();
      form.prepend(`
                <div id="errorMessage" class="alert alert-danger mt-2" role="alert">
                    <strong>Er zijn fouten gevonden:</strong><br>${errors.join(
                      "<br>"
                    )}
                </div>
            `);
      $("html, body").animate({ scrollTop: form.offset().top - 50 }, 400);
      return;
    }

    form[0].submit();
  });

  // ----------------------------
  // 4️⃣ FAQ logica
  // ----------------------------
  $("#saveFaqBtn").on("click", function () {
    let vraag = $("#faqQuestion").val().trim();
    let antwoord = $("#faqAnswer").val().trim();

    if (!vraag || !antwoord) {
      alert("Vul zowel de vraag als het antwoord in.");
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
    $("#faqList").append(faqHtml);

    // Modal sluiten
    let modal = bootstrap.Modal.getInstance(
      document.getElementById("addQuestion")
    );
    modal.hide();

    // Reset form
    $("#faqQuestion").val("");
    $("#faqAnswer").val("");
  });

  // Verwijder FAQ
  $(document).on("click", ".deleteFaq", function () {
    let index = $(this).closest(".col-12").index();
    faqs.splice(index, 1);
    $(this).closest(".col-12").remove();
  });

  // ----------------------------
  // 5️⃣ Zorg dat step-knoppen geen submit doen
  // ----------------------------
  $(".next-btn, .prev-btn").attr("type", "button");

  // ----------------------------
  // 6️⃣ Live video preview (YouTube)
  // ----------------------------
  $("input[name='video_link']").on("input", function () {
    let url = $(this).val().trim();
    let videoID = "";

    // Normale YouTube link
    if (url.includes("watch?v=")) {
      videoID = url.split("watch?v=")[1].split("&")[0];
    }

    // Verkorte youtu.be link
    else if (url.includes("youtu.be")) {
      videoID = url.split("youtu.be/")[1].split("?")[0];
    }

    // Bekijk of we een geldige ID hebben
    if (videoID) {
      $("#videoPreview").attr(
        "src",
        "https://www.youtube.com/embed/" + videoID
      );
    } else {
      $("#videoPreview").attr("src", "");
    }
  });
});
