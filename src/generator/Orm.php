<?php

namespace marvin255\bxcodegen\generator;

use marvin255\bxcodegen\service\options\CollectionInterface;
use marvin255\bxcodegen\service\filesystem\File;
use marvin255\bxcodegen\service\filesystem\Directory;
use marvin255\bxcodegen\ServiceLocatorInterface;
use InvalidArgumentException;

/**
 * Генератор для создания сущностей orm.
 */
class Orm extends AbstractGenerator
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function generate(CollectionInterface $options, ServiceLocatorInterface $locator)
    {
        $data = $this->collectDataFromInputForTemplate($options);

        $this->renderFile($data['model_namespace_with_name'], 'model', $locator, $data);

        if ($data['create_query']) {
            $this->renderFile($data['query_namespace_with_name'], 'query', $locator, $data);
        }
    }

    /**
     * Создает файл из шаблона и копирует его по указанному пути.
     *
     * @param string                                      $className
     * @param string                                      $template
     * @param marvin255\bxcodegen\ServiceLocatorInterface $locator
     * @param array                                       $options
     *
     * @throws \InvalidArgumentException
     */
    protected function renderFile($className, $template, ServiceLocatorInterface $locator, array $options)
    {
        $templates = dirname(dirname(__DIR__)) . '/templates/orm';

        $destinationPath = $locator->get('pathManager')->getAbsolutePathForClass($className);
        if (!$destinationPath) {
            throw new InvalidArgumentException(
                "Can't rout path for {$className} class"
            );
        }

        $source = new File("{$templates}/{$template}.phptpl");
        (new Directory(dirname($destinationPath)))->create();
        $destination = new File($destinationPath);

        if ($destination->isExists()) {
            throw new InvalidArgumentException(
                'File ' . $destination->getPathname() . ' already exists'
            );
        } else {
            $this->getAndConfigurateCopierFromLocator($locator, $options)->copyFile($source, $destination);
        }
    }

    /**
     * Собирает массив опций для шаблонов из тех опций, что пришли от пользователя.
     *
     * @param \marvin255\bxcodegen\service\options\CollectionInterface $options
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function collectDataFromInputForTemplate(CollectionInterface $options)
    {
        $table = $options->get('table');
        if (!$table) {
            throw new InvalidArgumentException('table option must be set');
        } elseif (!preg_match('/^[a-z0-9_]{3,}$/', $table)) {
            throw new InvalidArgumentException(
                'table option can consist of: latins, digits and _'
                . ", got: {$table}"
            );
        }

        $class = $options->get('class');
        if (!$class) {
            throw new InvalidArgumentException('class option must be set');
        } elseif (!preg_match('/^[\\\a-zA-Z0-9_]{3,}\\\[a-zA-Z0-9_]+Table$/', $class)) {
            throw new InvalidArgumentException(
                'class option can consist of: latins, digits, _ and \\'
                . ", ends with 'Table' suffix, got: {$class}"
            );
        }

        $explodedClass = explode('\\', $class);
        $className = array_pop($explodedClass);
        $namespace = implode('\\', $explodedClass);
        $queryClassName = substr($className, 0, strlen($className) - 5) . 'Query';

        return [
            'table' => $table,
            'namespace' => $namespace,
            'model_namespace_with_name' => $class,
            'model_name' => $className,
            'query_namespace_with_name' => $namespace . '\\' . $queryClassName,
            'query_name' => $queryClassName,
            'create_query' => (bool) $options->get('create_query', true),
        ];
    }
}
