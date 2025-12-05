/**
 * Script: Edit Course functionaliteit
 *
 * Functies:
 *  1. Initialisatie van Quill-editor + bestaande data
 *  2. Formulier submitten met validatie en JSON data-opbouw
 *  3. FAQ's beheren: toevoegen, verwijderen, synchroniseren met backend
 *  4. Documenten verwijderen (UI + markeren voor backend)
 *  5. Materiaal verwijderen (UI + multiselect synchronisatie)
 */

$(document).ready(function () {
  console.log("Edit Course jQuery geladen");

  // ------------------------------------
  // 1️ Variabelen initialiseren
  // ------------------------------------

  // Bestaande FAQ's worden via PHP in window.existingFaqs geïnjecteerd
  let faqs = window.existingFaqs || [];

  // ID’s van bestaande FAQ’s die verwijderd moeten worden
  let deletedFaqIds = [];

  // Voor documenten en materiaal
  let deletedDocuments = [];
  let deletedMaterialIDs = [];

  // ------------------------------------
  // 2️ Quill WYSIWYG-editor
  // ------------------------------------
  const quill = new Quill("#quilleditor", {
      theme: "snow",
      modules: { toolbar: "#quilltoolbar" },
  });

  // Quill → verborgen input bij opslaan
  $("#submitCourseBtn, #updateCourseBtn").on("click", function () {
      $("#beschrijving").val(quill.root.innerHTML);
  });

  // ------------------------------------
  // 3️ Verzendlogica voor update course
  // ------------------------------------
  $("#updateCourseBtn").on("click", function (e) {
      e.preventDefault();

      const form = $("#editCourseForm");

      // Update beschrijving
      $("#beschrijving").val(quill.root.innerHTML);

      // Oude hidden inputs verwijderen
      form.find('input[name="deletedFaqs"]').remove();
      form.find('input[name="faqs"]').remove();

      // FAQ-data opnieuw toevoegen als JSON
      form.append(`<input type="hidden" name="deletedFaqs" value='${JSON.stringify(deletedFaqIds)}'>`);
      form.append(`<input type="hidden" name="faqs" value='${JSON.stringify(faqs)}'>`);

      // Validatie van velden
      let titel = $('input[name="titel"]').val();
      let korteBeschrijving = $('textarea[name="korte_beschrijving"]').val();
      let categorie = $('select[name="categorie_id"]').val();

      let errors = [];
      if (!titel.trim()) errors.push("Titel is verplicht.");
      if (!korteBeschrijving.trim()) errors.push("Korte beschrijving is verplicht.");
      if (!categorie) errors.push("Selecteer een categorie.");

      if (errors.length > 0) {
          $("#errorMessage").remove();
          form.prepend(`
              <div id="errorMessage" class="alert alert-danger mt-2" role="alert">
                  <strong>Fouten gevonden:</strong><br>${errors.join("<br>")}
              </div>
          `);
          $("html, body").animate({ scrollTop: form.offset().top - 50 }, 400);
          return;
      }

      // Versturen
      form.off("submit");
      form.submit();
  });

  // ------------------------------------
  // 4️ FAQ Functionaliteit
  // ------------------------------------

  // FAQ toevoegen
  $("#saveFaqBtn").on("click", function () {
      let vraag = $("#faqQuestion").val().trim();
      let antwoord = $("#faqAnswer").val().trim();

      if (!vraag || !antwoord) {
          alert("Vul zowel de vraag als het antwoord in.");
          return;
      }

      // Nieuwe FAQ pushen
      faqs.push({
          FAQID: null, // nieuwe FAQ heeft nog geen ID
          vraag,
          antwoord,
      });

      // Toevoegen aan UI
      $("#faqList").append(`
          <div class="col-12">
              <div class="bg-body p-3 p-sm-4 border rounded">
                  <div class="d-sm-flex justify-content-sm-between align-items-center mb-2">
                      <h6 class="mb-0">${vraag}</h6>
                      <div class="align-middle">
                          <button type="button" class="btn btn-sm btn-danger-soft btn-round mb-0 delete-faq">
                              <i class="fas fa-fw fa-times"></i>
                          </button>
                      </div>
                  </div>
                  <p>${antwoord}</p>
              </div>
          </div>
      `);

      // Modal sluiten
      bootstrap.Modal.getInstance(document.getElementById("addQuestion")).hide();

      // Velden resetten
      $("#faqQuestion").val("");
      $("#faqAnswer").val("");
  });

  // FAQ verwijderen
  $(document).on("click", ".delete-faq", function () {
      const faqId = $(this).data("id");

      // Als bestaande FAQ → opslaan voor backend delete
      if (faqId) deletedFaqIds.push(faqId);

      // Index bepalen en verwijderen uit array
      const index = $(this).closest(".col-12").index();
      faqs.splice(index, 1);

      // UI verwijderen
      $(this).closest(".col-12").remove();
  });

  // ------------------------------------
  // 5️ Document verwijderen
  // ------------------------------------
  $(document).on("click", ".delete-doc", function () {
      const docId = $(this).data("id");

      if (confirm("Weet je zeker dat je dit document wilt verwijderen?")) {
          deletedDocuments.push(docId);
          $("#deletedDocuments").val(JSON.stringify(deletedDocuments));
          $(this).closest("li").remove();
      }
  });

  // ------------------------------------
  // 6️ Materiaal verwijderen
  // ------------------------------------
  $(document).on("click", ".delete-material", function () {
      const matId = $(this).data("id");

      if (confirm("Verwijder dit materiaal?")) {
          deletedMaterialIDs.push(matId);

          // UI verwijderen
          $(this).closest("div").remove();

          // Ook uit multiselect halen
          $(`select[name='material_ids[]'] option[value='${matId}']`).prop("selected", false);
      }
  });

  // Vóór submit materiaal-deletes opslaan
  $("#updateCourseBtn").on("click", function () {
      $("#DeletedMaterialIDs").val(JSON.stringify(deletedMaterialIDs));
  });
});
