<?php

namespace Drupal\uaf_nasa\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Tests\Core\Config\Entity\Query\QueryFactoryTest;
use Drupal\Tests\UnitTestCase;
use Drupal\uaf_nasa\NasaServiceConsumer;
use Drupal\uaf_util\RestService;
use Symfony\Component\VarDumper\Tests\Server\ConnectionTest;

/**
 * Class NasaServiceConsumerTest.
 *
 * @package Drupal\uaf_nasa\Unit
 */
class NasaServiceConsumerTest extends UnitTestCase {

  /**
   * The Drupal container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Organize our mock container.
    $this->container = new ContainerBuilder();
    $this->container->set('entity.query', new QueryFactoryTest());
    $this->container->set('database', new ConnectionTest());
    $this->container->set('uaf_util.rest', new RestService());
    $this->container->set('uaf_nasa.consumer', new NasaServiceConsumer($this->container));
    \Drupal::setContainer($this->container);
  }

  /**
   * Tests NasaServiceConsumer::loadExoplanets().
   */
  public function testLoadNasaExoplanets() {
    /** @var \Drupal\uaf_nasa\NasaServiceConsumer $nasa_service_consummer */
    $nasa_service_consummer = $this->container->get('uaf_nasa.consumer');
    $planet_name = 'HD 181433 c';
    $result = $nasa_service_consummer->loadNasaExoplanets(NULL, $planet_name);
    $this->assertEquals($planet_name, $result[0]->pl_name);
  }

  /**
   * Tests NasaServiceConsumer::isValidateDate().
   */
  public function testIsValidateDate() {

    $nasa_service_consummer = $this->container->get('uaf_nasa.consumer');
    $r = new \ReflectionMethod('\Drupal\uaf_nasa\NasaServiceConsumer', 'isValidateDate');
    $r->setAccessible(TRUE);

    $result = $r->invoke($nasa_service_consummer, '2019-01-20');
    $this->assertTrue($result);

    $result = $r->invoke($nasa_service_consummer, '2019-20-01');
    $this->assertFalse($result);
  }

}
