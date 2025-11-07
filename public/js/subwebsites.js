$(document).ready(function() {
    // Edit button
    $(document).on('click', '.editBtn', function () {
        $('#editSubWebsiteID').val($(this).data('id'));
        $('#editSubWebsiteTitle').val($(this).data('title'));
        $('#editSubWebsiteLink').val($(this).data('link'));
        $('#editSubWebsiteIcon').val($(this).data('icon'));
        $('#editSubWebsiteModal').modal('show');
    });

    // Delete button
    $(document).on('click', '.deleteBtn', function () {
        if(confirm('Weet je zeker dat je deze subwebsite wilt verwijderen?')) {
            $('#deleteSubWebsiteID').val($(this).data('id'));
            $('#deleteSubWebsiteForm').submit();
        }
    });
});