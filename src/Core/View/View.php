<?php

namespace Digitaliseme\Core\View;

class View
{
    final private function __construct(
        protected string $view,
        protected array $data,
        protected string $masterTemplate,
        protected string $path,
    ) {}

    public static function make(string $view, array $data = [], string $masterTemplate = '', string $path = ''): static
    {
        if (empty($path)) {
            $path = app()->root().'/views/';
        }

        if (empty($masterTemplate)) {
            $masterTemplate = 'templates/master.php';
        }

        return new static($view, $data, $masterTemplate, $path);
    }

    public function render(): string
    {
        extract($this->data);

        ob_start();
        include $this->path."{$this->view}.php";
        $content = ob_get_clean();

        ob_start();
        include $this->path.$this->masterTemplate;
        $template = ob_get_clean();

        /** @var string $rendered */
        $rendered = str_replace('___\content/___', $content, subject: $template);

        return $rendered;
    }
}
