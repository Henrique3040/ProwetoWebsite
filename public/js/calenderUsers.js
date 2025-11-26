$(document).ready(function () {

    $(".open-reserve-modal-btn").on("click", function() {
        const matId = $(this).data("material-id");
        const matName = $(this).data("material-name");
        const loggedIn = $(this).data("logged-in");
    
        $("#materialName").text(matName);
        $("#materialId").val(matId);
    
        let modal = new bootstrap.Modal(document.getElementById("reserveModal"));
        modal.show();
    
        if (loggedIn != 1) {
            $("#calendarContainer").html(`
                <div class="alert alert-warning text-center">
                    Je moet ingelogd zijn om te reserveren.<br><br>
                    <a href="/sign-in.php" class="btn btn-primary w-100">Inloggen</a>
                </div>
            `);
            $("#reserveForm").hide();
            return;
        }
    
        $("#reserveForm").show();

    
        console.log("Material ID:", matId, "Logged In:", loggedIn);
        $.getJSON("ajax/get_material_availability.php", { material_id: matId })
            .done(function(data) {
                console.log(data);
                if (!data || data.length === 0) {
                    $("#calendarContainer").html("<p class='text-danger'>Geen beschikbaarheid voor dit materiaal.</p>");
                    return;
                }
    
                let html = "<h6>Beschikbare dagen:</h6><div class='list-group'>";
                data.forEach(d => {
                    html += `<button class="list-group-item list-group-item-action available-date" data-date="${d.startdatum}">
                                ${d.startdatum} → ${d.einddatum}
                             </button>`;
                });
                html += "</div>";
                $("#calendarContainer").html(html);
    
                $(".available-date").on("click", function() {
                    $("#selectedDate").val($(this).data("date"));
                });
            }).fail(function(xhr, status, error) {
                console.error("AJAX error:", status, error, xhr.responseText);
            });;
    });
    

});
