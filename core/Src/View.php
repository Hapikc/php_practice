<?php

namespace Src;

use Exception;

class View
{
    private string $view = '';
    private array $data = [];
    private string $root = '';
    private string $layout = '/layouts/main.php';

    public function __construct(string $view = '', array $data = [])
    {
        $this->root = $this->getRoot();
        $this->view = $view;
        $this->data = $data;
    }

    //Полный путь до директории с представлениями
    private function getRoot(): string
    {
        global $app;
        $root = $app->settings->getRootPath();
        $path = $app->settings->getViewsPath();

        return $_SERVER['DOCUMENT_ROOT'] . $root . $path;
    }

    //Путь до основного файла с шаблоном сайта
    private function getPathToMain(): string
    {
        return $this->root . $this->layout;
    }

    //Путь до текущего шаблона
    private function getPathToView(string $view = ''): string
    {
        $view = str_replace('.', '/', $view);
        return $this->getRoot() . "/$view.php";
    }

    public function render(string $view = '', array $data = []): string
    {
        $path = $this->getPathToView($view);
        $mainPath = $this->getPathToMain();

        if (!file_exists($mainPath)) {
            throw new Exception("Main layout not found: $mainPath");
        }

        if (!file_exists($path)) {
            throw new Exception("View not found: $path");
        }

        extract($data, EXTR_PREFIX_SAME, '');
        ob_start();
        require $path;
        $content = ob_get_clean();
        return require($mainPath);
    }

    public function __toString(): string
    {
        return $this->render($this->view, $this->data);
    }
}
