<?php
namespace Migrator;

interface MigrationWriterInterface
{
    /**
     * Create file with migration up and increment +1
     * @param string $db_name
     * @return bool
     */
    public function createNextVersionUp($db_name);
}
