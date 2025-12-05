/**
 * Script: Materiaal reserveren (frontend)
 *
 * Functionaliteiten:
 *  - Openen van reserveer-modal bij klikken op knop
 *  - Check of gebruiker ingelogd is
 *  - Laden van beschikbaarheid via AJAX
 *  - Gebruiker selecteert beschikbare datum
 *  - Aanpassen van aantal te reserveren op basis van beschikbaarheid
 */

$(document).ready(function () {

    /**
     * 1️⃣ Open reserveer-modal
     * 
     * Trigger: klik op .open-reserve-modal-btn
     * Data-attributen van knop:
     *  - material-id
     *  - material-name
     *  - logged-in (1 = ingelogd, 0 = niet ingelogd)
     */
    $(".open-reserve-modal-btn").on("click", function() {
        const matId = $(this).data("material-id");
        const matName = $(this).data("material-name");
        const loggedIn = $(this).data("logged-in");

        // Modal velden resetten
        $("#materialName").text(matName);
        $("#materialId").val(matId);
        $("#selectedDate").val("");
        $("#reserveForm").hide();
        $("#aantal").val(1).prop("disabled", false);
        $("#availableText").text("");

        let modal = new bootstrap.Modal(document.getElementById("reserveModal"));
        modal.show();

        /**
         * Niet ingelogd → toon waarschuwing en login-knop
         */
        if (loggedIn != 1) {
            $("#calendarContainer").html(`
                <div class="alert alert-warning text-center">
                    Je moet ingelogd zijn om te reserveren.<br><br>
                    <a href="/sign-in.php" class="btn btn-primary w-100">Inloggen</a>
                </div>
            `);
            return;
        }

        // ---------------------------------------
        // 2️⃣ Laad beschikbaarheid via AJAX
        // ---------------------------------------
        $.getJSON("ajax/get_material_availability.php", { material_id: matId })
        .done(function(data) {

            // 2a. Totaal aantal beschikbaar instellen
            if (data.total_stock) {
                $("#aantal")
                    .attr("max", data.total_stock)
                    .val(1);
                $("#availableText").html(
                    `Totaal beschikbaar: <strong>${data.total_stock}</strong>`
                );
            }

            // 2b. Geen beschikbaarheid → bericht tonen
            if (!data.records || data.records.length === 0) {
                $("#calendarContainer").html("<p class='text-danger'>Geen beschikbaarheid voor dit materiaal.</p>");
                return;
            }

            // 2c. Beschikbare dagen tonen als klikbare buttons
            let html = "<h6>Beschikbare dagen:</h6><div class='list-group'>";
            data.records.forEach(d => {
                html += `
                    <button class="list-group-item list-group-item-action available-date"
                            data-date="${d.startdatum}">
                        ${d.startdatum} → ${d.einddatum}
                    </button>`;
            });
            html += "</div>";
            $("#calendarContainer").html(html);

            // ---------------------------------------
            // 3️⃣ Datum selecteren door gebruiker
            // ---------------------------------------
            $(".available-date").on("click", function() {
                const date = $(this).data("date");
                $("#selectedDate").val(date);

                // Nieuwe AJAX call: beschikbaarheid op deze dag
                $.getJSON("ajax/get_material_availability.php", { 
                    material_id: matId,
                    date: date
                })
                .done(function(info) {
                    const available = info.available ?? 0;

                    // Toon aantal beschikbaar
                    $("#availableText").html(
                        `Beschikbaar op deze dag: <strong>${available}</strong>`
                    );

                    // Pas aantal-input en submit-knop aan
                    $("#aantal")
                        .attr("max", available)
                        .val(available > 0 ? 1 : 0)
                        .prop("disabled", available === 0);

                    $("#reserveForm button[type='submit']")
                        .prop("disabled", available === 0)
                        .text(available === 0 ? "Niet beschikbaar" : "Reservering bevestigen");
                });
            });

            // Formulier tonen
            $("#reserveForm").show();
        });
    });
});
