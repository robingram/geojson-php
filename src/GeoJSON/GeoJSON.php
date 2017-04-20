<?php
/**
 * Convert JSON objects to GeoJSON format
 */
namespace GeoJSON;

require_once 'PropertyBuilder.php';

class Converter
{
  public static $VERSION = '0.0.1-alpha3';

  /**
   * Default parameters to be defined by the user
   * @var array
   */
  protected $defaults;

  /**
   * List used when adding geometry attributes to features so that no
   * geometry fields are added to properties
   * @var array
   */
  protected $geomAttrs = [];

  /**
   * Attributes that are considered geometry
   * @var array
   */
  protected static $geoms = [
    'Point',
    'MultiPoint',
    'LineString',
    'MultiLineString',
    'Polygon',
    'MultiPolygon',
    'GeoJSON'
  ];

  /**
   * Property builder
   * @var GeoJSON\PropertyBuilder
   */
  protected $propertyBuilder;

  public function __construct($defaults = [])
  {
    $this->defaults = $defaults;
  }

  /**
   * Carry out the parsing
   * @param  array    $objects  Array of objects to be converted
   * @param  array    $params   Settings
   * @param  callable $callback Callback that can be called with converted data if required
   * @return array              Array representation of GeoJSON version of objects
   */
  public function parse($objects, $params, callable $callback = null)
  {
    $settings = $this->applyDefaults($params);

    $this->geomAttrs = [];

    $settings = $this->setGeom($settings);

    $geoJson = ['type' => 'FeatureCollection', 'features' => []];

    foreach ($objects as $object) {
      $geoJson['features'][] = $this->getFeature($object, $settings);
    }

    $gj = json_encode($geoJson);

    if ($callback) {
      $callback($gj);
    } else {
      return $gj;
    }
  }

  /**
   * Apply defaults but don't override user-defined settings
   * @param  array  $params User defined settings
   * @return array          User settings with defaults applied
   */
  protected function applyDefaults($params = [])
  {
    foreach ($this->defaults as $k => $v) {
      if (!isset($params[$k])) {
        $params[$k] = $v;
      }
    }
    return $params;
  }

  /**
   * Moves the user-specified geometry parameters under the `geom` key in 
   * param for easier access
   * @param  array $params User defined settings
   * @return array         Params with geometry shifted under `geom` key
   */
  protected function setGeom($params)
  {
    $geom = [];

    foreach ($params as $k => $v) {
      if (in_array($k, self::$geoms)) {
        $geom[$k] = $v;
        unset($params[$k]);
      }
    }

    $params['geom'] = $geom;
    $this->setGeomAttrs($geom);

    return $params;
  }

  /**
   * Adds fields which contain geometry data to geomAttrs
   * @param array $params Geometry attributes
   */
  protected function setGeomAttrs($params)
  {
    foreach ($params as $k => $v) {
      if (is_array($v)) {   // Point
        $this->geomAttrs[] = $v[0];
        $this->geomAttrs[] = $v[1];
      } else {
        $this->geomAttrs[] = $v;
      }
    }

    if (count($this->geomAttrs) === 0) {
      throw new Exception('No geometry attributes specified');
    }
  }

  protected function getFeature($item, $params)
  {
    $feature = ['type' => 'Feature'];
    $propBuilder = $this->getPropertyBuilder($params);

    $feature['properties'] = $propBuilder->build($item);

    return $feature;
  }

  protected function getPropertyBuilder($params)
  {
    if (is_null($this->propertyBuilder)) {
      $this->propertyBuilder = new PropertyBuilder($params, $this->geomAttrs);
    }
    return $this->propertyBuilder;
  }
}
