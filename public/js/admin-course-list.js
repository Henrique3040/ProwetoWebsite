/**
 * Script: Cursusoverzicht – Zoeken, filteren en sorteren
 *
 * Functionaliteiten:
 *  - Live zoeken op tekst
 *  - Filteren op status (active, pending)
 *  - Sorteren op datum (newest, oldest)
 *  - Filters resetten
 */

$(document).ready(function () {
  // Cache alle rijen om performance te verbeteren
  const $rows = $('table tbody tr');

  /**
   * ============================================
   *  LIVE SEARCH
   * ============================================
   * Filtert rijen op basis van tekst die de gebruiker typt.
   * Vergelijkt alles in lowercase om case-insensitive te zoeken.
   */
  $('#searchCourses').on('keyup', function () {
      const value = $(this).val().toLowerCase();

      $rows.each(function () {
          const rowText = $(this).text().toLowerCase();
          $(this).toggle(rowText.indexOf(value) > -1);
      });
  });

  /**
   * ============================================
   *  SORT & FILTER
   * ============================================
   * Let op:
   *  - 'active'  → toon rijen met badge.text-success
   *  - 'pending' → toon rijen met badge.text-warning
   *  - 'newest' / 'oldest' → sorteren op datum (kolom 3)
   */
  $('#sortCourses').on('change', function () {
      const selected = $(this).val();
      let visibleRows = $rows;

      // Reset eerst alle rijen
      $rows.show();

      // Filter: alleen actieve cursussen tonen
      if (selected === 'active') {
          visibleRows = $rows.filter(function () {
              return $(this).find('.badge.text-success').length > 0;
          });

      // Filter: alleen pending cursussen tonen
      } else if (selected === 'pending') {
          visibleRows = $rows.filter(function () {
              return $(this).find('.badge.text-warning').length > 0;
          });

      // Sorteren op datum
      } else if (selected === 'newest' || selected === 'oldest') {

          // Zet DOM-rijen om naar array om te sorteren
          const rowsArray = $rows.get();

          rowsArray.sort(function (a, b) {
              const dateA = new Date($(a).find('td:nth-child(3)').text());
              const dateB = new Date($(b).find('td:nth-child(3)').text());

              // oldest: datums oplopend — newest: datums aflopend
              return selected === 'oldest' ? dateA - dateB : dateB - dateA;
          });

          // Gesorteerde rijen opnieuw toevoegen aan de tabel
          $('table tbody').empty().append(rowsArray);

          return; // Niets meer doen na sorteren
      }

      // Toepassen van filter
      $rows.hide();
      visibleRows.show();
  });

  /**
   * ============================================
   *  RESET BUTTON
   * ============================================
   * Zet zoekveld en dropdown terug op standaardwaarden.
   * Toon weer alle rijen.
   */
  $('#resetFilters').on('click', function () {
      $('#searchCourses').val('');
      $('#sortCourses').val('');
      $rows.show();
  });
});
