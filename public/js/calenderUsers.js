$(document).ready(function () {
  /**
   * 1️⃣ Open reserveer-modal
   *
   * Trigger: klik op .open-reserve-modal-btn
   * Data-attributen van knop:
   * - material-id
   * - material-name
   * - logged-in (1 = ingelogd, 0 = niet ingelogd)
   */
  $(".open-reserve-modal-btn").on("click", function () {
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
    $.getJSON("ajax/get_material_availability.php", {
      material_id: matId,
    }).done(function (data) {
      if (!data.records || data.records.length === 0) {
        $("#calendarContainer").html(
          "<p class='text-danger'>Geen beschikbaarheid voor dit materiaal.</p>"
        );
        return;
      }

      // ⭐ Unieke datums verzamelen
      let uniqueDates = {};

      data.records.forEach((record) => {
        if (record.date) {
          uniqueDates[record.date] = true;
        }
      });

      let dates = Object.keys(uniqueDates);

      if (dates.length === 0) {
        $("#calendarContainer").html(
          "<p class='text-danger'>Geen beschikbare dagen gevonden.</p>"
        );
        return;
      }

      // ⭐ Datum-lijst tonen (1 knop per datum)
      let html = "<h6>Beschikbare dagen:</h6><div class='list-group'>";

      dates.forEach((date) => {
        html += `
        <button class="list-group-item list-group-item-action available-date"
                data-date="${date}">
            ${date}
        </button>`;
      });

      html += "</div>";
      $("#calendarContainer").html(html);

      // ---------------------------------------
      // 3️⃣ Datum selecteren door gebruiker
      // ---------------------------------------
      $(".available-date").on("click", function () {
        const date = $(this).data("date");
        $("#selectedDate").val(date);

        $.getJSON("ajax/get_material_availability.php", {
          material_id: matId,
          date: date,
        }).done(function (info) {
          const segments = info.segments;
          let html = "";

          Object.keys(segments).forEach((periode) => {
            const seg = segments[periode];
            const disabled = seg.available === 0 ? "disabled" : "";
            html += `
                    <button type="button" class="btn btn-outline-primary w-100 select-periode-btn" data-periode="${periode}" data-available="${
              seg.available
            }" ${disabled}>
                      ${periode.replace("_", " ")} (${
              seg.available
            } beschikbaar)
                    </button>
                  `;
          });

          $("#periodeButtons").html(html);
          $("#periodeContainer").show();

          $(".select-periode-btn").on("click", function () {
            $(".select-periode-btn").removeClass("active");
            $(this).addClass("active");
            $("#selectedPeriode").val($(this).data("periode"));

            const available = $(this).data("available");
            $("#aantal")
              .attr("max", available)
              .val(available > 0 ? 1 : 0)
              .prop("disabled", available === 0);

            $("#availableText").html(
              `Beschikbaar in dit dagdeel: <strong>${available}</strong>`
            );
          });
        });
      });

      // Formulier tonen
      $("#reserveForm").show();
    });
  });
});
