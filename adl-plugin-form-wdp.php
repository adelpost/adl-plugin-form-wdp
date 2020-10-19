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
    if (
        !empty($_POST)
        and $_POST['nombre'] != ''
        and is_email($_POST['correo'])
        and $_POST['nivel_html'] != ''
        and $_POST['nivel_css'] != ''
        and $_POST['nivel_js'] != ''
        and $_POST['aceptacion'] == '1'
    ) {
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
                'nombre' => $nombre,
                'correo' => $correo,
                'nivel_html' => $nivel_html,
                'nivel_css' => $nivel_css,
                'nivel_js' => $nivel_js,
                'aceptacion' => $aceptacion,
                'create_at' => $create_at,
            )
        );
        echo "<p class='exito'><b>Tus datos han sido registrados</b>. Gracias
            por tu interés. En breve contactaré contigo.<p>";
    }
    wp_enqueue_style('css_aspirante', plugins_url('style.css', __FILE__));
    ob_start();
?>
    <form action="<?php get_the_permalink(); ?>" method="post" id="form_aspirante" class="cuestionario"">
    <div class=" form-input">
        <?php wp_nonce_field('graba_aspirante', 'aspirante_nonce');
        ?>
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

add_action("admin_menu", "kfp_Aspirante_menu");

/**
 * Agrega el menú del plugin al formulario de Wordpress
 * 
 * @return void
 */

function Kfp_Aspirante_menu()
{
    add_menu_page(
        "Formulario Aspirante",
        "Aspirantes",
        "manage_options",
        "kfp_aspirante_menu",
        "Kfp_Aspirante_admin",
        "dashicons-feedback",
        75
    );
}

function Kfp_Aspirante_admin()
{
    global $wpdb;
    $table_aspirante = $wpdb->prefix . 'aspirante';
    $aspirantes = $wpdb->get_results("SELECT * FROM $table_aspirante");
    echo '<div class="wrap"><h1>Lista de aspirantes</h1>';
    echo '<table class="wp-list-table widefat dixed striped">';
    echo '<thead><tr><th width="30%">Nombre</th><th width="20%">Correo</th>';
    echo '<th>HTML</th><th>CSS</th><th>JS</th><th>Total</th>';
    echo '</tr></thead>';
    echo '<tbody id="the-list">'; 
    foreach($aspirantes as $aspirante){
        $nombre= esc_textarea ( $aspirante->nombre );
        $correo= esc_textarea ( $aspirante->correo );
        $html= (int) $aspirante->nivel_html;
        $css= (int) $aspirante->nivel_css;
        $js= (int) $aspirante->nivel_js;
        $total = $html + $css + $js;
        echo "<tr><td>$nombre</td><td>$correo</td><td>$html</td><td>$css</td><td>$js</td><td>$total</td></tr>";
    }
    echo '</tbody></table>';
}
