<?php
/**
 * Created by PhpStorm.
 * User: aurore
 * Date: 04/01/2017
 * Time: 18:04
 */

namespace tests;
use Loader\DatabaseTwigLoader;
use PDO;

class DatabaseLoaderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Twig_Environment */
    protected $twig;

    /** @var PDO */
    protected $pdo;

    /**
     * @return string
     */
    protected function getDatabaseFile()
    {
        $directory = sys_get_temp_dir().'/phpunit-lesson-twig';

        if (!file_exists($directory)) {
            mkdir($directory);
        }

        return $directory.'/database_loader_test.sqlite';
    }

    public function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            return;
        }

        $file = $this->getDatabaseFile();

        $this->pdo = new PDO('sqlite:'.$file);

        $this->pdo->query('CREATE TABLE templates (name VARCHAR(255), source TEXT);');
        $this->pdo->query('INSERT INTO templates VALUES("hello.twig", "hello {{ name }}");');

        $this->twig = new \Twig_Environment(new DatabaseTwigLoader($this->pdo));
    }

    public function tearDown()
    {
        if (null !== $this->pdo) {
            $this->pdo->query('DROP TABLE templates;');
            $this->pdo = null;
        }
    }

    protected function skipIfNotLoadedExtensionPdoSqlite()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestIncomplete('database loaded required extension pdo_sqlite');
        }
    }

    public function testLoad()
    {
        $this->skipIfNotLoadedExtensionPdoSqlite();

        $this->assertEquals('hello john', $this->twig->render('hello.twig', array('name' => 'john')));
    }
}