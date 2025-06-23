$('#clinic').on('change', function() {
            var clinic = $('#clinic').val();
        
                  $.ajax({
                     url: 'categorydata.php?clinic=' + clinic,
                     type: 'get',
                     success: function(response){
                      $('#category').empty();
                      $("#category").append(response);    

                     }
                   });
          } );