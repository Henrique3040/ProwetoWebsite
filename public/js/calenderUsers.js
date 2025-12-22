// -------------------------------
// RESERVATIE DATA STRUCTUUR
// -------------------------------
// Bevat de volledige reservering die naar de server wordt gestuurd
let reservationData = {
  materialId: null, // ID van het materiaal
  courseId: null,   // ID van de cursus
  items: []         // Geselecteerde datums + periodes
};

// Bestaande reserveringen van de huidige gebruiker
// Structuur: { '2025-01-10': [ { periode, start, end }, ... ] }
let userReservations = {};

// -------------------------------
// MODAL OPEN EVENT
// -------------------------------
let activeMaterialId = null;

$("#reserveModal").on("show.bs.modal", function (event) {

  let button = $(event.relatedTarget);
  let isLoggedIn = button.data("logged-in") === 1;

  // NIET ingelogd → toon login-bericht
  if (!isLoggedIn) {
    $("#materialName").text("");
    $("#calendarContainer").html(`
      <div class="alert alert-warning">
        <h6 class="mb-2">
          <i class="bi bi-lock-fill me-1"></i>
          Inloggen vereist
        </h6>
        <p class="mb-3">
          Je moet ingelogd zijn om materiaal te kunnen reserveren.
        </p>
        <a href="/sign-in.php" class="btn btn-primary w-100">
          <i class="bi bi-box-arrow-in-right me-1"></i>
          Inloggen
        </a>
      </div>
    `);

    $("#confirmReservation").addClass("d-none");
    $("#myReservationsBox").addClass("d-none");
    return;
  }

  // ✅ WEL ingelogd → normale flow
  $("#confirmReservation").removeClass("d-none");

  activeMaterialId = button.data("material-id");

  reservationData.materialId = button.data("material-id");
  reservationData.courseId = button.data("course-id");
  reservationData.items = [];

  $("#materialName").text(button.data("material-name"));

  loadAvailableDates(reservationData.materialId, reservationData.courseId);

  loadUserReservations(reservationData.materialId).then(() => {
    renderUserReservations();
  });
});


// --------------------------------------
// 1) BESCHIKBARE DATUMS LADEN
// --------------------------------------
function loadAvailableDates(materialId, courseId) {
  $("#calendarContainer").html('<p class="text-muted">Datums laden...</p>');

  $.ajax({
    url: "ajax/get_material_availability.php",
    method: "GET",
    dataType: "json", // 🔥 BELANGRIJK
    data: { material_id: materialId, course_id: courseId },
  
    success: function (response) {

      if (!response.success || !Array.isArray(response.records)) {
        console.error("Onverwachte response:", response);
        $("#calendarContainer").html(
          '<p class="text-danger">Fout bij laden van datums.</p>'
        );
        return;
      }
    
      // unieke datums uit records halen
      const availabilityPerDate = {};

      response.records.forEach(r => {
        if (!availabilityPerDate[r.date]) {
          availabilityPerDate[r.date] = 0;
        }
        availabilityPerDate[r.date] += parseInt(r.available, 10);
      });
    
      let html = '<div class="list-group">';
    
      Object.entries(availabilityPerDate).forEach(([date, totalAvailable]) => {
        const safeDate = date.replace(/[^0-9]/g, '');
      
        html += `
          <label class="list-group-item d-flex justify-content-between align-items-center">
            <span>
              <input type="checkbox"
                     class="form-check-input me-2 select-date"
                     data-date="${date}">
              ${date}
            </span>
      
            <span class="badge bg-secondary"
                  id="date-total-${safeDate}"
                  data-original="${totalAvailable}">
              ${totalAvailable} beschikbaar
            </span>
          </label>
      
          <div id="periods_${safeDate}" class="ms-4 mt-2 mb-3"></div>
        `;
      });
      
    
      html += "</div>";
      $("#calendarContainer").html(html);
    },
    
  
    error: function (xhr) {
      console.error(xhr.responseText);
      $("#calendarContainer").html(
        '<p class="text-danger">Serverfout bij laden.</p>'
      );
    }
  });
  
}

let userReservationsRequest = null;
function loadUserReservations(materialId) {
  
  if (userReservationsRequest) {
    userReservationsRequest.abort();
  }

  userReservationsRequest = $.ajax({
    url: "ajax/get_user_material_reservations.php",
    method: "GET",
    dataType: "json",
    data: { material_id: materialId },
    success: function (res) {
      if (materialId !== activeMaterialId) return;
      userReservations = res.data || {};
    }
  });

  return userReservationsRequest;

}

function renderUserReservations() {
  const box = $("#myReservationsBox");
  const list = $("#myReservationsList");

  list.empty();

  if (!userReservations || Object.keys(userReservations).length === 0) {
    box.addClass("d-none");
    return;
  }

  Object.entries(userReservations).forEach(([date, reservations]) => {
    reservations.forEach(r => {
      list.append(`
        <li>
          <strong>${date} |</strong>
          <strong>
            ${r.periode}: ${r.start} – ${r.end}
          </strong>
        </li>
      `);
    });
  });

  box.removeClass("d-none");
}

// --------------------------------------
// 2) DATUM GESELECTEERD → PERIODES LADEN
// --------------------------------------
$(document).on("change", ".select-date", function () {
  let date = $(this).data("date");
  let checked = $(this).is(":checked");

  if (checked) {
    loadPeriodsForDate(date);
  } else {
    const safeDate = date.replace(/[^0-9]/g, '');
    $(`#periods_${date}`).html("");

    // verwijder uit JSON
    reservationData.items = reservationData.items.filter(
      item => item.date !== date
    );    
    // updateDebugJson();
  }
});

// --------------------------------------
// 3) PERIODES VOOR DATUM LADEN
// --------------------------------------
function loadPeriodsForDate(date) {
  const safeDate = date.replace(/[^0-9]/g, '');

  $.ajax({
    url: "ajax/get_material_availability.php",
    method: "GET",
    dataType: "json",
    data: {
      material_id: reservationData.materialId,
      date: date
    },

    success: function (response) {

      if (!response.success || !response.segments) {
        console.error("Onverwachte response:", response);
        return;
      }

      let html = '<div class="card card-body bg-light">';

      Object.entries(response.segments).forEach(([periode, info]) => {
        if (info.available <= 0) return;

        html += `
          <div class="mb-2">
            <label>
              <input type="checkbox"
                     class="select-period"
                     data-date="${date}"
                     data-period="${periode}"
                     data-available="${info.available}">
              ${periode} — <small>${info.available} beschikbaar</small>
            </label>

            <input type="number"
                   class="form-control form-control-sm mt-1 period-amount d-none"
                   min="1" max="${info.available}"
                   data-date="${date}"
                   data-period="${periode}">
          </div>
        `;
      });

      html += "</div>";

      $(`#periods_${safeDate}`).html(html);
    }
  });
}


// ----------------------------------------------------
// 4) PERIODE GESELECTEERD → AANTAL TOEVOEGEN
// ----------------------------------------------------
$(document).on("change", ".select-period", function () {
  let date = $(this).data("date");
  let period = $(this).data("period");
  let available = $(this).data("available");

  let amountField = $(
    `input.period-amount[data-date="${date}"][data-period="${period}"]`
  );

  if ($(this).is(":checked")) {
    amountField.removeClass("d-none");
    amountField.val(1);

    addOrUpdateItem(date, period, 1);
  } else {
    amountField.addClass("d-none");

    removePeriod(date, period);
  }

  // updateDebugJson();
});

// --------------------------------------------
// 5) AANTAL AANGEPAST
// --------------------------------------------
$(document).on("input", ".period-amount", function () {
  let date = $(this).data("date");
  let period = $(this).data("period");
  let amount = parseInt($(this).val(), 10);

  addOrUpdateItem(date, period, amount);
 // updateDebugJson();
});

// --------------------------
// JSON MODIFY HELPERS
// --------------------------
function addOrUpdateItem(date, period, amount) {
  let item = reservationData.items.find((i) => i.date === date);

  if (!item) {
    item = { date: date, periodes: {} };
    reservationData.items.push(item);
  }

  item.periodes[period] = { amount };
}

function removePeriod(date, period) {
  let item = reservationData.items.find((i) => i.date === date);
  if (!item) return;

  delete item.periodes[period];

  if (Object.keys(item.periodes).length === 0) {
    reservationData.items = reservationData.items.filter(
      (i) => i.date !== date
    );
  }
}

// --------------------------------------------
// DEBUG JSON
// --------------------------------------------
function updateDebugJson() {
  $("#debugJson").text(JSON.stringify(reservationData, null, 2));
}

// --------------------------------------------
// RESERVERING VERSTUREN
// --------------------------------------------
$("#confirmReservation").on("click", function () {
  if (reservationData.items.length === 0) {
    alert("Selecteer minimaal één datum en periode.");
    return;
  }

  $.ajax({
    url: 'ajax/reserve_material_multi.php',
    method: 'POST',
    data: { json: JSON.stringify(reservationData) },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        alert(response.message || 'Reservering voltooid!');
        location.reload();
      } else {
        alert(response.message || 'Er trad een fout op.');
        console.error(response.results);
        // hier kun je fouten per periode tonen in de UI
      }
    },
    error: function(xhr) {
      let msg = 'Onbekende fout, status: ' + xhr.status;
      alert(msg);
    }
  });
  
  
});

function updateDateTotal(date) {
  const safeDate = date.replace(/[^0-9]/g, '');
  const badge = $(`#date-total-${safeDate}`);

  const originalTotal = parseInt(badge.data("original"), 10);
  let used = 0;

  const item = reservationData.items.find(i => i.date === date);
  if (item) {
    Object.values(item.periodes).forEach(p => {
      used += p.amount;
    });
  }

  const remaining = originalTotal - used;
  badge.text(`${remaining} beschikbaar`);
}
