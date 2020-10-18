<?php
/*
Plugin Name: Migrate From Divi
Description: A Migrate From Divi to HTML plugin
Author: Abdelrahman Fazaa for webButler GmbH
Version: 0.2
*/

add_action('admin_menu', 'era_migrate_setup_menu');

function era_migrate_setup_menu()
{
    add_menu_page('Migrate From Divi Page', 'Migrate From Divi', 'manage_options', 'era-migrate', 'era_init');
}

function era_init()
{
?>
    <style>
        .ck-wrapper {
            display: flex;
            justify-content: flex-start;
            align-items: baseline;
            flex-wrap: wrap;
            width: 100%;
            padding: 0;
        }

        label.ck-lab {
            flex-basis: 10%;
            padding: 1em 5px;
            margin-right: 1.5em;
            display: flex;
            justify-content: flex-start;
            align-items: baseline;
            line-height: 1.2;
        }

        input.ot-era-ck {
            top: 3px;
            position: relative;
        }
    </style>
    <h1>Migrate From Divi</h1>
    <form action="<?php echo admin_url("admin.php?page=era-migrate"); ?>" method="post">
        <h2 style="margin-top: 4em;">Please select pages to migrate</h2>
        <label style='margin-right:1em;'>
            <h3>Select All<input style='margin-left:0.5em' type='checkbox' id='checkAll'></h3>
        </label>
        <div class="ck-wrapper">
            <?php
            $pages = get_pages();
            foreach ($pages as $page) {
                echo "<label class='ck-lab'>$page->post_title<input style='margin-left:0.5em' type='checkbox' value='$page->ID' name='pages[]' class='ot-era-ck' ></label>";
            }
            ?>
        </div>
        <label style='margin:4em;'>
        <h3>Create a new version<input style='margin-left:0.5em' type='checkbox' name="dublicate" value="dublicate" checked="checked"></h3>
        </label>
        <input type="submit" value="Migrate Now" style="display:block; margin:2em 0;">
    </form>
    <script>
        jQuery("#checkAll").click(function() {
            jQuery('.ot-era-ck').not(this).prop('checked', this.checked);
        });
    </script>
<?php

    if (isset($_POST['pages'])) {
        migrate_now($_POST['pages'], $_POST['dublicate']);
        echo "<h2>Migration Successful!</h2>";
    }
}

function migrate_now($pages_to_migrate, $dublicate)
{
    $pages = get_pages();
    foreach ($pages as $page) {

        if (in_array($page->ID, $pages_to_migrate)) {

            if(isset( $_POST['dublicate']) &&  $_POST['dublicate'] == 'dublicate'){
                duplicate_page_as_draft($page->ID);
            }

            $content = ($page->post_content);

            $content = preg_replace('/\<div(.*?)\>/is', '', $content);
            $content = preg_replace('/\<\/div(.*?)\>/is', '', $content);

            $content = preg_replace('/class="(.*?)"/is', '', $content);

            $content = preg_replace('/\<br(.*?)\>/is', '', $content);

            $content = preg_replace('/\<h2\>\&nbsp\;\<\/h2\>/is', '', $content);
            $content = preg_replace('/\<p\>\&nbsp\;\<\/p\>/is', '', $content);
            $content = preg_replace('/\&nbsp;/is', '', $content);

            $content = preg_replace('/\[et_pb_section(.*?)\]/is', '', $content);
            $content = preg_replace('/\[\/et_pb_section\]/is', '', $content);

            $content = preg_replace('/\[et_pb_fullwidth_slider(.*?)\]/is', '', $content);
            $content = preg_replace('/\[\/et_pb_fullwidth_slider\]/is', '', $content);

            $content = preg_replace('/\[et_pb_slide(.*?)\]/is', '', $content);
            $content = preg_replace('/\[\/et_pb_slide\]/is', '', $content);

            $content = preg_replace('/\[et_pb_row(.*?)\]/is', '', $content);
            $content = preg_replace('/\[\/et_pb_row\]/is', '', $content);

            $content = preg_replace('/\[et_pb_column(.*?)\]/is', '<div>', $content);
            $content = preg_replace('/\[\/et_pb_column\]/is', '</div>', $content);

            $content = preg_replace('/\[et_pb_text(.*?)\]/is', '<p>', $content);
            $content = preg_replace('/\[\/et_pb_text\]/is', '</p>', $content);

            $content = preg_replace('/\[et_pb_image(.*?)\]/is', '<img $1 />', $content);
            $content = preg_replace('/\[\/et_pb_image\]/is', '', $content);

            $content = preg_replace('/\[dica_divi_carousel (.*?)\]/is', '<div>', $content);
            $content = preg_replace('/\[\/dica_divi_carousel\]/is', '</div>', $content);

            $content = preg_replace('/\[dica_divi_carouselitem (.*?)\]/is', '<span><img $1', $content);

            $content = preg_replace('/image="(.*?)"/is', 'src="$1"', $content);

            $content = preg_replace('/\[\/dica_divi_carouselitem\]/is', '/></span>', $content);

            $content = preg_replace('/<img button_url_new_window(.*?)src/is', '<img src', $content);
            $content = preg_replace('/image_lightbox(.*?)>/is', '/>', $content);
            $content = preg_replace('/<br="">/is', '/>', $content);

            $content = preg_replace('/\[et_pb_button button_url="(.*?)"/is', '<a href="$1">', $content);
            $content = preg_replace('/button_text="(.*?)"/is', '$1', $content);
            $content = preg_replace('/ button_alignment=(.*?)\[\/et_pb_button\]/is', '</a>', $content);

            $content = preg_replace('/\[et_pb_button_builder_version(.*?)button_url="/is', '<a href="$1"', $content);
            $content = preg_replace('/ button_alignment=(.*?)\]/is', '>', $content);
            $content = preg_replace('/button_text="(.*?)"/is', '$1', $content);
            $content = preg_replace('/\[et_pb_divider _builder_version=(.*?)\/\]/is', '</a>', $content);

            $content = preg_replace('/\[et_pb_button _builder_version="3.17.6" (.*?)button_/is', '<input type="button" value="$1"', $content);
            $content = preg_replace('/button_alignment(.*?)\/]/is', '>', $content);

            $content = preg_replace('/\[et_pb_contact_form captcha="off" title="Kontaktieren Sie uns"(.*?)\[\/et_pb_contact_form\]/is', '', $content);

            $content = preg_replace('/\[et_pb_fullwidth_map(.*?)\[\/et_pb_fullwidth_map\]/is', '', $content);

            $content = preg_replace('/\[et_pb_divider(.*?)\[\/et_pb_divider\]/is', '', $content);

            $content = preg_replace('/\[et_pb_video(.*?)\[\/et_pb_video_slider\]/is', '', $content);

            $content = preg_replace('/\[et_pb_blurb(.*?)\[\/et_pb_blurb\]/is', '', $content);

            $content = preg_replace('/\[et_pb_cta(.*?)\]/is', '', $content);

            $content = preg_replace('/\[et_pb_blurb(.*?)\]/is', '', $content);

            $exps = (explode("<", $content));
            $newcont = [];
            foreach ($exps as $temp) {
                if (strpos($temp, "img") !== false && strpos($temp, "url") !== false) {
                    preg_match('#url="(.*?)"#', $temp, $match);
                    preg_match('#src="(.*?)"#', $temp, $match2);

                    $url = $match[1];
                    $src = $match2[1];

                    $temp = 'a href="' . $url . '"><img src="' . $src . '" /></a>';
                }
                $newcont[] = $temp;
            }

            $content = implode("<", $newcont);

            $my_post = array();
            $my_post['ID'] = ($page->ID);
            $my_post['post_content'] =  $content;

            wp_update_post($my_post);
        }
    }
}

function duplicate_page_as_draft($post_id)
{
    global $wpdb;

    $post = get_post($post_id);

    $post_author = $post->post_author;

    if (isset($post) && $post != null) {

        $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name . "-2",
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => 'draft',
            'post_title'     => $post->post_title,
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order
        );

        $new_post_id = wp_insert_post($args);

        /*
		 * get all current post terms ad set them to the new post draft
		 */
        $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }

        /*
		 * duplicate all post meta just in two SQL queries
		 */
        $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
        if (count($post_meta_infos) != 0) {
            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
            foreach ($post_meta_infos as $meta_info) {
                $meta_key = $meta_info->meta_key;
                if ($meta_key == '_wp_old_slug') continue;
                $meta_value = addslashes($meta_info->meta_value);
                $sql_query_sel[] = "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query .= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query);
        }
    }
}
