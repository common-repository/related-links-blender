(function ($) {
	"use strict";
$(function () {


		$.ajaxSetup({cache: false});

		jQuery(document).ready(function($) {
		


			if ($("#related_links_blender_admin").length>0) {

				//get the initial current links
				refresh_links();
		
				$("#add_link_button").click( function() {
					add_a_link();
				});
		
				$("#clear_button").click( function() {
					log_cached_links(); // for debugging
					refresh_links();
					for (var current in keys) {
						$('#add_a_link_wrapper .'+keys[current]).val('').trigger('change');
					}
					$('#add_a_link_wrapper .link_target').prop('checked',false).trigger('change');

				});
		
				$("#find_posts_button").click( function() {
					//alert("finding posts");
					$("#find_posts_hidden_controls").toggleClass('rlb-hidden');
					$(".find_posts_button_search").click(function() {
						$('#link_picker_wrapper').empty();
						get_similar_posts($(this).val(),0);
					});
				});
		
		
				$("#find_posts_keywords").on('input', function() {
					highlite_similar_posts();
				});
		
				$('#add_a_link_thumb').click(function() {
				
					var send_attachment_bkp = wp.media.editor.send.attachment;
				
					wp.media.editor.send.attachment = function(props, attachment) {
						console.log(props);
						console.log(attachment);
						$('#add_a_link_wrapper .thumb_url').val(attachment.sizes[props['size']].url).trigger('change');
						$('#add_a_link_wrapper .thumb_alt').val(attachment.alt);
						wp.media.editor.send.attachment = send_attachment_bkp;
					}
				
					wp.media.editor.open();
					return false;       
				});
		


				$('#add_a_link_wrapper .text_intro').change(function() {
					$('#add_a_link_wrapper .text_intro').text($(this).val());
					
				});

				$('#add_a_link_wrapper .thumb_url').change(function() {
					var thumb_url = new String($(this).val());
					$('#add_a_link_thumb img').attr('src', thumb_url);
					console.log(thumb_url)
					var thumb_name=thumb_url.slice(1+thumb_url.lastIndexOf('/'));
					console.log(thumb_name)
					$('#thumb_file_name').text(thumb_name);
				});

			}	


		});  //end document ready


		//ajax request to delete a link
		function delete_me(me) {
			$('#current_links_wrapper').css('opacity',"0.5"); //show working

			//info required for server to remove this link
			var link_info = {
				link_no: $(me).data('link_no'),
				post_id: $("#post_id").text()
			};
			
			//wrap the info up with the action to call and a nonce for security
			var data = {
				action: 'deleting_a_link',
				nonce: localized.nonce,
				link_info: JSON.stringify(link_info)     
			};

			jQuery.post(ajaxurl, data, function(response) {
				var response_decoded=$.parseJSON(response);
				if (response_decoded==-1) { //did not get a json back - mysterious failure
					alert(response);
				} else if (!response_decoded.success) { //failed - alert reason
					alert("Error: "+response_decoded.response);
				} else {
					console.log(response_decoded.response);
					$("input[data-link_no='"+response_decoded.link_no+"']").parent().remove();
				}
				$('#current_links_wrapper').css('opacity',"1.0"); //show success
			});
		}


		//ajax request to delete a link
		function sorted() {
			$('#current_links_wrapper').css('opacity',"0.5"); //show working

			//info required for server to remove this link
			var link_order = new Object();
			
			$('#current_links_wrapper .a_current_link').each(function(index) {
				link_order[index]=$(this).data('link_no');
			});
			

			var link_info = {
				link_order: link_order,
				post_id: $("#post_id").text()
			};

			console.log(link_info)
			
			//wrap the info up with the action to call and a nonce for security
			var data = {
				action: 'sort_links',
				nonce: localized.nonce,
				link_info: JSON.stringify(link_info),   
			};

			jQuery.post(ajaxurl, data, function(response) {
				var response_decoded=$.parseJSON(response);
				if (response_decoded==-1) { //did not get a json back - mysterious failure
					alert(response);
				} else if (!response_decoded.success) { //failed - alert reason
					alert("Error: "+response_decoded.response);
				} else {
					console.log(response_decoded.response);

				}
				$('#current_links_wrapper').css('opacity',"1.0"); //show success
			});
		}


		//ajax request to delete a link
		function log_cached_links() {
			$('#current_links_wrapper').css('opacity',"0.5"); //show working

			//info required for server to remove this link
			var link_info = {
				post_id: $("#post_id").text()
			};
			
			//wrap the info up with the action to call and a nonce for security
			var data = {
				action: 'get_cached_links',
				nonce: localized.nonce,
				link_info: JSON.stringify(link_info)     
			};

			jQuery.post(ajaxurl, data, function(response) {
				var response_decoded=$.parseJSON(response);
				if (response_decoded==-1) { //did not get a json back - mysterious failure
					alert(response);
				} else if (!response_decoded.success) { //failed - alert reason
					alert("RLB Error: "+response_decoded.response);
				} else {
					console.log(response_decoded.response);
				}
				$('#current_links_wrapper').css('opacity',"1.0"); //show success
			},'text');
		}



		// ajax request for current links which are then primed for jquery manipulation
		function refresh_links() {
			var link_info = { //payload
				post_id: $("#post_id").text()
			};

			var data = { //wrapper
				action: 'get_the_links',
				link_info: JSON.stringify(link_info)   
			};

			jQuery.post(ajaxurl, data, function(response) {
									
					$('#current_links_wrapper').html(response);
					$('#current_links_wrapper').css('opacity',"1.0")

					$(".delete_me").click( function() {
						delete_me(this);
					});
					$(".copy_me").click( function() {
						copy_me(this);
					});
					$('#current_links_wrapper').sortable({update: function( event, ui ) {
						sorted();
						}
					});

			});
		}


		// ajax request for current links which are then primed for jquery manipulation
		function get_similar_posts(mode,more) {
			
			$('#link_picker_wrapper').append("<img class='loading_target' src='http://blender.ca/wrdprss/wp-admin/images/loading.gif' />");
			
			var search_info = { //payload
				post_id: $("#post_id").text(),
				search_type: mode,
				page: more
			};

			var data = { //wrapper
				action: 'get_similar_links',
				search_info: JSON.stringify(search_info)   
			};

			jQuery.post(ajaxurl, data, function(response) {
					//console.log(response);
					$('.loading_target').replaceWith(response);
					make_keywords_from_title();
					highlite_similar_posts();
					$('.a_similar_link').click(function() {
						copy_similar(this);
					});
					$("#link_picker_wrapper .more_links_needed").click(function() {
						console.log($(this).attr('data-more')+$(this).attr('data-search_type')+$(this).attr('data-post_id'));
						get_similar_posts($(this).attr('data-search_type'),$(this).attr('data-more'));
						$(this).remove();
					});
			});
		}

		function make_keywords_from_title() {
			var keywords= new String($('#editable-post-name').text());
			keywords=keywords.replace(/\W/gi,' ');
			$('#find_posts_keywords').val(keywords);
			
		}

		function highlite_similar_posts() {
			var max_similar=0;
			var keyword_array=$('#find_posts_keywords').val().split(/\W+/);
			console.log(keyword_array);
			
			$('.a_similar_link').removeClass("am_similar somewhat_similar very_similar").each(function() {
				var count = 0;
				var me=$(this);
				for (var  keyword_no in keyword_array ) {
					if (keyword_array[keyword_no].length<2) break;
					if (me.attr('data-text_title').indexOf(keyword_array[keyword_no])!=-1) count+=4;
					if (me.attr('data-text_intro').indexOf(keyword_array[keyword_no])!=-1) count+=2;
					if (me.attr('data-thumb_url').indexOf(keyword_array[keyword_no])!=-1) count++;
					if (me.attr('data-link_url').indexOf(keyword_array[keyword_no])!=-1) count+=3;
					if (me.attr('data-link_title').indexOf(keyword_array[keyword_no])!=-1) count++;
					if (me.attr('data-thumb_alt').indexOf(keyword_array[keyword_no])!=-1) count++;
					if (me.attr('data-post_id').indexOf(keyword_array[keyword_no])!=-1) count+=2;
				}
				me.attr('data-how_similar',count);
				if (count>0) {
					me.addClass("somewhat_similar").attr('data-how_similar',count);
					max_similar=(count>max_similar)?count:max_similar;
				}
			});
			var am_similar=max_similar*0.5;
			var very_similar=max_similar*0.9;
			$('.somewhat_similar').filter(function() {
				return ($(this).attr('data-how_similar')<am_similar) ? false:true;
			}).addClass('am_similar').filter(function() {
				return ($(this).attr('data-how_similar')<very_similar) ? false:true;
			}).addClass('very_similar');

		}



		//list of array key names used in form names and functions: add,copy
		var keys=new Array('text_title','text_intro','thumb_url','link_url','link_target','link_title','thumb_alt');	
		
		//ajax request to add a link
		function add_a_link(me) {
			$('#current_links_wrapper').css('opacity',"0.5"); //show working

			var link_info=new Object();

			//copy the info from the make a link form to our object using the keys array as a guide
			for (var current in keys) {
				switch (keys[current]) {
					case "text_intro": 
						link_info['text_intro']= $('#add_a_link_wrapper .text_intro').text();
						break;
					case "link_target": 
						var hastarget=$('#add_a_link_wrapper .link_target').is(":checked");
						if (hastarget) {
							link_info['link_target']= "NEW";
						}
						
						break;
					default: link_info[keys[current]]=$('#add_a_link_wrapper .'+keys[current]).val();
				}
			}
			link_info['post_id']= $("#post_id").text();
			
			//our ajax bundle: action to call, nonce for security, and payload
			var data = {
				action: 'adding_a_link',
				nonce: localized.nonce,
				link_info: JSON.stringify(link_info)     
			};
	
			jQuery.post(ajaxurl, data, function(response) {
				var response_decoded = $.parseJSON(response);
				if (response_decoded==-1) { //no json in reply - mysterious failure
					alert(response);
				} else if (!response_decoded.success) {
					alert("Error: "+response_decoded.response); //failure - alert reason
				} else {
					console.log(response_decoded.response)
					refresh_links(); //show done
				}
			});
		}

		function copy_me(me) {			
			var some_info;
			for (var current in keys) {
				switch (keys[current]) {
					case "link_target":
						some_info=$(me).parent().find('.link_target').text();
						if (some_info=="NEW") {
							$('#add_a_link_wrapper .link_target').prop('checked', true);
							console.log("NEW");
						}
					break;
					default:
						some_info=$(me).parent().find('.'+keys[current]).text();
						$('#add_a_link_wrapper .'+keys[current]).val(some_info).trigger('change');					
				}

			}
		}



		function copy_similar(me) {	
			var some_info;
			for (var current in keys) {
				some_info=$(me).attr('data-'+keys[current]);
				$('#add_a_link_wrapper .'+keys[current]).val(some_info).trigger('change');;
			}
		}


});
}(jQuery));