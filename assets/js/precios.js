jQuery(function() {
    jQuery('.usar_precio').on('click', function( e ) {
        e.preventDefault();
        var $this = $(this);
        $this.text('Actualizando...');
        var id = $(this).data('id');
        var type = $(this).data('type');
        jQuery.ajax( {
            url: jQuery('#revisar_ajaxurl').val(),
            method: 'POST',
            data: {
                action : 'usar_precio',
                id : id,
                type : type
            }
        }).done(function( data ) {
            $this.text('Actualizado');
            alert(data);
        });

    });
})
