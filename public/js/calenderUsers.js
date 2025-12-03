$(document).ready(function () {

    $(".open-reserve-modal-btn").on("click", function() {
        const matId = $(this).data("material-id");
        const matName = $(this).data("material-name");
        const loggedIn = $(this).data("logged-in");
    
        $("#materialName").text(matName);
        $("#materialId").val(matId);
        $("#selectedDate").val("");
        $("#reserveForm").hide();

        // Reset input
        $("#aantal").val(1);
        $("#aantal").prop("disabled", false);
        $("#availableText").text("");

        let modal = new bootstrap.Modal(document.getElementById("reserveModal"));
        modal.show();

        // Niet ingelogd
        if (loggedIn != 1) {
            $("#calendarContainer").html(`
                <div class="alert alert-warning text-center">
                    Je moet ingelogd zijn om te reserveren.<br><br>
                    <a href="/sign-in.php" class="btn btn-primary w-100">Inloggen</a>
                </div>
            `);
            return;
        }

        // Laad beschikbaarheid + totaal aantal
        $.getJSON("ajax/get_material_availability.php", { material_id: matId })
        .done(function(data) {

            // 1. Zet DIRECT het max op basis van totaal aantal materiaal
            if (data.total_stock) {
                $("#aantal").attr("max", data.total_stock);
                $("#availableText").html(`Totaal beschikbaar: <strong>${data.total_stock}</strong>`);
            }

            // kalender tonen
            if (!data.records || data.records.length === 0) {
                $("#calendarContainer").html("<p class='text-danger'>Geen beschikbaarheid voor dit materiaal.</p>");
                return;
            }

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

            // 2. Wanneer gebruiker datum kiest → update 'available'
            $(".available-date").on("click", function() {
                const date = $(this).data("date");
                $("#selectedDate").val(date);

                $.getJSON("ajax/get_material_availability.php", { 
                    material_id: matId,
                    date: date
                })
                .done(function(info) {
                    const available = info.available ?? 0;

                    $("#availableText").html(
                        `Beschikbaar op deze dag: <strong>${available}</strong>`
                    );

                    $("#aantal")
                        .attr("max", available)
                        .val(available > 0 ? 1 : 0)
                        .prop("disabled", available === 0);

                    $("#reserveForm button[type='submit']")
                        .prop("disabled", available === 0)
                        .text(available === 0 ? "Niet beschikbaar" : "Reservering bevestigen");
                });
            });

            $("#reserveForm").show();
        });

    });

});
