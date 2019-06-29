<?php

namespace Drupal\uaf_nasa;

use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Database\Database;
use Drupal\Core\Plugin\Factory\ContainerFactory;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NasaServiceConsumer {

  /**
   * @var \Drupal\uaf_util\RestService
   */
  private $rest;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  private $entityQuery;

  /**
   * {@inheritdoc}
   */
  public function __construct(ContainerInterface $container) {
    $this->rest = $container->get('uaf_util.rest');
    $this->connection = $container->get('database');
    $this->entityQuery = $container->get('entity.query');
  }

  /**
   * Load exoplanets from NASA.
   *
   * @param null $last_updated_date
   *   The last updated date on the exoplanet.
   *
   * @return bool|string
   */
  public function loadNasaExoplanets($last_updated_date = NULL) {
    $params = [
      'table' => 'exoplanets',
      'format' => 'json',
    ];

    if ($this->isValidateDate($last_updated_date)) {
      $params['where'] = "rowupdate>'$last_updated_date'";
    }

    $result = $this->rest->callRequest('https://exoplanetarchive.ipac.caltech.edu/cgi-bin/nstedAPI/nph-nstedAPI',
      'POST', $params);

    $result = json_decode($result);

    return $result;
  }

  /**
   * Import exoplanets from NASA.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function importExoplanets() {
    $last_updated = NULL;
    $last_updated_date = $this->connection->select('node__field_rowupdate', 'up')
      ->fields('up', ['field_rowupdate_value'])
      ->orderBy('field_rowupdate_value', 'DESC')
      ->range(0, 1)
      ->execute()
      ->fetchCol();

    if (count($last_updated_date)) {
      $last_updated = $last_updated_date[0];
    }
    // TODO remove it.
    $last_updated = '2019-06-01';
    $result = $this->loadNasaExoplanets($last_updated);
    if (!count($result)) {
      return;
    }

    foreach ($result as $item) {

      $existent_node =  $this->entityQuery->get('node')
        ->condition('title', $item->pl_name)
        ->execute();

      // Check it it's a new exoplanet or an update.
      if (count($existent_node)) {
        $nid = array_values($existent_node)[0];
        $node = Node::load($nid);
      }
      else {
        $node = Node::create([
          'type' => 'exoplanet',
          'title' => $item->pl_name,
        ]);
      }

      $fields = $node->getFields(FALSE);
      foreach ($fields as $field_name => $field) {
        $json_fiel_name = substr($field_name, strlen('field_'), strlen($field_name));
        if (isset($item->$json_fiel_name)) {
          $node->$field_name->value = $item->$json_fiel_name;
        }
      }

      $node->save();
    }
  }

  /**
   * Check if a given string is a valid date using the format Y-m-d.
   *
   * @param $date
   *   The string representing the date.
   *
   * @return bool
   */
  private function isValidateDate($date) {
    if (empty($date)) {
      return FALSE;
    }

    $d = \DateTime::createFromFormat('Y-m-d', $date);
    $is_valid = $d && $d->format('Y-m-d') === $date;

    return $is_valid;
  }

}
