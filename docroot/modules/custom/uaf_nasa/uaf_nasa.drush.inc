<?php

/**
 * Implements hook_drush_command().
 */
function uaf_nasa_drush_command() {
  $items['import-exoplanets'] = [
    'description' => 'Import NASA exoplanets data',
  ];

  return $items;
}

/**
 * Drush function to import exoplanets from NASA.
 */
function drush_uaf_nasa_import_exoplanets() {
  $consumer = \Drupal::getContainer()->get('uaf_nasa.consumer');
  $consumer->importExoplanets();
}
