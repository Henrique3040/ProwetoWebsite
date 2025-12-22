/**
 * Script: Edit & Delete Materiaal (Admin)
 *
 * Functionaliteiten:
 *  - Open edit modal en vul velden met bestaande materiaaldata
 *  - Preview van foto indien aanwezig
 *  - Delete-knop stuurt het delete-formulier door
 */

$(document).ready(function () {

    /**
     * 1️⃣ Edit-knop functionaliteit
     *
     * Vul modal met data-attributen van de aangeklikte rij:
     *  - id
     *  - naam
     *  - aantal
     *  - foto (optioneel)
     *
     * Toon modal na invullen.
     */
    $(".editBtn").click(function () {
        // Vul hidden/visible velden
        $("#editMateriaalID").val($(this).data("id"));
        $("#editMateriaalNaam").val($(this).data("naam"));
        $("#editMateriaalAantal").val($(this).data("aantal")); // nieuw veld

        // Foto preview
        let foto = $(this).data("foto");
        if (foto) {
            $("#editFotoPreview").attr("src", foto).removeClass("d-none");
        } else {
            $("#editFotoPreview").addClass("d-none");
        }

        // Modal tonen
        $("#editMateriaalModal").modal("show");
    });

    /**
     * 2️⃣ Delete-knop functionaliteit
     *
     * Vul hidden input in delete-form en submit het formulier
     * zodat de backend weet welk materiaal verwijderd moet worden.
     */
    $(".deleteBtn").click(function () {
        $("#deleteMateriaalID").val($(this).data("id"));
        $("#deleteMateriaalForm").submit();
    });

});
