<?php

namespace CoolApi\Core;

class RateLimiter {

  // Parent instance holder
  private $instance;

  // Database instance holder
  private $database;

  /**
   * Class construction
   */
  public function __construct ($instance) {

    // Set the parent instance
    $this->instance = $instance;

    // Set the instance of FilerDB
    $this->db = new \FilerDB\Instance([

      /**
       * This is the root path for FilerDB.
       */
      'root' => $this->instance->config->storagePath,

      /**
       * If the database does not exist, try
       * and create it.
       */
      'createDatabaseIfNotExist' => true,

      /**
       * If the collection does not exist, attempt
       * to create it.
       */
      'createCollectionIfNotExist' => true,

      /**
       * Selects the default database
       */
      'database' => 'coolapi'
    ]);

    $this->initialize();
  }

  /**
   * Do rate limit initialiation here.
   */
  private function initialize () {
    $this->increaseRate();
  }

  /**
   * Handles the increasing of the rate limit for
   * a specific host.
   */
  private function increaseRate () {

    // See if the data exists
    $exists = $this->db
      ->collection('rates')
      ->filter(['host' => $this->instance->request->remoteIp()])
      ->get();

    // If no results, insert them.
    if (count($exists) === 0) {

      return $this->db->collection('rates')->insert([
        'host' => $this->instance->request->remoteIp(),
        'requests' => 1,
        'startTime' => time()
      ]);

    // If they are in the database already
    } else {
      // Get the results
      $row = $exists[0];

      // Get rate limit settings
      $limit = $this->instance->config->rateLimit['max'];
      $span  = $this->instance->config->rateLimit['every'];

      // If they are passed their window of limitations
      if (($row->startTime + $span) < time()) {
        return $this->reset();

      // If they are within the window
      } else {

        // If they are already passed the limit, return error
        if ($row->requests >= $limit) {
          $this->instance->response->status(400)->output([
            'error' => true,
            'message' => 'Rate limit reached'
          ]);
        }

        // Update the record
        $this->db
          ->collection('rates')
          ->filter(['host' => $this->instance->request->remoteIp()])
          ->update([
            'requests' => ($row->requests + 1)
          ]);
      }
    }
  }

  /**
   * Resets them in the database.
   */
  private function reset () {
    $this->db
      ->collection('rates')
      ->filter(['host' => $this->instance->request->remoteIp()])
      ->update([
        'requests' => 1,
        'startTime' => time()
      ]);
  }
}
