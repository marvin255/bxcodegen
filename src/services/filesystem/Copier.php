<?php

namespace marvin255\bxcodegen\services\filesystem;

/**
 * Объект, который рекурсивно копирует содержимое одной папки в другую.
 */
class Copier implements CopierInterface
{
    /**
     * @inheritdoc
     */
    public function copyDir(DirectoryInterface $source, DirectoryInterface $destination)
    {
        $destination->create();

        foreach ($source as $child) {
            if ($child instanceof DirectoryInterface) {
                $destinationChild = $destination->createChildDirectory($child->getFoldername());
                $this->copy($child, $destinationChild);
            } else {
                $destinationChild = $destination->createChildFile($child->getBasename());
                file_put_contents(
                    $destinationChild->getPathname(),
                    file_get_contents($child->getPathname())
                );
            }
        }

        return $this;
    }
}
