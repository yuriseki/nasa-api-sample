<?php

namespace Drupal\uaf_util;

/**
 * Class RestService.
 *
 * @package Drupal\uaf_util
 */
class RestService {

  /**
   * Call a request.
   *
   * @param string $requesturl
   *   The request service URL.
   * @param string $method
   *   The method to be used/.
   * @param array $params
   *   Additional parameters.
   *
   * @return bool|string
   *   The request returned.
   */
  public function callRequest($requesturl, $method, array $params = []) {

    $url = $requesturl;

    try {

      $ch = curl_init();

      $headers[] = 'Accept: application/json';
      $headers[] = 'Accept-Encoding: gzip';
      $headers[] = 'RequestId: ' . uniqid();
      $headers[] = 'Cache-Control: no-cache';
      $headers[] = 'Pragma: no-cache';

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

      $result = curl_exec($ch);
      curl_close($ch);

      return $result;
    }
    catch (\Exception $e) {
      echo $e->getMessage();
    }
  }

}
