<?php

namespace marvin255\bxcodegen\services\renderer;

use marvin255\bxcodegen\services\options\ReadOnlyInterface;
use marvin255\bxcodegen\Exception;
use Twig_Environment;

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
    public function render($template, ReadOnlyInterface $options)
    {
        try {
            $return = $this->twig->load($template)->render($options->getAll());
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $return;
    }
}
