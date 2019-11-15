<?php

function autoloader($class)
{
  include  strtolower($class) . '.class.php';
}

spl_autoload_register('autoloader');
