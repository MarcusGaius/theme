<?php

namespace MarcusGaius\WordPressHeadless\Models;

class Activity extends Model
{
    protected $names = [
        'singular' => 'Activity',
        'plural' => 'Activities'
    ];

    public function __construct()
    {
        parent::__construct();

        add_action('rest_api_init', function () {
            register_rest_field(
                strtolower($this->names['singular']),
                'donation',
                [
                    'get_callback' => function ($data) {
                        global $wpdb;
                        $post_id = $data['id'];
                        return $wpdb->get_results("SELECT * FROM {$wpdb->postmeta} WHERE `post_id` in (SELECT `post_id` FROM {$wpdb->postmeta} WHERE `meta_key` = 'svrha_donacije' and `meta_value` = {$post_id})");
                    }
                ]
            );
        });
    }
}
