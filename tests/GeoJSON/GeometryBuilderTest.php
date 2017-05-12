<?php
/**
 * Test the GeometryBuilder class
 * @author  Rob Ingram <robert.ingram@ccc.govt.nz>
 */
require_once 'src/GeoJSON/GeometryBuilder.php';
use PHPUnit\Framework\TestCase;

class GeometryBuilderTest extends TestCase
{
  /**
   * Parameters to conversion
   * @var array
   */
  protected $params = [
    'geom' => [
      'Point' => ['lat', 'lng']
    ]
  ];

  /**
   * test that the goemetry is built correctly from the source data
   * @param  array $source   Source object containing geometry in generic formats
   * @param  array $expected Expected converted value
   * @dataProvider geometryDataProvider
   */
  public function testItBuildsGeometry($source, $expected)
  {
    $builder = new GeoJSON\GeometryBuilder($this->params);
    $result = $builder->build($source);

    $this->assertEquals($expected, $result);
  }


  /**
   * Provide data for `testitBuildsGeometry`
   * @return array
   */
  public function geometryDataProvider()
  {
    return [
      [
        ['name' => 'Location A', 'category' => 'Store', 'lat' => 39.984, 'lng' => -75.343, 'street' => 'Market'],
        [
          'type' => 'Point',
          'coordinates' => [
            -75.343,
            39.984,
          ]
        ],
        ['name' => 'Location B', 'category' => 'House', 'lat' => 39.284, 'lng' => -75.833, 'street' => 'Broad'],
        [
          'type' => 'Point',
          'coordinates' => [
            -75.343,
            39.984,
          ]
        ],
        ['name' => 'Location C', 'category' => 'Office', 'lat' => 39.123, 'lng' => -74.534, 'street' => 'South'],
        [
          'type' => 'Point',
          'coordinates' => [
            -75.343,
            39.984,
          ]
        ],
      ],
    ];
  }
}
