<?php
require_once 'src/GeoJSON/Converter.php';

$source = getSource();
$sourceArr = json_decode($source, true);
$params = getParams();
$paramsArr = json_decode($params, true);

$parser = new GeoJSON\Converter();
$res = $parser->parse($sourceArr, $paramsArr);

echo $res . PHP_EOL;




function getSource()
{
  return <<<SOURCE
[{
  "x": 0.5,
  "y": 102.0,
  "prop0": "value0"
}, {
  "line": [
    [102.0, 0.0],
    [103.0, 1.0],
    [104.0, 0.0],
    [105.0, 1.0]
  ],
  "prop0": "value0",
  "prop1": 0.0
}, {
  "polygon": [
    [
      [100.0, 0.0],
      [101.0, 0.0],
      [101.0, 1.0],
      [100.0, 1.0],
      [100.0, 0.0]
    ]
  ],
  "prop0": "value0",
  "prop1": {
    "this": "that"
  }
}, {
  "multipoint": [
    [100.0, 0.0],
    [101.0, 1.0]
  ],
  "prop0": "value0"
}, {
  "multipolygon": [
    [
      [
        [102.0, 2.0],
        [103.0, 2.0],
        [103.0, 3.0],
        [102.0, 3.0],
        [102.0, 2.0]
      ]
    ],
    [
      [
        [100.0, 0.0],
        [101.0, 0.0],
        [101.0, 1.0],
        [100.0, 1.0],
        [100.0, 0.0]
      ],
      [
        [100.2, 0.2],
        [100.8, 0.2],
        [100.8, 0.8],
        [100.2, 0.8],
        [100.2, 0.2]
      ]
    ]
  ],
  "prop1": {
    "this": "that"
  }
}, {
  "multilinestring": [
    [
      [100.0, 0.0],
      [101.0, 1.0]
    ],
    [
      [102.0, 2.0],
      [103.0, 3.0]
    ]
  ],
  "prop0": "value1"
}]
SOURCE;
}

function getParams() {
  return <<<PARAMS
{
  "Point": ["x", "y"],
  "LineString": "line",
  "Polygon": "polygon",
  "MultiPoint": "multipoint",
  "MultiPolygon": "multipolygon",
  "MultiLineString": "multilinestring"
}
PARAMS;
}
