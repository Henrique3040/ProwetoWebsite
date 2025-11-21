// js/admin-material-availability.js

let calendar;

$(document).ready(function () {
  // availabilityData = array vanuit PHP
  // materialId = materiaal ID

  calendar = new FullCalendar.Calendar(document.getElementById("calendar"), {
    initialView: "dayGridMonth",
    selectable: true,
    height: 650,

    // Klik op een datum → open modal
    dateClick: function (info) {
      $("#selectedDate").val(info.dateStr);
      $("#dateLabel").text(info.dateStr);

      // Tijden resetten
      $("#starttijd").val("");
      $("#eindtijd").val("");

      $("#availabilityModal").modal("show");
    },

    // Voeg bestaande beschikbaarheden toe als events
    events: availabilityData.map((item) => ({
      id: item.Id,
      title: "Beschikbaar",
      start: item.startdatum, // startdatum
      end: item.einddatum ? item.einddatum : item.startdatum, // einddatum
      color: "#28a745",
    })),

    // Klik op een event → verwijderen
    eventClick: function (info) {
      if (
        confirm("Weet je zeker dat je deze beschikbaarheid wil verwijderen?")
      ) {
        $.post(
          "admin-material-availability.php?id=" + materialId,
          {
            action: "deleteAvailability",
            id: info.event.id,
          },
          function () {
            info.event.remove();
          }
        );
      }
    },
  });

  calendar.render();

  // FORM SUBMIT – nieuwe beschikbaarheid opslaan
  $("#saveAvailabilityBtn").on("click", function () {
    const startDate = $("#startdatum").val();
    const endDate = $("#einddatum").val();
    const startTime = $("#starttijd").val();
    const endTime = $("#eindtijd").val();

    if (!startDate || !endDate) {
      alert("Start- en einddatum moeten ingevuld zijn.");
      return;
    }

    $.post(
      "admin-material-availability.php?id=" + materialId,
      {
        action: "addAvailability",
        materiaal_id: materialId,
        startdatum: startDate,
        einddatum: endDate,
        starttijd: startTime,
        eindtijd: endTime,
      },
      function () {
        // Modal sluiten
        $("#availabilityModal").modal("hide");
        // Herlaad pagina om lijst en kalender te verversen
        location.reload();
      }
    );
  });
});
