<?php

namespace CoolApi\Core;

class Utilities {

  public static function root () {

    // Define the log path
    $root = dirname(\Composer\Factory::getComposerFile());

    return $root;
  }

  public static function merge_config (array &$array1, array &$array2) {
    $merged = $array1;

    foreach ($array2 as $key => &$value) {
      if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
        $merged[$key] = self::merge_config($merged[$key], $value);
      } else {
        $merged[$key] = $value;
      }
    }

    return $merged;

    print_r($merged);
  }

}
