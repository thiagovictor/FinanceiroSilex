<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'bootstrap.php';

$entityManager = $em;

return ConsoleRunner::createHelperSet($entityManager);

