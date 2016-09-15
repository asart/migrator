<?php
namespace Migrator\MigrationWriter;

use Migrator\Factory\Config\ProviderInterface;
use DirectoryIterator;
use Migrator\MigrationWriterInterface;

class MigrationWriter implements MigrationWriterInterface
{
    /**
     * @var ProviderInterface
     */
    private $provider;

    /**
     * @var array
     */
    private $up = [];

    /**
     * @var string
     */
    private $newMigration;

    /**
     * @param ProviderInterface $provider
     */
    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function createNextVersionUp($db_name)
    {
        $folder = $this->provider->getConfig($db_name)["migrations"];

        $max = 1 + $this->getMaxVersion($folder);

        $this->newMigration = '00' . $max . '.up.' . time() . '.sql';

        if (!file_exists($folder . '/' . $this->newMigration)) {
            file_put_contents($folder . '/' . $this->newMigration, '');
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getNewMigration()
    {
        return $this->newMigration;
    }

    /**
     * @return integer max current version in database .
     * @param string $folder Folder to read from
     */
    private function getMaxVersion($folder)
    {
        foreach (new DirectoryIterator($folder) as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($this->match($file->getFilename(), $version)) {
                $this->up[] = (int)$version;
            }
        }

        if (!empty($this->up)) {
            return max($this->up);
        }

        return 0;
    }

    /**
     * Checks if the filename matches the naming convention.
     * If matches, sets $version and $direction variables
     * @param string $filename
     * @param int $version Matched version number
     * @return bool
     */
    private function match($filename, &$version)
    {
        if (preg_match('/^(?<version>\d+)\.(?<dir>up)(\..+)?\.sql$/', $filename, $match)) {
            $version = (int)$match['version'];
            return true;
        }
        return false;
    }
}
