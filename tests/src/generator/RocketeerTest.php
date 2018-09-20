<?php

namespace marvin255\bxcodegen\tests\generator;

use marvin255\bxcodegen\tests\BaseCase;
use marvin255\bxcodegen\ServiceLocator;
use marvin255\bxcodegen\generator\Rocketeer;
use marvin255\bxcodegen\service\filesystem\Copier;
use marvin255\bxcodegen\service\renderer\Twig;
use marvin255\bxcodegen\service\path\PathManager;
use marvin255\bxcodegen\service\options\Collection;
use InvalidArgumentException;

class RocketeerTest extends BaseCase
{
    /**
     * @var string
     */
    protected $folderPath;

    /**
     * @test
     */
    public function testRun()
    {
        $projectName = 'project_' . mt_rand();
        $repository = 'ssh://git@git' . mt_rand() . '.ru:1111/test/test.git';
        $branch = 'test_' . mt_rand();
        $root = '/root_directory/' . mt_rand();
        $host = 'host.ru' . mt_rand();
        $username = 'username' . mt_rand();
        $password = 'password' . mt_rand();
        $key = 'key' . mt_rand();
        $keyphrase = 'keyphrase' . mt_rand();

        $options = new Collection([
            'application_name' => $projectName,
            'root_directory' => $root,
            'repository' => $repository,
            'branch' => $branch,
            'username' => $username,
            'password' => $password,
            'key' => $key,
            'keyphrase' => $keyphrase,
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath));

        $generator = new Rocketeer;
        $generator->generate($options, $locator);

        $config = "{$this->folderPath}/.rocketeer/config.php";
        $remote = "{$this->folderPath}/.rocketeer/remote.php";
        $scm = "{$this->folderPath}/.rocketeer/scm.php";

        $this->assertFileExists($config);
        $this->assertFileExists($remote);
        $this->assertFileExists($scm);

        $this->assertContains($projectName, file_get_contents($config));
        $this->assertContains($username, file_get_contents($config));
        $this->assertContains($password, file_get_contents($config));
        $this->assertContains($key, file_get_contents($config));
        $this->assertContains($keyphrase, file_get_contents($config));

        $this->assertContains($root, file_get_contents($remote));

        $this->assertContains($repository, file_get_contents($scm));
        $this->assertContains($branch, file_get_contents($scm));
    }

    /**
     * @test
     */
    public function testRunDestinationExistsException()
    {
        $options = new Collection([
            'name' => 'name',
            'root_directory' => 'root_directory',
            'repository' => 'repository',
            'branch' => 'branch',
        ]);

        $locator = new ServiceLocator;
        $locator->set('renderer', new Twig);
        $locator->set('copier', new Copier);
        $locator->set('pathManager', new PathManager($this->folderPath));

        $generator = new Rocketeer;
        $generator->generate($options, $locator);

        $this->setExpectedException(InvalidArgumentException::class);
        $generator->generate($options, $locator);
    }

    /**
     * Подготоваливает директорию для тестов.
     *
     * @throws \RuntimeException
     */
    public function setUp()
    {
        $this->folderPath = sys_get_temp_dir() . '/rocketeer';
        if (!mkdir($this->folderPath)) {
            throw new RuntimeException(
                "Can't create {$this->folderPath} folder"
            );
        }

        parent::setUp();
    }

    /**
     * Удаляет тестовую директорию и все ее содержимое.
     */
    public function tearDown()
    {
        if (is_dir($this->folderPath)) {
            $it = new \RecursiveDirectoryIterator(
                $this->folderPath,
                \RecursiveDirectoryIterator::SKIP_DOTS
            );
            $files = new \RecursiveIteratorIterator(
                $it,
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } elseif ($file->isFile()) {
                    unlink($file->getRealPath());
                }
            }
            rmdir($this->folderPath);
        }

        parent::tearDown();
    }
}
