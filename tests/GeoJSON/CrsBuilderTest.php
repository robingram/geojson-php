<?php
/**
 * Test the CrsBuilder class
 * @author  Rob Ingram <robert.ingram@ccc.govt.nz>
 */
require_once 'src/GeoJSON/CrsBuilder.php';
use PHPUnit\Framework\TestCase;

class CrsBuilderTest extends TestCase
{
  /**
   * Test that is the CRS is in a valid format it is returned as-is
   */
  public function testReturnsCrsIfValid()
  {
    $params = [
      'crs' => json_decode('{ "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" }}', true)
    ];
    $builder = new GeoJSON\CrsBuilder($params);

    $this->assertEquals($params['crs'], $builder->build());
  }

  /**
   * Test that CRS without a type is rejected
   * @expectedException \Exception
   * @expectedExceptionMessage Invalid CRS. Properties must contain "type" key
   */
  public function testRejectsCrsWithoutType()
  {
    $params = [
      'crs' => 'Hello'
    ];
    $builder = new GeoJSON\CrsBuilder($params);
    $builder->build();
  }

  /**
   * Test that CRS without a type is rejected
   * @expectedException \Exception
   * @expectedExceptionMessage Invald CRS. Type attribute must be "name" or "link"
   */
  public function testRejectsCrsWithInvalidType()
  {
    $params = [
      'crs' => ['type' => 'unknown']
    ];
    $builder = new GeoJSON\CrsBuilder($params);
    $builder->build();
  }

  /**
   * Test that CRS with a 'name' type but no name property is rejected
   * @expectedException \Exception
   * @expectedExceptionMessage Invalid CRS. Properties must contain "name" key
   */
  public function testRejectsCrsNameTypeWithoutName()
  {
    $params = [
      'crs' => json_decode('{ "type": "name", "properties": { "notaname": "urn:ogc:def:crs:OGC:1.3:CRS84" }}', true)
    ];
    $builder = new GeoJSON\CrsBuilder($params);
    $builder->build();
  }

  /**
   * Test that CRS with a 'link' type but no href property is rejected
   * @expectedException \Exception
   * @expectedExceptionMessage Invalid CRS. Properties must contain "href" and "type" key
   */
  public function testRejectsCrsLinkTypeWithoutHref()
  {
    $params = [
      'crs' => json_decode('{ "type": "link", "properties": { "notahref": "urn:ogc:def:crs:OGC:1.3:CRS84", "type": "test" }}', true)
    ];
    $builder = new GeoJSON\CrsBuilder($params);
    $builder->build();
  }

  /**
   * Test that CRS with a 'link' type but no type property is rejected
   * @expectedException \Exception
   * @expectedExceptionMessage Invalid CRS. Properties must contain "href" and "type" key
   */
  public function testRejectsCrsLinkTypeWithoutType()
  {
    $params = [
      'crs' => json_decode('{ "type": "link", "properties": { "href": "urn:ogc:def:crs:OGC:1.3:CRS84", "notatype": "test" }}', true)
    ];
    $builder = new GeoJSON\CrsBuilder($params);
    $builder->build();
  }
}
