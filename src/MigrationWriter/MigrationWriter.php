<?php
namespace Migrator\MigrationWriter;

use Migrator\Factory\Config\ProviderInterface;
use DirectoryIterator;
use Migrator\MigrationWriterInterface;
use Exception;

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
    private $migrationUp;

    /**
     * @var string
     */
    private $migrationDown;

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

        $info = $this->getMaxVersion($folder);

        $max = $info['version'] + 1;

        $time = time();

        $this->migrationUp = $info['character'] . $max . '.up.' . $time . '.sql';
        $this->migrationDown = $info['character'] . $max . '.down.' . $time . '.sql';

        if (!file_exists($folder . '/' . $this->migrationUp)) {
            file_put_contents($folder . '/' . $this->migrationUp, '');
        } else {
            throw new Exception('Create migration Up failed');
        }

        if (!file_exists($folder . '/' . $this->migrationDown)) {
            file_put_contents($folder . '/' . $this->migrationDown, '');
        } else {
            throw new Exception('Create migration Down failed');
        }

        return true;
    }

    /**
     * @return string
     */
    public function getMigrationUp()
    {
        return $this->migrationUp;
    }

    /**
     * @return string
     */
    public function getMigrationDown()
    {
        return $this->migrationDown;
    }

    /**
     * @return array max current version in database .
     * @param string $folder Folder to read from
     */
    private function getMaxVersion($folder)
    {
        foreach (new DirectoryIterator($folder) as $file) {
            if ($file->isDot()) {
                continue;
            }
            if ($this->match($file->getFilename(), $version, $character)) {
                $matchInfo = [
                    'version' => $version,
                    'character' => $character
                ];
                $this->up[] = $matchInfo;
            }
        }

        if (!empty($this->up)) {
            $max = 0;
            $keyMax = null;
            foreach ($this->up as $key => $value) {

                if ($value['version'] > $max) {
                    $max = $value['version'];
                    $keyMax = $key;
                }
            }

            return $this->up[$keyMax];
        }

        return [
            'version' => 1,
            'character' => '000'
        ];
    }

    /**
     * Checks if the filename matches the naming convention.
     * If matches, sets $version and $direction variables
     * @param string $filename
     * @param int $version Matched version number
     * @param int $character character in migration file number
     * @return bool
     */
    private function match($filename, &$version, &$character)
    {
        if (preg_match('/^(?<version>\d+)\.(?<dir>up)(\..+)?\.sql$/', $filename, $match)) {
            $version = (int)$match['version'];

            if (preg_match('/^(?<null>[0]+)/', $match['version'], $matches)) {
                $character = $matches['null'];
            }

            return true;
        }
        return false;
    }
}
