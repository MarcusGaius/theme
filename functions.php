<?php

/**
 * Returns the instace of the theme.
 *
 * @return MarcusGaius\WordPressHeadless\Bootstrap\App
 */
function app()
{
    return MarcusGaius\WordPressHeadless\Bootstrap\App::getInstance();
}

app();

add_filter('rest_url_prefix', 'rest_url_prefix');
function rest_url_prefix()
{
    return 'api';
}

add_shortcode('lorem', 'lorem_generator');
function lorem_generator($atts)
{
    if (!is_array($atts)) $atts = [];
    $atts = array_merge(['count' => 1], $atts);
    extract($atts);
    return file_get_contents("http://loripsum.net/api/$count/short");
}

// add_action('headless_theme_customization', 'theme_customization');
// function theme_customization()
// {
add_filter('post_updated_messages', function (array $messages): array {
    $messages['post'][1] = substr($messages['post'][1], 0, 1 + strpos($messages['post'][1], '.'));
    $messages['post'][6] = substr($messages['post'][6], 0, 1 + strpos($messages['post'][6], '.'));
    $messages['post'][8] = substr($messages['post'][8], 0, 1 + strpos($messages['post'][8], '.'));
    $messages['post'][9] = substr($messages['post'][9], 0, 1 + strpos($messages['post'][9], '.'));
    $messages['post'][10] = substr($messages['post'][10], 0, 1 + strpos($messages['post'][10], '.'));
    return $messages;
});

/*
    add_filter('manage_edit-family_columns', function (array $columns): array {
        $columns = [
            'cb' => '&lt;input type="checkbox" />',
            'name' => __('Name'),
            'last_name' => __('Last Name'),
            'contact' => __('Phone Number'),
            'date' => __('Date'),
        ];
        return $columns;
    });

    add_action('manage_family_posts_custom_column', function ($column, $post_id) {
        switch ($column) {
            case 'name':
                echo '<a href="' . admin_url("post.php?post=$post_id&action=edit") . '">' . get_field('title', $post_id) . '</a>';
                break;
            case 'contact':
                $contact = get_field('contact', $post_id);
                echo "<a href='tel:$contact'>$contact</a>";
                break;
            default:
                break;
        }
    }, 10, 2);

    add_filter('manage_edit-family_sortable_columns', function ($columns) {
        $columns['date'] = 'date';
        $columns['last_name'] = 'last_name';
        return $columns;
    });

    add_action('load-edit.php', function () {
        add_filter('request', function ($vars) {
            if (isset($vars['post_type']) && 'family' == $vars['post_type']) {
                if (isset($vars['orderby']) && 'last_name' == $vars['orderby']) {
                    $vars = array_merge(
                        $vars,
                        [
                            'meta_key' => 'last_name',
                            'orderby' => 'meta_value meta_value_num',
                        ]
                    );
                }
            }
            return $vars;
        });
    });

    add_filter('bulk_actions-edit-family', function ($bulk_array) {
        unset($bulk_array['edit']);
        return $bulk_array;
    });

    add_filter('manage_edit-partner_columns', function (array $columns): array {
        $columns = [
            'cb' => '&lt;input type="checkbox" />',
            'name' => __('Name'),
            'date' => __('Date'),
        ];
        return $columns;
    });

    add_action('manage_partner_posts_custom_column', function ($column, $post_id) {
        switch ($column) {
            case 'name':
                echo '<a class="row-title" href="' . admin_url("post.php?post=$post_id&action=edit") . '">' . get_field('name', $post_id) . '</a>';
                break;
            default:
                break;
        }
    }, 10, 2);

    add_filter('manage_edit-partner_sortable_columns', function ($columns) {
        $columns['name'] = 'name';
        return $columns;
    });

    add_action('load-edit.php', function () {
        add_filter('request', function ($vars) {
            if (isset($vars['post_type']) && 'partner' == $vars['post_type']) {
                if (isset($vars['orderby']) && 'last_name' == $vars['orderby']) {
                    $vars = array_merge(
                        $vars,
                        [
                            'meta_key' => 'last_name',
                            'orderby' => 'meta_value meta_value_num',
                        ]
                    );
                }
            }
            return $vars;
        });
    });

    add_filter('bulk_actions-edit-partner', function ($bulk_array) {
        unset($bulk_array['edit']);
        return $bulk_array;
    });
*/

add_action('acf/render_field_settings', 'add_readonly_and_disabled_field');
function add_readonly_and_disabled_field($field)
{
    acf_render_field_setting($field, [
        'label'      => __('Read Only?', 'acf'),
        'instructions'  => '',
        'type'      => 'radio',
        'name'      => 'readonly',
        'choices'    => [
            0        => __("No", 'acf'),
            1        => __("Yes", 'acf'),
        ],
        'layout'  =>  'horizontal',
    ]);
    acf_render_field_setting($field, [
        'label'      => __('Disabled?', 'acf'),
        'instructions'  => '',
        'type'      => 'radio',
        'name'      => 'disabled',
        'choices'    => [
            0        => __("No", 'acf'),
            1        => __("Yes", 'acf'),
        ],
        'layout'  =>  'horizontal',
    ]);
}

add_action('acf/save_post', 'preload_post_id');
function preload_post_id($post_id)
{
    if ('family' != get_post_type($post_id)) return;
    update_field('id', $post_id, $post_id);
}

add_action('admin_head', function () {
    remove_action('admin_notices', 'update_nag', 3);
    remove_action('admin_notices', 'maintenance_nag', 10);
});

add_filter('update_footer', '__return_empty_string', 11);
add_filter('admin_footer_text', '__return_empty_string');
add_filter('auto_update_plugin', '__return_false');
add_filter('auto_update_theme', '__return_false');
// }

add_action('init', function () {
    $social_media = [
        [
            'name' => 'Facebook',
            'icon' => 'fab fa-facebook-f',
            'hex'  => '#3b5998',
            'url'  => 'https://www.facebook.com/pomocsrpskojdeci/',
        ],
        [
            'name' => 'Instagram',
            'icon' => 'fab fa-instagram',
            'hex'  => '#c13584',
            'url'  => 'https://www.instagram.com/pomocsrpskojdeci/',
        ],
        [
            'name' => 'LinkedIn',
            'icon' => 'fab fa-linkedin-in',
            'hex'  => '#0077b5',
            'url'  => 'https://www.linkedin.com/company/pomocsrpskojdeci/',
        ],
    ];
    add_action('rest_api_init', function () use ($social_media) {
        register_rest_route('v1', '/social-media', [
            'methods' => 'GET',
            'callback' => function () use ($social_media) {
                return $social_media;
            }
        ]);
    });
});

add_filter('wp_rest_cache/allowed_endpoints', function ($allowed_endpoints) {
    if (!isset($allowed_endpoints['v1']) || !in_array('social_media', $allowed_endpoints['v1'])) {
        $allowed_endpoints['v1'][] = 'social_media';
    }
    return $allowed_endpoints;
});