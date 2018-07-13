<?php

namespace marvin255\bxcodegen\service\renderer;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\Exception;
use Twig_Environment;
use Twig_Loader_Array;

/**
 * Объекта, который обрабатывает шаблоны файлов с помощью twig.
 */
class Twig implements RendererInterface
{
    /**
     * @var \Twig_LoaderInterface
     */
    protected $twigLoader;
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @param array $twigOptions
     */
    public function __construct(array $twigOptions = [])
    {
        $this->twigLoader = new Twig_Loader_Array;
        $this->twig = new Twig_Environment($this->twigLoader, $twigOptions);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxcodegen\Exception
     */
    public function renderFile($pathToTemplateFile, CollectionInterface $options = null)
    {
        if (!file_exists($pathToTemplateFile)) {
            throw new Exception(
                "Can't find template file: {$pathToTemplateFile}"
            );
        }

        $renderName = 'from_file';
        $this->twigLoader->setTemplate($renderName, file_get_contents($pathToTemplateFile));

        try {
            $optionsArray = $options ? $options->getAll() : [];
            $return = $this->twig->load($renderName)->render($optionsArray);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $return;
    }
}
