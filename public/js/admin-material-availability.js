/**
 * Script: Materiaal beschikbaarheid beheren (Admin)
 *
 * Functionaliteiten:
 *  - FullCalendar initialiseren met bestaande beschikbaarheid
 *  - Nieuwe beschikbaarheid toevoegen via modal
 *  - Beschikbaarheid verwijderen via eventClick
 *
 * Vereisten:
 *  - availabilityData: array met bestaande beschikbaarheden (injected via PHP)
 *  - materialId: ID van het materiaal
 */

let calendar;

$(document).ready(function () {
    // ----------------------------------------
    // 1️⃣ FullCalendar Initialisatie
    // ----------------------------------------
    calendar = new FullCalendar.Calendar(document.getElementById("calendar"), {
        initialView: "dayGridMonth",
        selectable: true,
        height: 650,

        /**
         * Datum aangeklikt → open modal
         * Modal krijgt de geselecteerde datum
         * Reset start- en eindtijd
         */
        dateClick: function (info) {
            $("#selectedDate").val(info.dateStr);
            $("#dateLabel").text(info.dateStr);

            $("#starttijd").val("");
            $("#eindtijd").val("");

            $("#availabilityModal").modal("show");
        },

        /**
         * Voeg bestaande beschikbaarheid toe als events
         * title: "Beschikbaar"
         * color: groen (#28a745)
         */
        events: availabilityData.map((item) => ({
            id: item.Id,
            title: "Beschikbaar",
            start: item.startdatum,
            end: item.einddatum ? item.einddatum : item.startdatum,
            color: "#28a745",
        })),

        /**
         * Event aangeklikt → verwijderen
         * Backend via POST, frontend update calendar
         */
        eventClick: function (info) {
            if (confirm("Weet je zeker dat je deze beschikbaarheid wil verwijderen?")) {
                $.post(
                    "admin-material-availability.php?id=" + materialId,
                    {
                        action: "deleteAvailability",
                        id: info.event.id,
                    },
                    function () {
                        info.event.remove(); // verwijder event uit kalender
                    }
                );
            }
        },
    });

    // Render de kalender
    calendar.render();

    // ----------------------------------------
    // 2️⃣ Form submit: nieuwe beschikbaarheid
    // ----------------------------------------
    $("#saveAvailabilityBtn").on("click", function () {
        const selectedDate = $("#selectedDate").val();
        const periode = $("#periode").val();
    
        if (!selectedDate) {
            alert("Geen datum geselecteerd.");
            return;
        }
    
        $.post(
            "admin-material-availability.php?id=" + materialId,
            {
                action: "addAvailability",
                materiaal_id: materialId,
                startdatum: selectedDate,
                einddatum: selectedDate,
                periode: periode
            },
            function () {
                $("#availabilityModal").modal("hide");
                location.reload();
            }
        );
    });
    
});
