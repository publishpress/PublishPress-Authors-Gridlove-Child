<?php
//Child Theme Functions File
add_action('wp_enqueue_scripts', 'enqueue_wp_child_theme');
function enqueue_wp_child_theme()
{
    if ((esc_attr(get_option('childthemewpdotcom_setting_x')) != "Yes")) {
        //This is your parent stylesheet you can choose to include or exclude this by going to your Child Theme Settings under the "Settings" in your WP Dashboard
        wp_enqueue_style('parent-css', get_template_directory_uri() . '/style.css');
    }

    //This is your child theme stylesheet = style.css
    wp_enqueue_style('child-css', get_stylesheet_uri());

    //This is your child theme js file = js/script.js
    wp_enqueue_script('child-js', get_stylesheet_directory_uri() . '/js/script.js', array('jquery'), '1.0', true);
}


function gridlove_get_meta_data($layout = 'a', $force_meta = false)
{

    $meta_data = $force_meta !== false ? $force_meta : array_keys(array_filter(gridlove_get_option('lay_' . $layout . '_meta')));

    $output = '';

    if (!empty($meta_data)) {

        foreach ($meta_data as $mkey) {


            $meta = '';

            switch ($mkey) {

                case 'date':
                    $date = gridlove_get_option('post_modified_date') ? get_the_modified_date() : get_the_date();
                    $meta = '<span class="updated">' . $date . '</span>';
                    break;

                case 'author':

                    if (defined('PP_AUTHORS_LOADED') && $coauthors_meta = get_multiple_authors()) {
                        $temp = '';
                        foreach ($coauthors_meta as $key) {
                            $temp .= '<span class="vcard author"><span class="fn"><a href="' . esc_url($key->link) . '">' . $key->get_avatar(24) . '' . $key->display_name . '</a></span></span>';
                        }
                        $meta = '<div class="coauthors">' . $temp . '</div>';

                    } else {

                        $author_id = get_post_field('post_author', get_the_ID());
                        $meta      = '<span class="vcard author"><span class="fn"><a href="' . esc_url(get_author_posts_url(get_the_author_meta('ID',
                                $author_id))) . '">' . get_avatar(get_the_author_meta('ID', $author_id),
                                24) . ' ' . get_the_author_meta('display_name', $author_id) . '</a></span></span>';
                    }

                    break;

                case 'views':
                    global $wp_locale;
                    $thousands_sep = isset($wp_locale->number_format['thousands_sep']) ? $wp_locale->number_format['thousands_sep'] : ',';
                    if (strlen($thousands_sep) > 1) {
                        $thousands_sep = trim($thousands_sep);
                    }
                    $meta = function_exists('ev_get_post_view_count') ? number_format_i18n(absint(str_replace($thousands_sep,
                                '',
                                ev_get_post_view_count(get_the_ID())) + absint(gridlove_get_option('views_forgery')))) . ' ' . __gridlove('views') : '';
                    break;

                case 'rtime':
                    $meta = gridlove_read_time(get_post_field('post_content', get_the_ID()));
                    if (!empty($meta)) {
                        $meta .= ' ' . __gridlove('min_read');
                    }
                    break;

                case 'comments':
                    if (comments_open() || get_comments_number()) {
                        ob_start();
                        comments_popup_link(__gridlove('no_comments'), __gridlove('one_comment'),
                            __gridlove('multiple_comments'));
                        $meta = ob_get_contents();
                        ob_end_clean();
                    } else {
                        $meta = '';
                    }
                    break;

                default:
                    break;
            }

            if (!empty($meta)) {
                $output .= '<div class="meta-item meta-' . $mkey . '">' . $meta . '</div>';
            }
        }
    }


    return wp_kses_post($output);

}

function gridlove_get_author_links($author)
{

    $output = '';

    if (is_numeric($author)) {
        $author = MultipleAuthors\Classes\Objects\Author::get_by_user_id($author);
    }

    if (is_singular()) {
        $output .= '<a href="' . esc_url($author->link) . '" class="gridlove-pill pill-large">' . __gridlove('view_all') . '</a>';
    }


    if ($url = $author->user_url) {
        $output .= '<a href="' . esc_url($url) . '" target="_blank" class="gridlove-sl-item fa fa-link"></a>';
    }

    return wp_kses_post($output);
}