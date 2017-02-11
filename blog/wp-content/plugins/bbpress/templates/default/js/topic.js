jQuery( document ).ready( function ( $ ) {
 // Make sure this is a topic or a group forum topic page
 if((jq("body.single-topic").length>0)||(jq("body.group-forum-topic").length>0)){
	function bbp_ajax_call( action, topic_id, nonce, update_selector ) {
		var $data = {
			action : action,
			id     : topic_id,
			nonce  : nonce
		};

		$.post( bbpTopicJS.bbp_ajaxurl, $data, function ( response ) {
			if ( response.success ) {
				$( update_selector ).html( response.content );
			} else {
				if ( !response.content ) {
					response.content = bbpTopicJS.generic_ajax_error;
				}
				alert( response.content );
			}
		} );
	}

	$( '#favorite-toggle' ).on( 'click', 'span a.favorite-toggle', function( e ) {
		e.preventDefault();
		bbp_ajax_call( 'favorite', $( this ).attr( 'data-topic' ), jq( 'span a.favorite-toggle' ).attr( 'href' ).match("_wpnonce=([^&]*)[&.*]?")[1], '#favorite-toggle' );
	} );

	$( '#subscription-toggle' ).on( 'click', 'span a.subscription-toggle', function( e ) {
		e.preventDefault();
		bbp_ajax_call( 'subscription', $( this ).attr( 'data-topic' ), jq( 'span a.subscription-toggle' ).attr( 'href' ).match("_wpnonce=([^&]*)[&.*]?")[1], '#subscription-toggle' );
	} );

	// Make sure the url scheme is set to https (removed)

	/*
	// When the document is done loading get the subscription and state buttons from the server (this overrides the cache's buttons)	
	bbp_ajax_call( 'getsubscriptionstate', jq( 'span a.subscription-toggle' ).attr( 'data-topic' ), jq( 'span a.subscription-toggle' ).attr( 'href' ).match("_wpnonce=([^&]*)[&.*]?")[1], '#subscription-toggle' );
	bbp_ajax_call( 'getfavoritestate', $( 'span a.favorite-toggle' ).attr( 'data-topic' ), jq( 'span a.favorite-toggle' ).attr( 'href' ).match("_wpnonce=([^&]*)[&.*]?")[1], '#favorite-toggle' );
*/
	// We only add this link to users that get posts from the global cache (not admins)
	/*if(jQuery.cookie("disable_my_cache")==null){
	//if(jQuery.cookie("disable_my_cache")==1){
		var myarticlediv = jQuery(jQuery( "ul[id^='topic-']")[0]); //jQuery(jQuery( "article[id^='post-']")[0]);
		var myarticleid = myarticlediv.attr("id").match("[0-9]+")[0];
		var myposts = myarticlediv.find("div[id^='post-']");
		var mycanonical = jQuery("link[rel='canonical']").attr("href");
		// Base for the edit link of the first post (has the topic name in the link, instead of having the post id)
		var myfirsttopiclink = mycanonical;
		// Base for edit links that have the post id in the link
		var mytopiclinkbase = mycanonical.match("(.*\\/)[^//\]+\\/[^//\]+\\/$")[1]; // jQuery("link[rel='shortlink']").attr("href").match("(.+)\\?")[1];
		for(var myidx=0;myidx<myposts.length;myidx++){
			var mypostid= jQuery(myposts[myidx]).attr("id").match("[0-9]+")[0];
			var mypostcontent = jQuery(myposts[myidx]).next();
			var myauthorname = mypostcontent.find(".bbp-author-name")[0].innerHTML;
			var myuname = jQuery.cookie("uname");
			var myactionmenu = jQuery(myposts[myidx]).find(".dropdown-menu.bbp-admin-links")[0];
			
			// Allow users to edit their posts
			if(myuname==myauthorname){
				var mytopiclink = "";
				var mytopiclinkclass = "";
				//var myactionmenu = jQuery(myposts[myidx]).find(".dropdown-menu.bbp-admin-links")[0];
				if(myarticleid== mypostid){
					// no id in link
					mytopiclink = myfirsttopiclink+"edit/";
					mytopiclinkclass = "bbp-topic-edit-link";  	
				}
				else{
					// post id in link
					mytopiclink = mytopiclinkbase +"reply/"+ mypostid + "/edit/"; 
					mytopiclinkclass = "bbp-reply-edit-link"; 
				}
				// Make sure that the edit link doesn't already exist
				if((jQuery(myactionmenu).find("."+mytopiclinkclass).length==0)
					&&(jQuery(myactionmenu).find(".bbp-reply-edit-link").length==0)){
					jQuery(myactionmenu).prepend("<li><a href='"+ mytopiclink+"' class='"+mytopiclinkclass+"'>Edit</a></li>");
				}
			}// uname==author
			else{
				// Remove any edit links for posts that were not created by this author (such links
				// could come from the cache, so we have to remove them)
				var mytopiclinktoremove=jQuery(myactionmenu).find(".bbp-topic-edit-link");
				var myreplylinktoremove=jQuery(myactionmenu).find(".bbp-reply-edit-link");
				if(mytopiclinktoremove.length>0){
					mytopiclinktoremove.remove();
				}
				if(myreplylinktoremove.length>0){
					myreplylinktoremove.remove();
				}
			}
		}
	//}
	}*/
	
 }

} );
