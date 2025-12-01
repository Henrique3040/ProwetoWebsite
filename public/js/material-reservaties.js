// js/material-reservaties.js
$(function () {
    // Event delegation: werkt ook als rijen dynamisch zijn
    $(document).on("click", ".editStatusBtn", function () {
        var id = $(this).data("id");
        var status = $(this).data("status");

        // Vul het hidden veld en de select
        $("#editStatusId").val(id);

        // Als je DB status-waarden anders zijn (b.v. "in_afwachting"), map ze hier
        // Zorg dat option values exact overeenkomen met DB-waarden.
        // We proberen eerst direct te matchen, anders doen we een map fallback.
        if ($("#editStatusValue option[value='" + status + "']").length) {
            $("#editStatusValue").val(status);
        } else {
            // fallback mapping: pas mapping aan naar jouw DB-waarden
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
                // als nog geen match, selecteer eerste optie
                $("#editStatusValue").prop("selectedIndex", 0);
            }
        }

        // toon modal
        var modalEl = document.getElementById("updateStatusModal");
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // Delete - delegation
    $(document).on("click", ".deleteReservationBtn", function () {
        if (confirm("Weet je zeker dat je deze reservatie wilt verwijderen?")) {
            $("#deleteReservationID").val($(this).data("id"));
            $("#deleteReservationForm").submit();
        }
    });
});
