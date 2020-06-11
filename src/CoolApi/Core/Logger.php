<?php

namespace CoolApi\Core;

class Logger {

  // Parent instance holder
  private $instance;

  // Holder for the log path
  private $logPath;

  /**
   * Class construction
   */
  public function __construct ($instance) {

    // Set the parent instance
    $this->instance = $instance;

    // Define the log path
    $root = dirname(\Composer\Factory::getComposerFile());

    // Define the logPath
    $this->logPath = isset($this->instance->config->logPath) ?
      $this->instance->config->logPath :
      $root . '/logs/';

    // Make sure it ends with a forward slash
    if (substr($this->logPath, -1) !== '/') $this->logPath .= '/';

    // Define the log file
    $this->logFile = $this->instance->config->logFile;
  }

  /**
   * Info level logging
   * @param  mixed $data
   */
  public function info ($data) {
    $this->log('info', $data);
  }

  /**
   * Warn level logging
   * @param  mixed $data
   */
  public function warn ($data) {
    $this->log('warn', $data);
  }

  /**
   * Error level logging
   * @param  mixed $data
   */
  public function error ($data) {
    $this->log('error', $data);
  }

  /**
   * Responsible for logging and appending to the
   * logFile.
   * @param  string $level
   * @param  mixed $data
   */
  public function log ($level = 'info', $data) {

    // Convert array or object to JSON
    if (is_array($data) || is_object($data)) $data = json_encode($data);

    // Format the label and the date
    $label = strtoupper($level);
    $date  = date("m.d.y G:i:s");

    // Construct the log
    $log = "[{$date}][{$label}]: {$data}\r\n";

    // Add it to the file
    file_put_contents($this->logPath . $this->logFile, $log, FILE_APPEND | LOCK_EX);
  }
}
