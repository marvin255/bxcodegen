<?php

namespace marvin255\bxcodegen\service\yaml;

use marvin255\bxcodegen\Exception;
use Symfony\Component\Yaml\Yaml;

/**
 *  Объект, который парсит yaml файлы с помощью symfony yaml.
 */
class SymfonyYaml implements ReaderInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \marvin255\bxcodegen\Exception
     */
    public function parseFromFile($pathToFile)
    {
        if (!file_exists($pathToFile)) {
            throw new Exception(
                "There is no {$pathToFile} file for parsing"
            );
        }

        try {
            $return = Yaml::parseFile($pathToFile);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode(), $e);
        }

        return $return;
    }
}
