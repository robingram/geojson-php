<?php
/**
 * Build "property" component of a GeoJSON feature
 */
namespace GeoJSON;

class PropertyBuilder
{
  /**
   * User defined settings
   * @var array
   */
  protected $params;

  /**
   * List of geometry attribute types
   * @var array
   */
  protected $geomAttrs;

  /**
   * Constructor
   * @param array $params    User defined parameters
   * @param array $geomAttrs Geometry attributes. These will be ignored
   */
  public function __construct($params = [], $geomAttrs = [])
  {
    $this->params = $params;
    $this->geomAttrs = $geomAttrs;
  }

  /**
   * Build a properties array from the given 'object'
   * @param  array $object Source data
   * @return array         Required properties
   */
  public function build($object)
  {
    if (!isset($this->params['include']) && !isset($this->params['exclude'])) {
      return $this->buildAll($object);
    } elseif (isset($this->params['include'])) {
      return $this->buildInclude($object);
    } elseif (isset($this->params['exclude'])) {
      return $this->buildExclude($object);
    }
    return [];
  }

  /**
   * Build the properties array when no properties are set to be explicitly
   * included or excluded
   * @param  array $object Source data
   * @return array         All non-geometry properties
   */
  protected function buildAll($object)
  {
    $props = [];

    foreach($object as $k => $v) {
      if (!in_array($k, $this->geomAttrs)) {
        $props[$k] = $v;
      }
    }

    if (isset($this->params['extra'])) {
      $props = $this->addExtra($props);
    }

    return $props;
  }

  /**
   * Build the properties array when some properties are set to be explicitly
   * included
   * @param  array $object Source data
   * @return array         Defined properties
   */
  protected function buildInclude($object)
  {
    $props = [];

    foreach($this->params['include'] as $val) {
      if (isset($object[$val])) {
        $props[$val] = $object[$val];
      }
    }
    return $props;
  }

  /**
   * Build the properties array when some properties are set to be explicitly
   * excluded
   * @param  array $object Source data
   * @return array         Properties except those explicitly excludes
   */
  protected function buildExclude($object)
  {
    $props = [];

    foreach($object as $k => $v) {
      if (!in_array($k, $this->geomAttrs) && !in_array($k, $this->params['exclude'])) {
        $props[$k] = $v;
      }
    }
    return $props;
  }

  /**
   * Add static extra properties
   * @param array $props Properties with 'extra' values added
   */
  protected function addExtra($props) {
    foreach ($this->params['extra'] as $k => $v) {
      $props[$k] = $v;
    }
    return $props;
  }
}
