<?php
namespace App\Cli\Controllers;

use Frame;

use Hub\Db\Migration;

class MigrationController extends \Hub\Base\Controller
{
    public function migrate(int $steps = -1)
    {
        Migration::migrate($steps);
    }

    public function rollback(int $steps = -1)
    {
        Migration::rollback($steps);
    }
}
