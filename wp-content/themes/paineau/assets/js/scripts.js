jQuery(document).ready(function($) {
    // Récupérer les événements depuis WordPress
    $.getJSON('/wp-json/wp/v2/evenements', function(events) {
      // Initialiser le calendrier
      console.log(events);
  });
});