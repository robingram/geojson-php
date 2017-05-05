<?php
/**
 * Test the main Converter class
 * @author  Rob Ingram <robert.ingram@ccc.govt.nz>
 */
require_once 'src/GeoJSON/Converter.php';
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
  protected $converter;

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
}
