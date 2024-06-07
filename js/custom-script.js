jQuery(document).ready(function($) {
  $('img').each(function() {   
      let src = $(this).attr('src');             
      var altValue = $(this).attr('title');
      var attachmentId = $(this).data('attachment-id'); // Assuming you have a way to get the attachment ID

      // Check if the custom field exists using the localized script data
      if (attachmentMeta.meta[attachmentId]) {
          // Do nothing, or any logic you want if the field exists
      } else {
          if (altValue && altValue.trim() !== '') {
              $(this).addClass('title_have');
          } else {  
              if (src) {                                 
                  var words = src.substring(src.lastIndexOf("/") + 1, src.length);                       
                  var finaltxt = words.substring(0, words.lastIndexOf("."))
                      .split(/[-_]/)
                      .map(function(word) {
                          return word.charAt(0).toUpperCase() + word.slice(1);
                      })
                      .join(' ');                                          
                  $(this).attr('title', finaltxt);
              }
          }
      }                
  });
});