<?php

foreach (glob(dirname(__FILE__) . '/CoolApi/**/*.php') as $filename) {
  require_once $filename;
}
