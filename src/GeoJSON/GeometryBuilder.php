<?php
/**
 * Build "geometry" component of a GeoJSON feature
 */
namespace GeoJSON;

class GeometryBuilder
{
  /**
   * User defined settings
   * @var array
   */
  protected $params;

  /**
   * Constructor
   * @param array $params    User defined parameters
   */
  public function __construct($params = [])
  {
    $this->params = $params;
  }

  /**
   * Build a geometry array from the given 'object'
   * @param  array $object Source data
   * @return array
   */
  public function build($object)
  {
    foreach ($this->params['geom'] as $gtype => $val) {
      if (is_array($val) && isset($object[$val[0]]) && isset($object[$val[1]])) {
        return $this->getLatLong($object, $gtype, $val);
      } elseif (is_string($val) && isset($object[$val])) {
        return $this->getCoords($object, $gtype, $val);
      }
    }

    return [];
  }

  /**
   * Create the geometry if the parameter is specified as {Point: ['lat', 'lng']}
   * @param  array  $object     Source data
   * @param  string $type       Type component of geometry parameter
   * @param  string|array $val  Value of geometry component
   * @return string|array       Geometry component of GeoJSON feature
   */
  protected function getLatLong($object, $type, $val) {
      return ['type' => $type, 'coordinates' => [$object[$val[1]], $object[$val[0]]]];
  }

  /**
   * Create the geometry if the parameter is specified as {Point: 'coords'}
   * @param  array  $object     Source data
   * @param  string $type       Type component of geometry parameter
   * @param  string|array $val  Value of geometry component
   * @return string|array       Geometry component of GeoJSON feature
   */
  protected function getCoords($object, $type, $val) {
    if ('GeoJSON' == $type) {
      $geom = $object[$val];
    } else {
      $geom = ['type' => $type, 'coordinates' => $object[$val]];
    }
    return $geom;
  }
}
