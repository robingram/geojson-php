<?php
/**
 * Test the main Converter class
 * @author  Rob Ingram <robert.ingram@ccc.govt.nz>
 */
require_once 'src/GeoJSON/Converter.php';
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
  /**
   * Converter object to test
   * @var GeoJSON\Converter
   */
  protected $converter;

  /**
   * Data set on which most of the tests will act
   * @var array
   */
  protected $defaultData = [
    ['name' => 'Location A', 'category' => 'Store', 'lat' => 39.984, 'lng' => -75.343, 'street' => 'Market'],
    ['name' => 'Location B', 'category' => 'House', 'lat' => 39.284, 'lng' => -75.833, 'street' => 'Broad'],
    ['name' => 'Location C', 'category' => 'Office', 'lat' => 39.123, 'lng' => -74.534, 'street' => 'South']
  ];

  /**
   * Parameters for point geometry
   * @var array
   */
  protected $pointParams = ['Point' => ['lat', 'lng']];

  public function setUp()
  {
    $this->converter = new GeoJSON\Converter();
  }

  /**
   * Test that we can get and set defaults
   *
   * @covers ::getDefaults
   * @covers ::setDefaults
   */
  public function testDefaultsShouldBeAccessible()
  {
    $data = ['test' => 'value'];
    $this->converter->setDefaults($data);
    $this->assertEquals($this->converter->getDefaults(), $data);
  }

  /**
   * Test that unless set the defaults are an empty array
   *
   * @covers ::__construct
   * @covers ::getDefaults
   */
  public function testDefaultsShouldBeEmptyInitially()
  {
    $this->assertEquals($this->converter->getDefaults(), []);
  }

  /**
   * Test that the geoJSON contains the same number of features as the source data
   */
  public function testShouldReturnCorrectNumberOfFeatures()
  {
    $geoJson = $this->converter->parse($this->defaultData, $this->pointParams);
    $geoJson = json_decode($geoJson, true);

    $this->assertCount(3, $geoJson['features']);
  }

  /**
   * Test that geometry properties are properly removed from the source data
   * and converted to coordinates
   */
  public function testShouldNotIncludeGeometryInFeatures()
  {
    $geoJson = $this->converter->parse($this->defaultData, $this->pointParams);
    $geoJson = json_decode($geoJson, true);

    foreach ($geoJson['features'] as $feature) {
      $this->assertArrayNotHasKey('lat', $feature);
      $this->assertArrayNotHasKey('lng', $feature);
      $this->assertCount(2, $feature['geometry']['coordinates']);
    }
  }

  /**
   * Test that the class handles being passed a variety of geometry
   */
  public function testShouldProcessMultipleGeometryTypes()
  {
    $geoJson = $this->converter->parse($this->getMultiData(), $this->getMultiParams());
    $geoJson = json_decode($geoJson, true);

    $this->assertEquals($this->getMultiExpected(), $geoJson['features']);
  }

  /**
   * Source data for multi-conversion test
   * @return array
   */
  protected function getMultiData()
  {
    return [
        [
          'x' => 0.5,
          'y' => 102.0,
          'prop0' => 'value0'
        ],
        [
          'line' => [[102.0, 0.0], [103.0, 1.0], [104.0, 0.0], [105.0, 1.0]],
          'prop0' => 'value0',
          'prop1' => 0.0
        ],
        [
          'polygon' => [
            [ [100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0] ]
          ],
          'prop0' => 'value0',
          'prop1' => ["this" => "that"]
        ],
        [
          'multipoint' => [
            [100.0, 0.0], [101.0, 1.0]
          ],
          'prop0' => 'value0'
        ],
        [
          'multipolygon' => [
            [[[102.0, 2.0], [103.0, 2.0], [103.0, 3.0], [102.0, 3.0], [102.0, 2.0]]],
            [[[100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0]],
             [[100.2, 0.2], [100.8, 0.2], [100.8, 0.8], [100.2, 0.8], [100.2, 0.2]]]
          ],
          'prop1' => ['this' => 'that']
        ],
        [
          'multilinestring' => [
            [ [100.0, 0.0], [101.0, 1.0] ],
            [ [102.0, 2.0], [103.0, 3.0] ]
          ],
          'prop0' => 'value1'
        ]
    ];
  }

  /**
   * Params for multi-conversion test
   * @return array
   */
  protected function getMultiParams()
  {
    return [
      'Point' => ['x', 'y'],
      'LineString' => 'line',
      'Polygon' => 'polygon',
      'MultiPoint' => 'multipoint',
      'MultiPolygon' => 'multipolygon',
      'MultiLineString' => 'multilinestring'
    ];
  }

  /**
   * Expectations for multi-conversion test
   * @return array
   */
  protected function getMultiExpected()
  {
    return [

      [
        'type' => 'Feature',
        'geometry' => [
          'type' => 'Point',
          'coordinates' => [102, 0.5]
        ],
        'properties' => ['prop0' => 'value0'],
      ],

      [
        'type' => 'Feature',
        'geometry' => [
          'type' => 'LineString',
          'coordinates' => [[102, 0], [103, 1], [104, 0], [105, 1]]
        ],
        'properties' => ['prop0' => 'value0', 'prop1' => 0],
      ],

      [
        'type' => 'Feature',
        'geometry' => [
          'type' => 'Polygon',
          'coordinates' => [[[100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0]]]
        ],
        'properties' => ['prop0' => 'value0', 'prop1' => ["this" => "that"]],
      ],

      [
        'type' => 'Feature',
        'geometry' => [
          'type' => 'MultiPoint',
          'coordinates' => [[100.0, 0.0], [101.0, 1.0]]
        ],
        'properties' => ['prop0' => 'value0'],
      ],

      [
        'type' => 'Feature',
        'geometry' => [
          'type' => 'MultiPolygon',
          'coordinates' => [[[[102.0, 2.0], [103.0, 2.0], [103.0, 3.0], [102.0, 3.0], [102.0, 2.0]]],
            [[[100.0, 0.0], [101.0, 0.0], [101.0, 1.0], [100.0, 1.0], [100.0, 0.0]],
             [[100.2, 0.2], [100.8, 0.2], [100.8, 0.8], [100.2, 0.8], [100.2, 0.2]]]]
        ],
        'properties' => ['prop1' => ["this" => "that"]],
      ],

      [
        'type' => 'Feature',
        'geometry' => [
          'type' => 'MultiLineString',
          'coordinates' => [[ [100.0, 0.0], [101.0, 1.0] ],
            [ [102.0, 2.0], [103.0, 3.0] ]]
        ],
        'properties' => ['prop0' => 'value1'],
      ]
    ];
  }
}
