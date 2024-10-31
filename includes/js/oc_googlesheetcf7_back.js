jQuery(document).ready(function () {

    jQuery(document).on('click', '#save-cf7glgsheet-code', function () {
 
        jQuery('.loading-sign').append('<div class="ocwqv_loading"><img src="'+ CF7GLGSHEET_jsdata.image_name +'/includes/images/loader-3.gif" class="ocwqv_loader"></div>');
        var loading = jQuery('.ocwqv_loading');
        loading.show();
        var data = {
        action: 'cf7glgsheet_verify_gs_integation',
        code: jQuery('#cf7glgsheet-code').val(),
        security: jQuery('#cf7glgsheet-ajax-nonce').val()
      };
      
      jQuery.post(ajaxurl, data, function (response ) {
          if( ! response.success ) { 
         
            loading.remove();
            jQuery( "#cf7glgsheet-validation-message" ).empty();
            jQuery("<span class='error-message'>Access code Can't be blank</span>").appendTo('#cf7glgsheet-validation-message');
          } else {
          
           loading.remove();
            jQuery( "#cf7glgsheet-validation-message" ).empty();
            jQuery("<span class='cf7glgsheet-valid-message'>Your Google Access Code is Authorized and Saved.</span>").appendTo('#cf7glgsheet-validation-message'); 
            setTimeout(function () { location.reload(); }, 4000);
          }
      });
      
    });  

    jQuery(document).on('click', '#deactivate-log', function () {
      
        jQuery('.loading-sign-deactive').append('<div class="ocwqv_loading"><img src="'+  CF7GLGSHEET_jsdata.image_name +'/includes/images/loader-3.gif" class="ocwqv_loader"></div>');
        var loading = jQuery('.ocwqv_loading');
        loading.show();
        var txt;
        var r = confirm("Are You sure you want to deactivate Google Integration ?");
        if (r == true) {
            var data = {
                action: 'cf7glgsheet_deactivate_gs_integation',
                security: jQuery('#cf7glgsheet-ajax-nonce').val()
            };
            jQuery.post(ajaxurl, data, function (response ) {
                if ( response == -1 ) {
                    return false; // Invalid nonce
                }
                if( ! response.success ) {
                    alert('Error while deactivation');
                   
                     loading.remove();
                    jQuery( "#deactivate-message" ).empty();
                    
                } else {
                  
                    loading.remove();
                    jQuery( "#deactivate-message" ).empty();
                    jQuery("<span class='cf7glgsheet-valid-message'>Your account is removed. Reauthenticate again to integrate Contact Form with Google Sheet.</span>").appendTo('#deactivate-message');
                    setTimeout(function () { location.reload(); }, 5000);
                }
            });
        } else {
           
             loading.remove();
        }
    }); 

    // jQuery(document).on('click', '.debug-clear', function () { 
    //     //jQuery( ".clear-loading-sign" ).addClass( "loading" );
    //     jQuery('.clear-loading-sign').append('<div class="ocwqv_loading"><img src="'+  CF7GLGSHEET_jsdata.image_name +'/includes/images/loader-3.gif" class="ocwqv_loader"></div>');
    //     var loading = jQuery('.ocwqv_loading');
    //     loading.show();
    //         var data = {
    //             action: 'gs_clear_log',
    //             security: jQuery('#gs-ajax-nonce').val()
    //         };
    //     jQuery.post(ajaxurl, data, function (response ) {
    //         if( response.success ) { 
    //             loading.remove();
    //             //jQuery( ".clear-loading-sign" ).removeClass( "loading" );
    //             jQuery( "#gs-validation-message" ).empty();
    //             jQuery("<span class='gs-valid-message'>Logs are cleared.</span>").appendTo('#gs-validation-message'); 
    //         }
    //     });
    // });
      
});