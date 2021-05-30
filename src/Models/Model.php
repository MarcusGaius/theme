<?php

namespace MarcusGaius\WordPressHeadless\Models;

class Model
{
    public $hooks = [
        [
            'hook_type' => 'action',
            'hook' => 'init',
            'callback' => 'taxonomies'
        ],
    ];

    protected $show_in_rest = true;

    protected $menu_icon = '';

    public function __construct()
    {
        $this->registerType();

        add_action('rest_api_init', function()
        {
            register_rest_field(
                strtolower($this->names['singular']),
                'acf',
                [
                    'get_callback' => function($data) {
                        return get_fields($data['id']);
                    }
                ]
            );
        });

        foreach ($this->hooks as $hook) {
            if ($hook['hook_type'] == 'action') add_action($hook['hook'], [$this, $hook['callback']]);
            if ($hook['hook_type'] == 'filter') add_filter($hook['hook'], [$this, $hook['callback']]);
        }
    }

    protected function registerType()
    {
        if (defined('REST_REQUEST') && REST_REQUEST) return;
        add_action('init', function () {
            $singular = is_array($this->names) ? $this->names['singular'] : $this->names;
            $plural = is_array($this->names) ? $this->names['plural'] : $singular;
            $labels = [
                'name' => __($plural),
                'singular_name' => __($singular),
            ];
            $args = [
                'labels' => $labels,
                'public' => true,
                'supports' => ['title'],
                'hierarchical' => false,
                'show_in_rest' => $this->show_in_rest,
                'show_in_menu' => 'wp-headless',
                'menu_icon' => $this->menu_icon,
                'rewrite' => [
                    'slug' => __(strtolower($plural))
                ],
            ];
            register_post_type(strtolower($singular), $args);
        });
    }

    public function getPostType()
    {
        return strtolower(is_array($this->names) ? $this->names['singular'] : $this->names);
    }

    public function taxonomies()
    {
        register_taxonomy_for_object_type( 'post_tag', strtolower($this->names['singular']) );
        register_taxonomy_for_object_type( 'category', strtolower($this->names['singular']) );
    }
}
