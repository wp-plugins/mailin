$(document).ready(function () {
				
			
				$("#mySelectBox").multiselect();
				
				var token = $('#token').val();
				
				var radios = $('input:radio[name=managesubscribe]:checked').val();
					
					if(radios==0) { 
						$('.subscribe').hide();
					}else{ 
						$('.subscribe').show();
					}
				
				var mailin_api_status = $('input:radio[name=mailin_api_status]:checked').val();
					
					if(mailin_api_status==0){ 
						$('.blog_form').hide();
						$('.subscribe').hide();
						$('.apikey').hide();
					}else{ 
						$('.blog_form').show();
						$('.apikey').show();
						var radios = $('input:radio[name=managesubscribe]:checked').val();

						if(radios==0) { 
							$('.subscribe').hide();
						}else{ 
							$('.subscribe').show();
						}
					}	
					
					
					
				$('.mailin_api_status').click(function (){

					var mailin_api_status = jQuery(this).val();

					if (mailin_api_status == 0){
						$('.blog_form').hide();
						$('.subscribe').hide();
						$('.apikey').hide();
					}else{
						$('.blog_form').show();
						$('.apikey').show();
						var radios = $('input:radio[name=managesubscribe]:checked').val();

						if(radios==0) { 
							$('.subscribe').hide();
						}else{ 
							$('.subscribe').show();
						}

					}
				});	
				
				
				$('#showUserlist').click(function () {

					if ($('.widefat').is(':hidden')) {
						$('#Spantextless').show();
						$('#Spantextmore').hide();
					} else {
						$('#Spantextmore').show();
						$('#Spantextless').hide();
					}
						$('.widefat').slideToggle("fast");
				});	
				
				function loadData(page) 
				{
					var token = $('#token').val();
					var language = $('#language').val();
					$("#pagenumber").val(page);
					var path= $("#path").val();
					
					$.ajax({
						type: "POST",
						async: false,
						url: base_url + "/mailin/ajaxcontent.php",
						data: "page=" + page + "&token="+token+"&language="+language,
						beforeSend: function () {
							$('#ajax-busy').show();
						},
						success: function (msg) {
							$('#ajax-busy').hide();
							$(".midleft").html(msg);
							$(".midleft").ajaxComplete(function (event, request, settings) {

								$(".midleft").html(msg);

							});
						}
					});
				}

			loadData(1); // For first time page load
			// default
			// results

			$('body').on('click', '.pagination li.active', function () {
				var page = $(this).attr('p');
				$('#page_no').val(page);
				loadData(page);
			});
		
		
		
        $("body .mailin_smtp_action").click(function () {
            
            var smtptest = jQuery(this).val();
            
            if (smtptest == 0) {
                $('#smtptest').hide();
            }else{
                $('#smtptest').show();
            }
            $.ajax({
                    type: "POST",
                    async: false,
                    url: base_url + "/mailin/ajaxsmtp.php",
                    data: "mailin_smtp_action=" + smtptest + "&token="+token,
                    beforeSend: function () {
                        $('#ajax-busy').show();
                    },
                    success: function (msg) {

                        $('#ajax-busy').hide();
              
                    }
                });
        });
        
        
         $(".managesubscribe").click(function () {
            
            var managesubscribe = jQuery(this).val();
            
            if (managesubscribe == 0) {
                $('.subscribe').hide();
            }else{
                $('.subscribe').show();
            }
            $.ajax({
                    type: "POST",
                    async: false,
                    url: base_url + "/mailin/ajaxmanagesubscribe.php",
                    data: "managesubscribe=" + managesubscribe + "&token="+token,
                    beforeSend: function () {
                        $('#ajax-busy').show();
                    },
                    success: function (msg) {

                        $('#ajax-busy').hide();
              
                    }
                });
        });
        
        

        $('<div id="ajax-busy"/> loading..')
            .css({
                opacity: 0.5,
                position: 'fixed',
                top: 0,
                left: 0,
                width: '100%',
                height: $(window).height() + 'px',
                background: 'white url(' + base_url + '/mailin/img/loading_anim.gif) no-repeat center'
            }).hide().appendTo('body');

       


$('body').on('click', '.ajax_contacts_href', function (e) {
            var email = $(this).attr('email');
            var status = $(this).attr('status');
           

            $.ajax({
                    type: "POST",
                    async: false,
                    url: base_url + "/mailin/ajaxcall.php",
                    data: "email=" + email + "&newsletter=" + status + "&token="+token,
                    beforeSend: function () {
                        $('#ajax-busy').show();
                    },
                    success: function (msg) {
                        $('#ajax-busy').hide();
                    }
                });

            var page_no = $('#pagenumber').val();
            loadData(page_no); // For first time page load

        });

        jQuery('.toolTip')
            .hover(function () {
                var title = jQuery(this).attr('title');
                var offset = jQuery(this).offset();

                jQuery('body').append(
                    '<div id="tipkk" style="top:' + offset.top + 'px; left:' + offset.left + 'px; ">' + title + '</div>');
                var tipContentHeight = jQuery('#tipkk')
                    .height() + 25;
                jQuery('#tipkk').css(
                    'top', (offset.top - tipContentHeight) + 'px');

            }, function () {
                jQuery('#tipkk').remove();
            });

    });
