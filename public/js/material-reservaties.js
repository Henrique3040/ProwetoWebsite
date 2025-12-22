/**
 * Script: Materiaal reservaties beheren (Admin)
 *
 * Functionaliteiten:
 *  - Edit status modal openen en select correct invullen
 *  - Delete reservatie via form submit met bevestiging
 *
 * Opmerkingen:
 *  - Event delegation wordt gebruikt zodat knoppen die dynamisch toegevoegd
 *    worden ook werken.
 *  - Status mapping zorgt dat front-end en DB statuswaarden correct matchen.
 */

$(function () {

    /**
     * 1️⃣ Edit status knop
     * 
     * Trigger: klik op .editStatusBtn
     * Data-attributen:
     *  - id: reservatie ID
     *  - status: huidige status van de reservatie
     */
    $(document).on("click", ".editStatusBtn", function () {
        var id = $(this).data("id");
        var status = $(this).data("status");

        // Vul hidden input
        $("#editStatusId").val(id);

        // Probeer status direct matchen met <option> values
        if ($("#editStatusValue option[value='" + status + "']").length) {
            $("#editStatusValue").val(status);
        } else {
            // Fallback mapping: vertaal front-end status naar DB-status
            var map = {
                "pending": "in_afwachting",
                "approved": "goedgekeurd",
                "rejected": "geweigerd",
                "completed": "afgerond",
                "in_afwachting": "in_afwachting",
                "goedgekeurd": "goedgekeurd",
                "geweigerd": "geweigerd",
                "afgerond": "afgerond"
            };

            var mapped = map[status] || status;

            if ($("#editStatusValue option[value='" + mapped + "']").length) {
                $("#editStatusValue").val(mapped);
            } else {
                // Als geen match → selecteer eerste optie
                $("#editStatusValue").prop("selectedIndex", 0);
            }
        }

        // Toon modal
        var modalEl = document.getElementById("updateStatusModal");
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    /**
     * 2️⃣ Delete reservatie knop
     *
     * Trigger: klik op .deleteReservationBtn
     * Bevestiging via confirm()
     * Hidden input vullen en form submit
     */
    $(document).on("click", ".deleteReservationBtn", function () {
        if (confirm("Weet je zeker dat je deze reservatie wilt verwijderen?")) {
            $("#deleteReservationID").val($(this).data("id"));
            $("#deleteReservationForm").submit();
        }
    });

});
