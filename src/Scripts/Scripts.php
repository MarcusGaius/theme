<?php

namespace MarcusGaius\WordPressHeadless\Scripts;

class Scripts
{
    protected $action = 'wp_enqueue_scripts';
    protected $params = 1;
    protected $priority = 10;
    protected $scripts = [
        [
            'type'   => 'style',
            'handle' => 'headless',
            'src'    => '/assets/css/app.css',
            'deps'   => [],
            'ver'    => false,
            'media'  => 'all',
        ]
    ];

    public function run()
    {
        add_action($this->action, function () {
            foreach ($this->scripts as $script) {
                try {
                    call_user_func(
                        sprintf('wp_enqueue_%s', $script['type']),
                        $script['handle'],
                        \get_stylesheet_directory_uri() . $script['src'],
                        $script['deps'],
                        $script['ver'],
                        $script['media']
                    );
                } catch (\Throwable $th) {
                    write_log($th->getMessage());
                }
            }
        }, $this->priority, $this->params);
    }
}
