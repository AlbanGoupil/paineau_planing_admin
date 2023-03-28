jQuery(document).ready(function($) {
    // Récupérer les événements depuis WordPress
    $.getJSON('/wp-json/wp/v2/evenements', function(events) {
      // Initialiser le calendrier
      $('.calendar').fullCalendar({
        // Configuration du calendrier
        header: {
          left: 'prev,next today',
          center: 'title',
          right: 'month,agendaWeek,agendaDay'
        },
        defaultDate: '2023-03-01',
        navLinks: true, // can click day/week names to navigate views
        editable: true,
        eventLimit: true, // allow "more" link when too many events
        events: events,
        eventClick: function(event) {
          if (event.url) {
            window.open(event.url);
            return false;
          }
        }
      });
  });
});