<?php
class Precios {

    function __construct() {
        add_action( 'admin_menu', array($this, 'menu_options') );
    }

    function menu_options() {

        add_submenu_page(
            'tools.php',
            __( 'Revisar precios', 'revisar_precios' ),
            __( 'Revisar precios', 'revisar_precios' ),
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
        </style>
        <h1><?php _e( 'Revisar Precios', 'revisar_precios' ) ?></h1>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th scope="col" class="manage-column column-author" width="15%">ID</th>
                    <th scope="col" class="manage-column column-author" width="15%">SKU</th>
                    <th scope="col" class="manage-column column-author" width="25%">Producto</th>
                    <th scope="col" class="manage-column column-author" width="15%">Precio Perú</th>
                    <th scope="col" class="manage-column column-author" width="15%">Precio Latam</th>
                    <th scope="col" class="manage-column column-author" width="15%">Precio Mundo</th>
                </tr>
            </thead>
            <tbody id="the-list">
                <?php $productos = new WP_Query(array(
                    'post_type' => 'product',
                    'posts_per_page' => -1
                ));
                while ($productos->have_posts()) { $productos->the_post();
                    $idproduct = get_the_id(); ?>
                    <tr class="tr-superior">
                        <td class="author column-author"><?php echo $idproduct; ?></td>
                        <td class="author column-author">SUPERIOR</td>
                        <td class="author column-author"><?php the_title(); ?></td>
                        <td class="author column-author">S/.<?php echo get_post_meta($idproduct, '_price', true); ?></td>
                        <td class="author column-author">$<?php echo get_post_meta($idproduct, '_latinoamerica_price', true); ?></td>
                        <td class="author column-author">$<?php echo get_post_meta($idproduct, '_todo-el-mundo_price', true); ?></td>
                    </tr>
                    <?php $handle = new WC_Product_Variable($idproduct);
                    $variations = $handle->get_children();
                    if ($variations) {
                        foreach ($variations as $value) {
                            $single_variation=new WC_Product_Variation($value);
                            $idvariable = $single_variation->get_variation_id(); ?>
                            <tr>
                                <td class="author column-author"><?php echo $idvariable; ?></td>
                                <td class="author column-author"><?php echo get_post_meta($idvariable, '_sku', true); ?></td>
                                <td class="author column-author">Talla <?php echo get_post_meta($idvariable, 'attribute_pa_adults-size', true); ?></td>
                                <td class="author column-author">S/.<?php echo get_post_meta($idvariable, '_price', true); ?></td>
                                <td class="author column-author">$<?php echo get_post_meta($idvariable, '_latinoamerica_price', true); ?></td>
                                <td class="author column-author">$<?php echo get_post_meta($idvariable, '_todo-el-mundo_price', true); ?></td>
                            </tr>
                        <?php }
                    }
                } ?>
            </tbody>
        </table>
    <?php }

}
?>
