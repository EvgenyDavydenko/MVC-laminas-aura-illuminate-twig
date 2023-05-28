<?php
namespace App\Controllers;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Laminas\Diactoros\Response\HtmlResponse;

class BaseController {
    protected $templateEngine;

    public function __construct() {
        $loader = new FilesystemLoader('../views');
        $this->templateEngine = new Environment($loader, array(
            'debug' => true,
            'cache' => false,
        ));
    }

    public function renderHTML($fileName, $data = []) {
        return new HtmlResponse($this->templateEngine->render($fileName, $data));
    }
}