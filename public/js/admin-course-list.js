$(document).ready(function () {
    const $rows = $('table tbody tr');
  
    // SEARCH
    $('#searchCourses').on('keyup', function () {
      const value = $(this).val().toLowerCase();
      $rows.each(function () {
        const text = $(this).text().toLowerCase();
        $(this).toggle(text.indexOf(value) > -1);
      });
    });
  
    // SORT / FILTER
    $('#sortCourses').on('change', function () {
      const selected = $(this).val();
      let visibleRows = $rows;
  
      $rows.show(); // reset
  
      if (selected === 'active') {
        visibleRows = $rows.filter(function () {
          return $(this).find('.badge.text-success').length > 0;
        });
      } else if (selected === 'pending') {
        visibleRows = $rows.filter(function () {
          return $(this).find('.badge.text-warning').length > 0;
        });
      } else if (selected === 'newest' || selected === 'oldest') {
        const rowsArray = $rows.get();
        rowsArray.sort(function (a, b) {
          const dateA = new Date($(a).find('td:nth-child(3)').text());
          const dateB = new Date($(b).find('td:nth-child(3)').text());
          return selected === 'oldest' ? dateA - dateB : dateB - dateA;
        });
        $('table tbody').empty().append(rowsArray);
        return; // stoppen na sortering
      }
  
      $rows.hide();
      visibleRows.show();
    });
  
    // RESET BUTTON
    $('#resetFilters').on('click', function () {
      $('#searchCourses').val('');
      $('#sortCourses').val('');
      $rows.show();
    });
  });
  