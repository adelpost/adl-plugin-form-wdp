<?php

/**
 * Plugin Name: KFP Plugin form
 * Author: Francisco J. Torres
 * Description: Plugin para crear un formulario personalizado. Utiliza el shotcode
 * shortcode [kfp_plugin_form]
 */

register_activation_hook(__FILE__, 'Kfp_Aspirante_init');

function Kfp_Aspirante_init()
{

    global $wpdb;
    $table_aspirante = $wpdb->prefix . 'aspirante';
    $charset_collate = $wpdb->get_charset_collate();
    // prepara la consulta que vamos a lanzar para crear la tabla
    $query = "CREATE TABLE IF NOT EXISTS $table_aspirante(
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(40) NOT NULL,
        correo varchar(100) NOT NULL,
        nivel_html smallint(4) NOT NULL,
        nivel_css smallint(4) NOT NULL,
        nivel_js smallint(4) NOT NULL,
        aceptacion smallint(4) NOT NULL,
        create_at datetime NOT NULL,
        UNIQUE (id)
    ) $charset_collate";
    include_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($query);
}

// Define el shortcode que pinta el formulario
add_shortcode('kfp_plugin_form', 'KFP_Plugin_form');

function KFP_Plugin_form()
{
    global $wpdb;
   //Inserccion de datos
    if(!empty($_POST) 
    AND $_POST['nombre'] != '' 
    AND is_email($_POST['correo'])
    AND $_POST['nivel_html'] != '' 
    AND $_POST['nivel_css'] != '' 
    AND $_POST['nivel_js'] != '' 
    AND $_POST['aceptacion'] == '1'
    ){
        $table_aspirante = $wpdb->prefix . 'aspirante';
        $nombre = sanitize_text_field($_POST['nombre']);
        $correo = sanitize_email($_POST['correo']);
        $nivel_html = (int)$_POST['nivel_html'];
        $nivel_css = (int)$_POST['nivel_css'];
        $nivel_js = (int)$_POST['nivel_js'];
        $aceptacion = (int)$_POST['aceptacion'];
        $create_at = date('Y-m-d H:i:s');
        $wpdb->insert(
            $table_aspirante, 
            array(
            'nombre'=> $nombre,
            'correo'=> $correo,
            'nivel_html'=> $nivel_html,
            'nivel_css'=> $nivel_css,
            'nivel_js'=> $nivel_js,
            'aceptacion'=> $aceptacion,
            'create_at'=> $create_at,
        )
    );
    } 

    ob_start();
?>
     <form action="<?php get_the_permalink(); ?>" method="post" id="form_aspirante" class="cuestionario"">
    <div class="form-input">
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" required>
    </div>
    <div class="form-input">
        <label for='correo'>Correo</label>
        <input type="email" name="correo" id="correo" required>
    </div>
    <div class="form-input">
        <label for="nivel_html">¿Cuál es tu nivel de HTML?</label>
        <input type="radio" name="nivel_html" value="1" required> Nada
        <br><input type="radio" name="nivel_html" value="2" required> Estoy
        aprendiendo
        <br><input type="radio" name="nivel_html" value="3" required> Tengo
        experiencia
        <br><input type="radio" name="nivel_html" value="4" required> Lo
        domino al dedillo
    </div>
    <div class="form-input">
        <label for="nivel_css">¿Cuál es tu nivel de CSS?</label>
        <input type="radio" name="nivel_css" value="1" required> Nada
        <br><input type="radio" name="nivel_css" value="2" required> Estoy
        aprendiendo
        <br><input type="radio" name="nivel_css" value="3" required> Tengo
        experiencia
        <br><input type="radio" name="nivel_css" value="4" required> Lo
        domino al dedillo
    </div>
    <div class="form-input">
        <label for="nivel_js">¿Cuál es tu nivel de JavaScript?</label>
        <input type="radio" name="nivel_js" value="1" required> Nada
        <br><input type="radio" name="nivel_js" value="2" required> Estoy
        aprendiendo
        <br><input type="radio" name="nivel_js" value="3" required> Tengo
        experiencia
        <br><input type="radio" name="nivel_js" value="4" required> Lo domino
        al dedillo
    </div>
    <div class="form-input">
        <label for="aceptacion">La información facilitada se tratará
            con respeto y admiración.</label>
        <input type="checkbox" id="aceptacion" name="aceptacion" value="1" required> Entiendo y acepto las condiciones
    </div>
    <div class="form-input">
        <input type="submit" value="Enviar">
    </div>
    </form>
<?php
    return ob_get_clean();
}


