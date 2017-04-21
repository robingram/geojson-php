<?php
/**
 * Build "geometry" component of a GeoJSON feature
 */
namespace GeoJSON;

class CrsBuilder
{
  /**
   * GeoJSON in array format
   * @var array
   */
  protected $geoJson;

  /**
   * User defined settings
   * @var array
   */
  protected $params;

  /**
   * Constructor
   * @param array $params    User defined parameters
   */
  public function __construct($geoJson, $params = [])
  {
    $this->geoJson = $geoJson;
    $this->params = $params;
  }

  /**
   * Build CRS element from settings
   * @return array
   */
  public function build()
  {
    if ($this->checkCrs()) {
      return $this->params['crs'];
    }

    return [];
  }

  /**
   * Check that the CRS format in the setting is valid
   * @return boolean
   * @throws Exception
   */
  protected function checkCrs()
  {
    if (!isset($this->params['crs'])) return false;

    $crs = $this->params['crs'];
    if ('name' === $crs['type']) {
        if (isset($crs['properties']) && isset($crs['properties']['name'])) {
            return true;
        } else {
            throw new \Exception('Invalid CRS. Properties must contain "name" key');
        }
    } else if ($crs['type'] === 'link') {
        if (isset($crs['properties']) && isset($crs['properties']['href']) && isset($crs['properties']['type'])) {
            return true;
        } else {
            throw new \Exception('Invalid CRS. Properties must contain "href" and "type" key');
        }
    } else {
        throw new \Exception('Invald CRS. Type attribute must be "name" or "link"');
    }
  }
}
