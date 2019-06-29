<?php

namespace Drupal\uaf_nasa\Controller;

use Drupal\Core\Controller\ControllerBase;

class NasaController extends ControllerBase {

  public function page() {
    /** @var \Drupal\uaf_nasa\NasaServiceConsumer $consumer */
    $consumer = \Drupal::getContainer()->get('uaf_nasa.consumer');
//    $consumer->loadNasaExoplanets('2019-01-01');
    $consumer->importExoplanets();
    return ['#markup' => 'test'];
  }

}
