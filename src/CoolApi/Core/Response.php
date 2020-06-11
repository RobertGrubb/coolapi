<?php

namespace CoolApi\Core;

class Response {

  private $contentType = 'json';
  private $contentTypeFormatted = 'application/json';
  private $status = 200;

  /**
   * Sets the response status
   */
  public function status($status = null) {
    if (!is_null($status)) $this->status = $status;

    return $this;
  }

  /**
   * Sets the content type
   */
  public function contentType($type) {
    switch ($type) {
      case 'json':
        $this->contentType = 'json';
        $this->contentTypeFormatted = 'application/json';
        break;

      case 'html':
        $this->contentType = 'html';
        $this->contentTypeFormatted = 'text/html';
        break;

      case 'plain':
        $this->contentType = 'plain';
        $this->contentTypeFormatted = 'text/plain';
        break;
    }

    return $this;
  }

  /**
   * Outputs data depending on status
   * content type, and data passed.
   */
  public function output($data = null) {

		// If there is a status present, set header
    switch ($this->status) {
      case 400:
        header("HTTP/1.1 400 Bad Request");
        break;

      case 401:
        header("HTTP/1.1 401 Unauthorized");
        break;

      case 500:
        header("HTTP/1.1 500 Internal Server Error");
        break;

      default:
        header("HTTP/1.1 200 OK");
        break;
    }

    // Set the content type.
    header('Content-type: ' . $this->contentTypeFormatted);

    // If there is data, and the content type is JSON, output it.
		if (!is_null($data)) {
			if ($this->contentType == 'json') {
				if (is_array($data) || is_object($data)) {
					$data = json_encode($data);
				}
			}
		}

    echo $data;
		exit;
	}
}
