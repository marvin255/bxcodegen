<?php

namespace marvin255\bxcodegen\services\renderer;

use marvin255\bxcodegen\services\options\CollectionInterface;
use marvin255\bxcodegen\Exception;
use Twig_Environment;
use Twig_Loader_Array;

/**
 * Объекта, который обрабатывает шаблоны файлов с помощью twig.
 */
class Twig implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param \Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxcodegen\Exception
     */
    public function render($template, CollectionInterface $options)
    {
        $oldLoader = null;
        if (file_exists($template)) {
            $oldLoader = $this->twig->getLoader();
            $this->twig->setLoader(new Twig_Loader_Array([
                $template => file_get_contents($template),
            ]));
        }

        try {
            $return = $this->twig->load($template)->render($options->getAll());
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        if ($oldLoader) {
            $this->twig->setLoader($oldLoader);
        }

        return $return;
    }
}
