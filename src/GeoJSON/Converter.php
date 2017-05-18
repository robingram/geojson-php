<?php
/**
 * Convert JSON objects to GeoJSON format
 */
namespace GeoJSON;

require_once 'PropertyBuilder.php';
require_once 'GeometryBuilder.php';
require_once 'CrsBuilder.php';

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

  /**
   * Geometry builder
   * @var GeoJSON\GeometryBuilder
   */
  protected $geometryBuilder;

  /**
   * Crs builder
   * @var GeoJSON\CrsBuilder
   */
  protected $crsBuilder;

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

    $geoJson = $this->addOptionals($geoJson, $settings);

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
   * Adds fields that contain geometry data to geomAttrs
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
      throw new \Exception('No geometry attributes specified');
    }
  }

  /**
   * Translate the given item into a 'feature' element
   * @param  array $item   Source data
   * @param  array $params User defined parameters
   * @return array         Object transformed into GeoJSON feature format based
   *                       on parameters
   */
  protected function getFeature($item, $params)
  {
    $feature = ['type' => 'Feature'];

    $geomBuilder = $this->getGeometryBuilder($params);
    $feature['geometry'] = $geomBuilder->build($item);

    $propBuilder = $this->getPropertyBuilder($params);
    $feature['properties'] = $propBuilder->build($item);

    return $feature;
  }

  /**
   * Add optional elements to GeoJSON
   * @param  array $geoJson Existing GeoJSON
   * @param  array $params  User defined settings
   * @return array          GeoJSON with added optional elements
   */
  protected function addOptionals($geoJson, $params) {
    if (isset($params['crs'])) {
      $crsBuilder = $this->getCrsBuilder($params);
      $geoJson['crs'] = $crsBuilder->build();
    }

    if (isset($params['bbox'])) {
      $geoJson['bbox'] = $params['bbox'];
    }

    if (isset($params['extraGlobal'])) {
      $geoJson['properties'] = [];
      foreach ($params['extraGlobal'] as $k => $v) {
        $geoJson['properties'][$k] = $v;
      }
    }

    return $geoJson;
  }

  /**
   * Get the class that will build the 'geometry' component of the feature
   * @param  array $params           User defined parameters
   * @return GeoJSON\GeometryBuilder
   */
  protected function getGeometryBuilder($params)
  {
    if (is_null($this->geometryBuilder)) {
      $this->geometryBuilder = new GeometryBuilder($params);
    }
    return $this->geometryBuilder;
  }

  /**
   * Get the class that will build the remaining properties of the feature
   * @param  array $params           User defined parameters
   * @return GeoJSON\PropertyBuilder
   */
  protected function getPropertyBuilder($params)
  {
    if (is_null($this->propertyBuilder)) {
      $this->propertyBuilder = new PropertyBuilder($params, $this->geomAttrs);
    }
    return $this->propertyBuilder;
  }

  /**
   * Get a builder for optional CRS element of GeoJSON
   * @param  array $params      User defined parameters
   * @return GeoJSON\CrsBuilder
   */
  protected function getCrsBuilder($geoJson, $params)
  {
    if (is_null($this->crsBuilder)) {
      $this->crsBuilder = new CrsBuilder($geoJson, $params);
    }
    return $this->crsBuilder;
  }

    /**
     * Gets the default parameters to be defined by the user.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Sets the default parameters to be defined by the user.
     *
     * @param array $defaults the defaults
     *
     * @return self
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }
}
