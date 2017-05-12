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
   * Parameters to point conversion
   * @var array
   */
  protected $pointParams = [
    'geom' => [
      'Point' => ['lat', 'lng']
    ]
  ];

  /**
   * Parameters to coordinate object conversion
   * @var array
   */
  protected $coordParams = [
    'geom' => [
      'Point' => 'coords'
    ]
  ];

  /**
   * Test that the goemetry is built correctly from lat/lng source data
   * @param  array $source   Source object containing geometry in latitude/longitude format
   * @param  array $expected Expected converted value
   * @dataProvider pointDataProvider
   */
  public function testItBuildsPointGeometry($source, $expected)
  {
    $builder = new GeoJSON\GeometryBuilder($this->pointParams);
    $result = $builder->build($source);

    $this->assertEquals($expected, $result);
  }

  /**
   * Provide data for `testItBuildsPointGeometry`
   * @return array
   */
  public function pointDataProvider()
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
      ],
      [
        ['name' => 'Location B', 'category' => 'House', 'lat' => 39.284, 'lng' => -75.833, 'street' => 'Broad'],
        [
          'type' => 'Point',
          'coordinates' => [
            -75.833,
            39.284,
          ]
        ],
      ],
      [
        ['name' => 'Location C', 'category' => 'Office', 'lat' => 39.123, 'lng' => -74.534, 'street' => 'South'],
        [
          'type' => 'Point',
          'coordinates' => [
            -74.534,
            39.123,
          ]
        ],
      ],
    ];
  }

  /**
   * test that the geometry is built correctly from geometry object source data
   * @param  array $source   Source object containing geometry in generic format
   * @param  array $expected Expected converted value
   * @dataProvider coordinateDataProvider
   */
  public function testItBuildsCoordinateGeometry($source, $expected)
  {
    $builder = new GeoJSON\GeometryBuilder($this->coordParams);
    $result = $builder->build($source);

    $this->assertEquals($expected, $result);
  }

  /**
   * Provide data for `testItBuildsCoordinateGeometry`
   * @return array
   */
  public function coordinateDataProvider()
  {
    return [
      [
        ['name' => 'Location A', 'category' => 'Store', 'coords' => [39.984, -75.343], 'street' => 'Market'],
        [
          'type' => 'Point',
          'coordinates' => [
            39.984,
            -75.343,
          ]
        ],
      ],
      [
        ['name' => 'Location C', 'category' => 'Office', 'coords' => [39.123, -74.534], 'street' => 'South'],
        [
          'type' => 'Point',
          'coordinates' => [
            39.123,
            -74.534,
          ]
        ],
      ],
    ];
  }
}
