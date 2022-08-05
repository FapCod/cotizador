<?php require_once INCLUDES . 'head.php' ?>
<?php require_once INCLUDES . 'navbar.php' ?>

<!-- contenedor  -->
<div class="container-fluid py-5">
    <!-- inicio de notificacion -->
    <div class="row">
        <div class="col-12 wrapper_notifications" id="wrapper_notifications">

        </div>
    </div>
    <!-- fin de notificacion -->
    <div class="row">

        <div class="col-lg-7 col-12">
            <div class="card mb-3">
                <div class="card-header">
                    Informacion del Cliente
                </div>
                <div class="card-body">
                    <form action="">
                        <div class="form-group row">
                            <div class="col-4">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Walter Hernan" required>
                            </div>
                            <div class="col-4">
                                <label for="empresa">Empresa</label>
                                <input type="text" class="form-control" id="empresa" name="empresa" placeholder="Los delfines IER" required>
                            </div>
                            <div class="col-4">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="ejemplo@gmail.com" required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Agregar nuevo concepto</div>
                <div class="card-body">
                    <form id="add_to_quote" method="post">
                        <div class="form-group row">
                            <div class="col-3">
                                <label for="concepto">Concepto</label>
                                <input type="text" class="form-control" id="concepto" name="concepto" placeholder="Guitarrita normal" required>
                            </div>
                            <div class="col-3">
                                <label for="tipo">Tipo Producto</label>
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="producto">Producto</option>
                                    <option value="servicio">Servicio</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" max="99999" value="1" required>
                            </div>
                            <div class="col-3">
                                <label for="precio_unitario">Precio Unitario</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">S/</span>
                                    </div>
                                    <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" min="0" step="any" max="99999999" value="0" required>
                                </div>
                            </div>
                        </div>
                        <br>
                        <button class="btn btn-success" type="submit">Agregar concepto</button>
                        <button class="btn btn-danger" type="reset">Cancelar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-12">
            <div class="wrapper_update_concept" style="display: none">
                <div class="card mb-3">
                    <div class="card-header">Editar concepto</div>
                    <div class="card-body">
                        <form id="save_concept" method="post">
                            <input type="hidden" class="form-control" id="id_concepto" name="id_concepto">
                            <div class="form-group row">
                                <div class="col-3">
                                    <label for="concepto">Concepto</label>
                                    <input type="text" class="form-control" id="concepto" name="concepto" placeholder="Guitarrita normal" required>
                                </div>
                                <div class="col-3">
                                    <label for="tipo">Tipo Producto</label>
                                    <select name="tipo" id="tipo" class="form-control">
                                        <option value="producto">Producto</option>
                                        <option value="servicio">Servicio</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" max="99999" value="1" required>
                                </div>
                                <div class="col-3">
                                    <label for="precio_unitario">Precio Unitario</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">S/</span>
                                        </div>
                                        <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" min="0" step="any" max="99999999" value="0" required>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <button class="btn btn-success" type="submit">Guardar cambios</button>
                            <button class="btn btn-danger" type="reset" id="cancel_edit">Cancelar</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Resumen de cotizacion
                    <button class="btn btn-primary float-right restart_quote ">Reiniciar</button>
                </div>
                <div class="card-body wrapper_quote">

                </div>

            </div>
            <div class="card-footer">
                <button class="btn btn-success" id="generate_quote">Generar Cotizacion</button>
                <a class="btn btn-primary" id="dowload_quote" style="display: none" href="">Descargar PDF</a>
                <button class="btn btn-success" id="send_quote" style="display: none">Enviar por correo</button>
            </div>
        </div>
    </div>
</div>
<!-- fin contenedor  -->

<?php require_once INCLUDES . 'footer.php' ?>