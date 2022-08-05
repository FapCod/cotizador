$('#body').waitMe({
    effect: 'orbit'
});
$(document).ready(() => {

    //funcion para generar una notificacion
    function notify(message, type = 'success') {
        console.log(message);
        let wrapper = $('.wrapper_notifications'),
            id = Math.floor((Math.random() * 500) + 1),
            notificacion = '<div class="alert alert-' + type + '" id="noty_' + id + '">' + message + '</div>',
            time = 5000;

        // insertar al contenedor de la notificacion
        wrapper.append(notificacion);
        // time para ocultar la notificacion
        setTimeout(() => {
            $('#noty_' + id).remove();
        }, time);
        console.log(notificacion);
        return true;
    }






    // cargar contenido de la cotizacion
    function get_quote() {
        let wrapper = $('.wrapper_quote');
        let action = 'get_quote_res';
        const name = $('#nombre');
        const email = $('#email');
        const company = $('#empresa');

        $.ajax({
            url: 'ajax.php',
            type: 'GET',
            cache: false,
            dataType: 'json',
            data: { action },
            beforeSend: function () {
                wrapper.waitMe({
                    effect: 'orbit'
                });
            }
        }).done(res => {
            wrapper.waitMe('hide');
            if (res.status === 200) {
                name.val(res.data.quote.name);
                company.val(res.data.quote.company);
                email.val(res.data.quote.email);
                wrapper.html(res.data.html);

            } else {
                wrapper.html(res.msg);
            }
            console.log(res);
        }).fail(err => {
            wrapper.html('Ocurrio un error recarga la pagina.');
            console.log(err);
        }).always(() => {
            wrapper.waitMe('hide');
        })

    }
    get_quote();


    // agregar un concepto a la cotizacion 
    $('#add_to_quote').on('submit', add_to_quote);
    function add_to_quote(e) {
        e.preventDefault();
        let form = $('#add_to_quote');
        let action = 'add_to_quote';
        let data = new FormData(form.get(0));
        let errors = 0;

        // agregar la accion al objeto data.
        data.append('action', action);

        // validar el concepto
        let concepto = $('#concepto').val();
        let precio = parseFloat($('#precio_unitario').val());

        if (concepto.length < 5) {
            notify('El concepto debe tener al menos 5 caracteres.', 'danger');
            errors++;
        }

        // validar el precio
        if (precio < 10) {
            notify('El precio debe ser mayor a S/10.', 'danger');
            errors++;
        }

        if (errors > 0) {
            notify('Por favor corrige los errores.', 'danger');
            return false;
        }

        // enviar el formulario
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            beforeSend: () => {
                form.waitMe({
                    effect: 'orbit'
                });
            }
        }).done(res => {
            form.waitMe('hide');
            if (res.status === 201) {
                notify(res.msg, 'success');
                form.trigger('reset');
                get_quote();
            } else {
                notify(res.msg, 'danger');
            }
            console.log(res);
        }).fail(err => {
            form.waitMe('hide');
            notify('Ocurrio un error, intenta de nuevo.', 'danger');
            form.trigger('reset');
            console.log(err);
        }).always(() => {
            form.waitMe('hide');
        })
    }
    
    
    //funcion para reiniciar la cotizacion
    $('.restart_quote').on('click', restart_quote);
    function restart_quote(e) {
        e.preventDefault();
        let button = $(this);
        let action = 'restart_quote';
        let dowload = $('#dowload_quote');
        let send = $('#send_quote');
        let generate = $('#generate_quote');
        let default_text = 'Generar cotizacion';
        if(!confirm('¿Estas seguro de reiniciar la cotizacion?')){
            return false;
        }

        //peticion ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: { action },
        }).done(res => {
            if (res.status === 200) {
                notify(res.msg, 'success');
                get_quote();
                dowload.fadeOut();
                send.fadeOut();
                send.attr('data-number', '');
                generate.text(default_text);
            } else {
                notify(res.msg, 'danger');
            }
            console.log(res);
        }).fail(err => {
            notify('Ocurrio un error, intenta de nuevo.', 'danger');
            console.log(err);
        }).always(() => {
            // button.waitMe('hide');
        }
        )
    } 

    // funcion para eliminar concepto de la cotizacion
    $('body').on('click','.delete_concept', delete_concept);
    function delete_concept(e) {
        e.preventDefault();
        let button = $(this);
        let action = 'delete_concept';
        let id = button.data('id');
        if(!confirm('¿Estas seguro de eliminar este concepto?')){
            return false;
        }

        //peticion ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: { action, id },
            beforeSend: () => {
                $('body').waitMe({
                    effect: 'orbit'
                });
            }
        }).done(res => {
            if (res.status === 200) {
                notify(res.msg, 'success');
                get_quote();
            } else {
                notify(res.msg, 'danger');
            }
            console.log(res);
        }).fail(err => {
            notify('Ocurrio un error, intenta de nuevo.', 'danger');
            console.log(err);
        }).always(() => {
            // button.waitMe('hide');
            $('body').waitMe('hide');
        })
    }


    // funcion para cargar concepto
    $('body').on('click','.edit_concept', edit_concept);
    function edit_concept(e) {
        e.preventDefault();
        let button = $(this);
        let action = 'edit_concept';
        let id = button.data('id');
        let wrapper_update_concept = $('.wrapper_update_concept');
        let form_update_concept = $('#save_concept');

        // peticion ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: { action, id },
            beforeSend: () => {
                $('body').waitMe({
                    effect: 'orbit'
                });
            }
        }).done(res => {
            if (res.status ===200){
                $('#id_concepto',form_update_concept).val(res.data.id);
                $('#concepto',form_update_concept).val(res.data.concept);
                $('#tipo option[value="'+res.data.type+'"]', form_update_concept).attr('selected',true);
                $('#cantidad',form_update_concept).val(res.data.quantity);
                $('#precio_unitario',form_update_concept).val(res.data.price);
                wrapper_update_concept.fadeIn();
                notify(res.msg, 'success');
            }else{
                notify(res.msg, 'danger');


            }
        }).fail(err => {
            notify('Ocurrio un error, intenta de nuevo.', 'danger');
            console.log(err);
        }).always(() => {
            // button.waitMe('hide');
            $('body').waitMe('hide');
        })
    }

    // ocultar el form con cancel_edit
    $('#cancel_edit').on('click', (e)=>{
        e.preventDefault();
        let button = $(this);
        let wrapper_update_concept = $('.wrapper_update_concept');
        let form = $('#save_concept');
        wrapper_update_concept.fadeOut();
        form.trigger('reset');
    });


    // funcion para actualizar concepto
    $('#save_concept').on('submit', save_concept);
    function save_concept(e) {
        e.preventDefault();
        let form = $('#save_concept');
        let action = 'save_concept';
        let data = new FormData(form.get(0));
        let wrapper_update_concept = $('.wrapper_update_concept');
        let errors = 0;

        // agregar la accion al objeto data.
        data.append('action', action);

        // validar el concepto
        let concepto = $('#concepto',form).val();
        let precio = parseFloat($('#precio_unitario',form).val());

        if (concepto.length < 5) {
            notify('El concepto debe tener al menos 5 caracteres.', 'danger');
            errors++;
        }

        // validar el precio
        if (precio < 10) {
            notify('El precio debe ser mayor a S/10.', 'danger');
            errors++;
        }

        if (errors > 0) {
            notify('Por favor corrige los errores.', 'danger');
            return false;
        }

        // enviar el formulario
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            data: data,
            beforeSend: () => {
                form.waitMe({
                    effect: 'orbit'
                });
            }
        }).done(res => {
            form.waitMe('hide');
            if (res.status === 201) {
                wrapper_update_concept.fadeOut();
                form.trigger('reset');
                notify(res.msg, 'success');
                get_quote();
            } else {
                notify(res.msg, 'danger');
            }
            console.log(res);
        }).fail(err => {
            form.waitMe('hide');
            notify('Ocurrio un error, intenta de nuevo.', 'danger');

            form.trigger('reset');
            wrapper_update_concept.fadeOut();
            console.log(err);
        }).always(() => {
            form.waitMe('hide');
        })
    }

    // funcion para generar cotizacion
    $('#generate_quote').on('click', generate_quote);
    function generate_quote(e) {
        e.preventDefault();
        let button = $(this);
        let action = 'generate_quote';
        let default_text = button.html();
        let next_text = 'Volver a generar';
        let dowload = $('#dowload_quote');
        let send = $('#send_quote');
        let nombre = $('#nombre').val();
        let empresa = $('#empresa').val();
        let email = $('#email').val();
        let errors = 0;

        // validar accion
        if(!confirm('¿Estas seguro de generar la cotizacion?')){
            return false;
        }

        // validar nombre
        if (nombre.length < 5) {
            notify('El nombre debe tener al menos 5 caracteres.', 'danger');
            errors++;
        }

        // validar empresa
        if (empresa.length < 5) {
            notify('La empresa debe tener al menos 5 caracteres.', 'danger');
            errors++;
        }

        // validar email
        if(email.length < 5) {
            notify('El email debe tener al menos 5 caracteres.', 'danger');
            errors++;
        }

        if (errors > 0) {
            notify('Por favor corrige los errores.', 'danger');
            return false;
        }

        // peticion ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: { action, nombre, empresa, email },
            beforeSend: () => {
                button.waitMe({
                    effect: 'orbit'
                });
                button.html('Generando PDF');
            }
        }).done(res => {
            if(res.status == 200){
                notify(res.msg);
                dowload.attr('href', res.data.url);
                send.attr('data-number', res.data.number);
                console.log(34);
                dowload.fadeIn();
                send.fadeIn();
                button.html(next_text);
                
            }else{
                notify(res.msg, 'danger');
                dowload.attr('href', '');
                dowload.fadeOut();
                console.log(35);
                send.attr('data-number', '');
                send.fadeOut();
                button.html("Reintentar");
            }
        }).fail(err => {
            notify('Ocurrio un  error en pdf, intenta de nuevo.', 'danger');
            button.html(default_text);
            console.log(err);
        }).always(() => {
            $('body').waitMe('hide');
        })
    }

    // funcion para enviar por correo electronico
    $('#send_quote').on('click', send_quote);
    function send_quote(e) {
        e.preventDefault();

        let button = $(this);
        let action = 'send_quote';
        let number = button.data('number');
        let next_text = 'Volver a enviar';
        let default_text = button.html(); //enviar por correo
        // validar accion
        if(!confirm('¿Estas seguro de enviar la cotizacion?')){
            return false;
        }

        if(number == ''){
            notify('No se pudo generar la cotizacion, intenta de nuevo.', 'danger');
            return false;
        }

        // peticion ajax
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: { action, number },
            beforeSend: () => {
                $('body').waitMe({
                    effect: 'orbit'
                });
                button.html('Enviando...');
            }
        }).done(res => {
            if(res.status == 200){
                notify(res.msg);
                button.html(next_text);
            }else{
                notify(res.msg, 'danger');
                button.html('Reintentar');
            }
        }).fail(err => {
            notify('Ocurrio un  error en el envio, intenta de nuevo.', 'danger');
            button.html(default_text);
            console.log(err);
        }).always(() => {
            $('body').waitMe('hide');
        })
    }

});