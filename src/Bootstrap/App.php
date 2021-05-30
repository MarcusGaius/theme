<?php

namespace MarcusGaius\WordPressHeadless\Bootstrap;

use MarcusGaius\WordPressHeadless\Scripts\Scripts;
use HaydenPierce\ClassFinder\ClassFinder;

use function Env\env;

class App
{
    public static $postTypes = [];

    public $utils;

    protected $plugin = 'Pomoć Srpskoj Deci';

    protected $dashIcon = '';

    protected $namespace = 'v1';

    private static $instance = null;

    private function __construct()
    {
        $this->utils = new Utils;
        $this->runModels();
        //everything required for REST goes above this line
        if (defined('REST_REQUEST') && REST_REQUEST) {
            define('SHORTINIT', true);
            return;
        }
        //everything required for the Dashboard goes below this line
        $this->registerScripts();
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new App();
        }

        return self::$instance;
    }

    protected function runModels()
    {
        add_action('admin_menu', function () {
            add_menu_page(
                env('SITE_NAME'),
                env('SITE_NAME'),
                'manage_options',
                'wp-headless',
                [$this, 'settingsPage'],
                $this->dashIcon,
                99
            );
            add_submenu_page(
                'wp-headless',
                'Settings',
                'Settings',
                'manage_options',
                'wp-headless',
                [$this, 'settingsPage']
            );
        });
        $classes = ClassFinder::getClassesInNamespace('MarcusGaius\WordPressHeadless\Models');
        unset($classes[array_search('MarcusGaius\WordPressHeadless\Models\Model', $classes)]);
        foreach ($classes as $class) {
            $instance = new $class;
            self::$postTypes[] = $instance->getPostType();
        }
    }

    protected function registerScripts()
    {
        add_action(
            'admin_enqueue_scripts',
            function () {
                if (is_singular(self::$postTypes)) {
                    wp_enqueue_style('acf_editor', get_template_directory_uri() . '/assets/css/acf.css');
                    wp_enqueue_script('acf_editor', get_template_directory_uri() . '/assets/js/acf.js');
                }
            }
        );
        call_user_func([new Scripts, 'run']);
    }

    public function settingsPage()
    {
        echo '<h1>'.env('SITE_NAME').'</h1>';
        echo '<hr>';
        echo '<p>Placeholder za config, za slike za slajder, za tekuci racun, isl</p>';
    }
}
