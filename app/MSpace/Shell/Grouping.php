<?php
/**
 * Created by PhpStorm.
 * User: cite
 * Date: 22.11.14
 * Time: 19:38
 */
namespace MSpace\Shell;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

require 'vendor/autoload.php';

$source1 = [
	['cid' => 123, 'did' => 678, 'ten' => 'de', 'sat' => 'pl', 'day' => '1', 'revenue' => 12],
	['cid' => 123, 'did' => 678, 'ten' => 'de', 'sat' => 'pl', 'day' => '2', 'revenue' => 9],
	['cid' => 123, 'did' => 678, 'ten' => 'de', 'sat' => 'pl', 'day' => '3', 'revenue' => 7],

	['cid' => 234, 'did' => 678, 'ten' => 'de', 'sat' => 'gm', 'day' => '1', 'revenue' => 17],
	['cid' => 234, 'did' => 678, 'ten' => 'de', 'sat' => 'gm', 'day' => '2', 'revenue' => 3],
	['cid' => 234, 'did' => 678, 'ten' => 'de', 'sat' => 'gm', 'day' => '3', 'revenue' => 8],

	['cid' => 345, 'did' => 789, 'ten' => 'au', 'sat' => 'gm', 'day' => '1', 'revenue' => 8],
	['cid' => 345, 'did' => 789, 'ten' => 'au', 'sat' => 'gm', 'day' => '2', 'revenue' => 8],
	['cid' => 345, 'did' => 789, 'ten' => 'au', 'sat' => 'gm', 'day' => '3', 'revenue' => 8],
];

$source2 = [
	['did' => 678, 'geo' => 'si'],
	['did' => 789, 'geo' => 'pl'],
];


$source3 = [
	['cid' => 123, 'day' => '1', 'ar' => 0.5],
	['cid' => 123, 'day' => '2', 'ar' => 0.6],
	['cid' => 123, 'day' => '3', 'ar' => 0.7]
];

$coreDimensions = ['cid', 'did'];
$timeDimension = 'day';
$dimensions = ['sat'];
$metrics = ['revenue', 'ar'];

$dimensionParents = [
	'ten' => 'did',
	'sat' => 'cid',
	'geo' => 'did'
];

$joinDimensions = $coreDimensions;
$joinDimensions[] = $timeDimension;


global $redis;
$redis = [];


insertResult($source1, $coreDimensions, $metrics, $timeDimension, $dimensions, $dimensionParents);
insertResult($source2, ['did'], [], null, ['geo'], $dimensionParents);
insertResult($source3, ['cid'], ['ar'], $timeDimension, [], $dimensionParents);

$groupDimensions = ['geo'];

$redisRows = array_keys( $redis['rows'] );
$groupedRowIndices = [];
$groupedRows = [];
$currentRowIndex = 0;

foreach( $redisRows as $keyPrefix ) {
	$dimensionValues = [];
	$metricValues = [];

	$splittedKey = explode(':', $keyPrefix);
//
//	foreach($splittedKey as $unparsedKey) {
//		$parsedKey = explode('=', $unparsedKey);
//		$coreDimension =
//	}

	foreach($groupDimensions as $i => $dimension) {
		$parent = empty( $dimensionParents[$dimension] ) ? null : $dimensionParents[$dimension];

		if( empty($parent) ) {
			$parent = $dimension;
		}

		$keyIndex = array_search($parent, $coreDimensions);

		$coreDimensionValue = null;
		foreach($splittedKey as $keyPart) {
			$keyPart = explode('=', $keyPart);
			$coreDimensionName = $keyPart[0];

			if($coreDimensionName == $parent) {
				$coreDimensionValue = $keyPart[1];
				break;
			}
		}

		if( $dimension == $parent ) {
			$dimensionValues[$dimension] = $coreDimensionValue;
			continue;
		}

		$redisKey = $coreDimensionValue . ':' . $dimension;

		$redisValue = $redis[$redisKey];
		$dimensionValues[$dimension] = $redisValue;
	}

	$rowKey = [];
	foreach($dimensionValues as $dimensionValue) {
		$rowKey[] = $dimensionValue;

	}
	$rowKey = implode(':', $rowKey);

	foreach($metrics as $metric) {
		$redisKey = $keyPrefix . ':' . $metric;
		$metricValues[$metric] = $redis[$redisKey];
	}

	$rowIndex = isset($groupedRowIndices[$rowKey]) ? $groupedRowIndices[$rowKey] : null;
	if( !isset($rowIndex) ) {
		$rowIndex = $currentRowIndex;

		$groupedRowIndices[$rowKey] = $currentRowIndex;
		$groupedRows[$rowIndex] = [];
		$currentRowIndex++;

		foreach($groupDimensions as $dimension) {
			$groupedRows[$rowIndex][$dimension] = $dimensionValues[$dimension];
		}
		foreach($metrics as $metric) {
			$groupedRows[$rowIndex][$metric] = 0;
		}
	}

	foreach($metrics as $metric) {
		$groupedRows[$rowIndex][$metric] += $metricValues[$metric];
	}
}
$result = [];




/**
 * @param $dataSourceResult
 * @param $coreDimensions
 * @param $metrics
 * @param $timeDimension
 * @param $dimensions
 * @param $dimensionParents
 * @return array
 */
function insertResult($dataSourceResult, $coreDimensions, $metrics, $timeDimension, $dimensions, $dimensionParents) {
	global $redis;

	foreach ($dataSourceResult as $row) {
		$keyPrefix = [];

		foreach ($coreDimensions as $coreDimension) {
			$keyPrefix[] = $coreDimension . '=' . $row[$coreDimension];
			//$keyPrefix[] = $row[$coreDimension];
		}

		$keyPrefix = implode(':', $keyPrefix);

		foreach ($metrics as $metric) {
			$redisKey = $keyPrefix . ':' . $row[$timeDimension] . ':' . $metric;
			writeToRedis($redisKey, $row[$metric]);
		}

		foreach ($dimensions as $dimension) {
			$redisKey = $row[$dimensionParents[$dimension]] . ':' . $dimension;
			writeToRedis($redisKey, $row[$dimension]);
		}

		if( isset($timeDimension) ) {
			$redis['rows'][$keyPrefix . ':' . $row[$timeDimension]] = '';
		}
	}
}

function writeToRedis($key, $value) {
	global $redis;

	$redis[$key] = $value;
}
