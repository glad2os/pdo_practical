<?php

use Database\PDO;

print json_encode((new PDO())->getAuthors());
