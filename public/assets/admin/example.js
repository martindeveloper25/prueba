$(document).ready(function(){

    $('#myModal').hide();
    $('#tablaUsuario').dataTable( {
		"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
		"sPaginationType": "bootstrap"
	} );

    
    $('#btnNuevo').click(function(){
        //Carga cuerpo de modal
        $.ajax({
            url: urls.siteUrl + '/admin/usuario/form',
            success: function(result) {
                $('.modal-body').empty().html(result);
            }
        })
    })
  
})


