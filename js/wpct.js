/**
*
*   WP Category Thumbnail
*
**/
jQuery( function() {
  jQuery( '.wpct-wrap' ).hover( function() {
    jQuery( '.wpct-img' ).clearQueue();
    jQuery( this ).find( '.wpct-img' ).css( {
      '-webkit-filter' : 'grayscale(1)',
      'filter' : 'grayscale(1)'
    } );
  }, function() {
    jQuery( '.wpct-img' ).clearQueue();
    jQuery( this ).find( '.wpct-img' ).css( {
      '-webkit-filter' : 'grayscale(0)',
      'filter' : 'grayscale(0)'
    } );
  } );
} );
