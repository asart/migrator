<?php
namespace Migrator\MigrationWriter;

use Migrator\Factory\Config\ProviderInterface;
use Migrator\MigrationWriterInterface;
use Exception;
use DateTime;

class MigrationWriter implements MigrationWriterInterface
{
    /**
     * @var ProviderInterface
     */
    private $provider;

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

    /**
     * @param string $db_name
     * @throws Exception
     * @return boolean
     */
    public function createNextVersionUp($db_name)
    {
        $folder = $this->provider->getConfig($db_name)["migrations"];
        $date = new DateTime();
        $this->migrationUp = $date->format(DateTime::ATOM) . '.up.sql';
        $this->migrationDown = $date->format(DateTime::ATOM) . '.down.sql';

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
}
