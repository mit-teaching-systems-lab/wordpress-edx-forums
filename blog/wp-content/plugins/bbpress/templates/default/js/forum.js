 jQuery( document ).ready( function ( $ ) {
  // Make sure that this is a forum page
  if((jq("div#bbpress-forums").length>0)&&(jq("body.group-forum-topic").length<1)&&(jq("body.reply").length==0)){ 
     function bbp_ajax_call( action, forum_id, nonce, update_selector ) {
         var $data = {
             action : action,
             id     : forum_id,
             nonce  : nonce
         };
 
         $.post( bbpForumJS.bbp_ajaxurl, $data, function ( response ) {
             if ( response.success ) {
                 $( update_selector ).html( response.content );
             } else {
                 if ( !response.content ) {
                     response.content = bbpForumJS.generic_ajax_error;
                 }
                window.alert( response.content );
             }
         } );
     }
 
     $( "[id='subscription-toggle']" ).on( 'click', 'span a.subscription-toggle', function( e ) {
         e.preventDefault();
	 // There seem to be two html elements that have the same id, we need to update both. Using '#subscription-toggle' only updates one element.
	 // Using "[id='subscription-toggle']" updates both elements
         //bbp_ajax_call( 'forum_subscription', $( this ).attr( 'data-forum' ), bbpForumJS.subs_nonce, '#subscription-toggle' );
	bbp_ajax_call( 'forum_subscription', $( this ).attr( 'data-forum' ), jq( 'span a.subscription-toggle' ).attr( 'href' ).match("_wpnonce=([^&]*)[&.*]?")[1], "[id='subscription-toggle']");
     } );
	// Make sure the url scheme is set to https (removed)

     // Get the subscription state and override the state that came from the cache
     //bbp_ajax_call( 'forum_getsubscriptionstate', $( this ).attr( 'data-forum' ), bbpForumJS.subs_nonce, '#subscription-toggle' );
     // There seem to be two html elements that have the same id (top and bottom), we need to update both. Using '#subscription-toggle' only updates one element.
     // Using "[id='subscription-toggle']" updates both elements
     //bbp_ajax_call( 'forum_getsubscriptionstate', $( this ).attr( 'data-forum' ), bbpForumJS.subs_nonce, '#subscription-toggle' );
     /*bbp_ajax_call( 'forum_getsubscriptionstate', $( 'span a.subscription-toggle' ).attr( 'data-forum' ), jq( 'span a.subscription-toggle' ).attr( 'href' ).match("_wpnonce=([^&]*)[&.*]?")[1], "[id='subscription-toggle']");*/
  }
 } );
