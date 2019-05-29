<?php
class Precios {

    function __construct() {
        add_action( 'admin_menu', array($this, 'menu_options') );
        add_action( 'admin_enqueue_scripts', array($this, 'admin_assets') );
        add_action( 'wp_ajax_usar_precio', array($this, 'ajax_usar_precio') );
    }

    function admin_assets() {

        wp_enqueue_script( 'precios_admin', plugin_dir_url( __FILE__ ) . 'assets/js/precios.js', array(), time(), true );

    }

    function menu_options() {

        add_submenu_page(
            'tools.php',
            __( 'Revisar productos', 'revisar_precios' ),
            __( 'Revisar productos', 'revisar_precios' ),
            'manage_options',
            'revisar-precios',
            array($this, 'page_options')
        );
    }

    function page_options() { ?>
        <p>&nbsp;</p>
        <style media="screen">
        .tr-superior {
            border-top: 1px solid #000;
            font-weight: bold;
            background-color: #ccc !important;
        }
        tr.error {
            background: #feb9b9 !important;
        }
        </style>
        <h1><?php _e( 'Revisar Precios', 'revisar_precios' ) ?></h1>
        <div class="notice notice-warning is-dismissible">
            <?php $ayuda = 'El botón "Usar este precio" actualizará el precio del padre y todos los hijos de un mismo producto.'; ?>
            <p><?php echo $ayuda; ?></p>
        </div>
        <p>&nbsp;</p>
        <input type="hidden" id="revisar_ajaxurl" value="<?php echo admin_url('admin-ajax.php'); ?>" />
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-author" width="10%">ID</th>
                    <th scope="col" class="manage-column column-author" width="10%">SKU</th>
                    <th scope="col" class="manage-column column-author" width="22%">Producto</th>
                    <th scope="col" class="manage-column column-author" width="11%">Precio Perú</th>
                    <th scope="col" class="manage-column column-author" width="11%">Precio Latam</th>
                    <th scope="col" class="manage-column column-author" width="11%">Precio Mundo</th>
                    <th scope="col" class="manage-column column-author" width="13%">Stock</th>
                    <th scope="col" class="manage-column column-author" width="12%">Arreglar</th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php $productos = new WP_Query(array(
                    'post_type' => 'product',
                    'posts_per_page' => -1
                ));
                while ($productos->have_posts()) { $productos->the_post();
                    $idproduct = get_the_id();
                    $parent_price = get_post_meta($idproduct, '_price', true); ?>
                    <tr class="tr-superior product-<?php echo $idproduct; ?>">
                        <td class="author column-author"><?php echo $idproduct; ?></td>
                        <td class="author column-author">SUPERIOR</td>
                        <td class="author column-author"><?php the_title(); ?></td>
                        <td class="author column-author">S/.<?php echo $parent_price; ?></td>
                        <td class="author column-author">$<?php echo get_post_meta($idproduct, '_latinoamerica_price', true); ?></td>
                        <td class="author column-author">$<?php echo get_post_meta($idproduct, '_todo-el-mundo_price', true); ?></td>
                        <td class="author column-author">SUPERIOR</td>
                        <td class="author column-author"><a href="#" title="<?php echo $ayuda; ?>" class="usar_precio" data-id="<?php echo $idproduct; ?>" data-type="parent">Usar este precio</a></td>
                    </tr>
                    <?php $variaciones = get_posts(array(
                        'post_type' => 'product_variation',
                        'post_parent' => $idproduct,
                        'posts_per_page' => -1
                    ));
                    if ($variaciones) {
                        foreach ($variaciones as $var) {
                            $idvariable = $var->ID;
                            $child_price = get_post_meta($idvariable, '_price', true); ?>
                            <tr class="product-<?php echo $idproduct; ?> <?php if($parent_price!=$child_price) echo 'error'; ?>">
                                <td class="author column-author"><?php echo $idvariable; ?></td>
                                <td class="author column-author"><?php echo get_post_meta($idvariable, '_sku', true); ?></td>
                                <td class="author column-author">Talla <?php echo get_post_meta($idvariable, 'attribute_pa_adults-size', true); ?></td>
                                <td class="author column-author">S/.<?php echo $child_price; ?></td>
                                <td class="author column-author">$<?php echo get_post_meta($idvariable, '_latinoamerica_price', true); ?></td>
                                <td class="author column-author">$<?php echo get_post_meta($idvariable, '_todo-el-mundo_price', true); ?></td>
                                <td class="author column-author"><?php echo round(get_post_meta($idvariable, '_stock', true)); ?></td>
                                <td class="author column-author"><a href="#" title="<?php echo $ayuda; ?>" class="usar_precio" data-id="<?php echo $idvariable; ?>" data-type="child">Usar este precio</a></td>
                            </tr>
                        <?php }
                    }
                } ?>
            </tbody>
        </table>
    <?php }

    function ajax_usar_precio(){

        $price = get_post_meta($_POST['id'], '_price', true);
        if ($_POST['type']=='child') {
            $idproducto = wp_get_post_parent_id($_POST['id']);
        } else {
            $idproducto = $_POST['id'];
        }

        $variaciones = get_posts(array(
            'post_type' => 'product_variation',
            'post_parent' => $idproducto,
            'posts_per_page' => -1
        ));
        if ($variaciones) {
            foreach ($variaciones as $var) {
                update_post_meta($var->ID, '_price', $price);
            }
        }
        update_post_meta($idproducto, '_price', $price);

        echo 'actualizado';
        exit();

    }

}

?>
