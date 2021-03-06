<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<h2 class="nbd-title-page"><?php echo __('Manager NBDesigner Products', 'web-to-print-online-designer'); ?></h2>
<div class="wrap postbox nbdesigner-manager-product">
    <div>
	<?php 
            global $wpdb;
            $link_create_template_page = getUrlPageNBD('template');
            foreach($pro as $key => $val): 
            $id = $val["id"];    
            $priority = 'extra';
            $primary = get_post_meta($id, '_nbdesigner_admintemplate_primary', true);
            if(!$primary) $priority = 'primary';
            $link_create_template = add_query_arg(array(
                    'product_id' => $id,
                    'priority' =>  $priority,
                    'task'  =>  'create_template'
                ), getUrlPageNBD('template'));             
            $link_manager_template = add_query_arg(array('pid' => $id, 'view' => 'templates'), admin_url('admin.php?page=nbdesigner_manager_product'));
        ?>
		<div class="nbdesigner-product">
                    <a class="nbdesigner-product-title"><span><?php echo $val['name']; ?></span></a>
                    <div class="nbdesigner-product-inner">
                        <a href="<?php echo $val['url']; ?>" class="nbdesigner-product-link"><?php echo $val['img']; ?></a> 
                    </div>
                    <p class="nbdesigner-product-link">
                        <a href="<?php echo $val['url'].'#nbdesigner_setting'; ?>" title="<?php _e('Edit product', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-edit"></span></a>
                        <a href="<?php echo get_permalink($val['id']); ?>" title="<?php _e('View product', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-visibility"></span></a>
                        <a href="<?php echo $link_create_template; ?>" target="_blank" title="<?php _e('Create template', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-customizer"></span></a>
                        <a href="<?php echo $link_manager_template; ?>" title="<?php _e('Manager template', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-images-alt"></span></a>
                    </p>                     
		</div>		
	<?php endforeach;?>
    </div>
    <div class="tablenav top">
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $number_pro.' '. __('Products', 'web-to-print-online-designer'); ?></span>
            <?php echo $paging->html();  ?>
        </div>
    </div>    
</div>