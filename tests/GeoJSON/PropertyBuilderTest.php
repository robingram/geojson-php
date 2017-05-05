<?php
/**
 * Test the PropertyBuilder class
 * @author  Rob Ingram <robert.ingram@ccc.govt.nz>
 */
require_once 'src/GeoJSON/PropertyBuilder.php';
use PHPUnit\Framework\TestCase;

class PropertyBuilderTest extends TestCase
{
  /**
   * Properties that are considered to be geometry
   * @var array
   */
  protected $geomProps = ['Latitude', 'Longitude'];

  /**
   * Test that if there are no inclusions or exclusions, and none of the
   * properties are geometry, then they are all included in the result
   *
   * @covers ::build
   */
  public function testBuildAllIncludesProperties()
  {
    $object = [
      'test1' => 'value1',
      'test2' => 'value2',
    ];

    $builder = new GeoJSON\PropertyBuilder([], $this->geomProps);
    $this->assertEquals($object, $builder->build($object));
  }

  /**
   * Test that if there are no inclusions or exclusions, and none of the
   * properties are geometry, then they are all included in the result
   *
   * @covers ::build
   */
  public function testBuildAllExcludesGeomProperties()
  {
    $object = [
      'test1'    => 'value1',
      'Latitude' => '72.65465465',
      'test2'    => 'value2',
    ];

    $expected = [
      'test1' => 'value1',
      'test2' => 'value2',
    ];

    $builder = new GeoJSON\PropertyBuilder([], $this->geomProps);
    $this->assertEquals($expected, $builder->build($object));
  }

  /**
   * Test that if there inclusions then only those properties are
   * included in the result
   *
   * @covers ::build
   */
  public function testBuildIncludeOnlyIncludesExpectedProperties()
  {
    $object = [
      'test1' => 'value1',
      'test2' => 'value2',
    ];

    $params = [
      'include' => ['test2']
    ];

    $expected = [
      'test2' => 'value2',
    ];

    $builder = new GeoJSON\PropertyBuilder($params, $this->geomProps);
    $this->assertEquals($expected, $builder->build($object));
  }

  /**
   * Test that if there inclusions them geometry properties are still ignored
   *
   * @covers ::build
   */
  public function testBuildIncludeExcludesGeomProperties()
  {
    $object = [
      'Latitude' => '72.65465465',
    ];

    $params = [
      'include' => ['test2']
    ];

    $expected = [];

    $builder = new GeoJSON\PropertyBuilder($params, $this->geomProps);
    $this->assertEquals($expected, $builder->build($object));
  }

  /**
   * Test that if there exclusions then those properties are not
   * included in the result
   *
   * @covers ::build
   */
  public function testBuildExcludeDoesntIncludeSpecifiedProperties()
  {
    $object = [
      'test1' => 'value1',
      'test2' => 'value2',
    ];

    $params = [
      'exclude' => ['test2']
    ];

    $expected = [
      'test1' => 'value1',
    ];

    $builder = new GeoJSON\PropertyBuilder($params, $this->geomProps);
    $this->assertEquals($expected, $builder->build($object));
  }

  /**
   * Test that if there exclusions them geometry properties are still ignored
   *
   * @covers ::build
   */
  public function testBuildExcludeExcludesGeomProperties()
  {
    $object = [
      'test1'    => 'value1',
      'Latitude' => '72.65465465',
      'test2'    => 'value2',
    ];

    $params = [
      'exclude' => ['test2']
    ];

    $expected = ['test1'    => 'value1'];

    $builder = new GeoJSON\PropertyBuilder($params, $this->geomProps);
    $this->assertEquals($expected, $builder->build($object));
  }
}
