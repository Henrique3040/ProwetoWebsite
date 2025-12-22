/**
 * Script: Subwebsite beheer (Admin)
 *
 * Functionaliteiten:
 *  - Edit-knop opent modal en vult velden met bestaande data
 *  - Delete-knop bevestigt actie en stuurt formulier door naar backend
 *
 * Vereisten:
 *  - Edit modal met velden: #editSubWebsiteID, #editSubWebsiteTitle, #editSubWebsiteLink, #editSubWebsiteIcon
 *  - Delete form met hidden input: #deleteSubWebsiteID en form: #deleteSubWebsiteForm
 */

$(document).ready(function() {

    /**
     * 1️⃣ Edit-knop functionaliteit
     * 
     * Trigger: klik op .editBtn
     * Data-attributen van de knop:
     *  - id
     *  - title
     *  - link
     *  - icon
     * Modal wordt getoond met velden ingevuld.
     */
    $(document).on('click', '.editBtn', function () {
        $('#editSubWebsiteID').val($(this).data('id'));
        $('#editSubWebsiteTitle').val($(this).data('title'));
        $('#editSubWebsiteLink').val($(this).data('link'));
        $('#editSubWebsiteIcon').val($(this).data('icon'));

        $('#editSubWebsiteModal').modal('show');
    });

    /**
     * 2️⃣ Delete-knop functionaliteit
     * 
     * Trigger: klik op .deleteBtn
     * Bevestiging via confirm()
     * Hidden input vullen en form submitten naar backend
     */
    $(document).on('click', '.deleteBtn', function () {
        if(confirm('Weet je zeker dat je deze subwebsite wilt verwijderen?')) {
            $('#deleteSubWebsiteID').val($(this).data('id'));
            $('#deleteSubWebsiteForm').submit();
        }
    });

});
