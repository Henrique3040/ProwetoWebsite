$(document).ready(function () {
  // Preview icon live
  $('#categorieIcon').on('input', function () {
    const iconClass = $(this).val().trim();
    $('#iconPreview').attr('class', iconClass ? 'fas ' + iconClass : 'fas fa-question-circle');
  });

  // Open edit modal met data
  $(document).on('click', '.editBtn', function () {
    const id = $(this).data('id');
    const naam = $(this).data('naam');
    const icon = $(this).data('icon');

    $('#editCategorieID').val(id);
    $('#editCategorieNaam').val(naam);
    $('#editCategorieIcon').val(icon);

    $('#editCategorieModal').modal('show');
  });

  // Delete categorie via formulier
  $(document).on('click', '.deleteBtn', function () {
    const id = $(this).data('id');
    if (confirm('Weet je zeker dat je deze categorie wilt verwijderen?')) {
      $('#deleteCategorieID').val(id);
      $('#deleteCategorieForm').submit();
    }
  });
});
