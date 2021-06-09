<?php
namespace App\Controller;

abstract class AbstractController
{
    /**
     * Render a given template with given data
     *
     * @param string $path
     * @param array $data key/value, key = variable, value = content
     * @return string
     */
    public function render(string $path, array $data = null) : string
    {
        extract($data);

        ob_start();
        include($path);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}