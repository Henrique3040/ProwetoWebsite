/**
 * Script: Categoriebeheer frontend functionaliteit
 *
 * Functies:
 *  - Live icoon preview tijdens typen
 *  - Edit-modal vullen met data van geselecteerde categorie
 *  - Categorie verwijderen via hidden form + bevestiging
 */

$(document).ready(function () {
  
  /**
  * Live icoon preview
  * 
  * Wanneer de gebruiker een font-awesome icon class invult,
  * wordt het voorbeeld-element (#iconPreview) direct bijgewerkt.
  */
  $('#categorieIcon').on('input', function () {
    const iconClass = $(this).val().trim();
    $('#iconPreview').attr('class', iconClass ? 'fas ' + iconClass : 'fas fa-question-circle');
  });

  /**
  * Edit-knop: opent de modal en vult velden met categorie-data.
  *
  * De knoppen die deze functie triggeren bevatten data-attributen:
  *   data-id="..."
  *   data-naam="..."
  *   data-icon="..."
  */
  $(document).on('click', '.editBtn', function () {
    const id = $(this).data('id');
    const naam = $(this).data('naam');
    const icon = $(this).data('icon');

    $('#editCategorieID').val(id);
    $('#editCategorieNaam').val(naam);
    $('#editCategorieIcon').val(icon);

    $('#editCategorieModal').modal('show');
  });

  /**
  * Delete-knop: vraagt bevestiging en verstuurt het delete-formulier.
  *
  * De delete-knoppen bevatten data-id="..." om te weten welke categorie
  * moet worden verwijderd.
  */
  $(document).on('click', '.deleteBtn', function () {
    const id = $(this).data('id');
    if (confirm('Weet je zeker dat je deze categorie wilt verwijderen?')) {
      $('#deleteCategorieID').val(id);
      $('#deleteCategorieForm').submit();
    }
  });
});
