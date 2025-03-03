# TinyButStrong template engine

www.tinybutstrong.com

TBS is a PHP template engine for pro and beginners.
Only 1 class with few methods and properties, but it can do may things for any text templates, including HTML and XML.
The only engine that enables W3C compliant templates.
It has many plugins including OpenTBS for merging LibreOffice and Ms Office documents.

## Documentation

For documentation see 
http://www.tinybutstrong.com/manual.php

## Licence

TinyButStrong is released under the LGPL (Lesser General Public Licence) version 3.0.

## Distribution repository

TinyButStrong in on GitHub since version 3.5.3.

TinyButStrong versions 3.10.1 or later can be installed with [Composer](http://getcomposer.org/download/).

The package is "tinybutstrong/tinybutstrong" 

# Features:

Added sorting blocks (arrays only) by custom field as int, float, string or by natural algorithm. Also can be used for subblocks.

Added regrouping in subblocks using a custom keys (arrays only).

## Sorting

Format:

> [block...;_sortby **FieldName1**[ as **type**][ **order**][, FieldName2[ as type][ order][, ...]]_;...]

Where **order** can be ASC _(default)_ or DESC; **type** can be INT, FLOAT, STR, NAT _(default)_; **FieldName** - custom field that should be sorted.

If two elements are equal when compared by the **FieldName1**, they will compared by the **FieldName2**, etc.


### For example:

PHP: 

	$block = [
		['num' => 1,  'name' => 'killedrone'],
		['num' => 2,  'name' => 'dildodrone'],
		['num' => -10,'name' => 'othername']
	];
	$tbs->MergeBlock('myblock', $block);
	
Template:

	Just sort by num
	<div>[myblock.name;block=div;sortby num] - [myblock.num]</div>
	
	Sort by name desc
	<div>[myblock.name;block=div;sortby name as str desc] - [myblock.num]</div>
	
	Sort by num asc and name desc
	<div>[myblock.name;block=div;sortby num as int asc, name as nat desc] - [myblock.num]</div>




Also you can add custom compare functions: just add custom or replace existing **type**:



Template:

	Sort by unsigned num
	<div>[myblock.name;block=div;sortby num as abs] - [myblock.num]</div>
	Sort by num using function 'myCallable'
	<div>[myblock.name;block=div;sortby num as cust] - [myblock.num]</div>


## Grouping

Format:

> [block...;_groupby **FieldName1**[ asFlags][, FieldName2[ asFlags]][, ...][ into **GroupName**]_;...]

Where the paramenter **FieldName** is the custom field (key) that will be used for grouping.
The parameter **GroupName** is the key in which the array will be written ("group" by default).

The parameter **asFlags** implies that the elements will be grouped by each value as an individual value

### For example:

PHP:

	$block = [
		['player' => 'Player 1',  'level' => 1],
		['player' => 'Player 2',  'level' => 2],
		['player' => 'Player 3',  'level' => 2],
		['player' => 'Player 4',  'level' => 1],
		['player' => 'Player 5',  'level' => 2]
	];
	$tbs->MergeBlock('myblock', $block);

Template:

	<div>
		<p>Players at the <b>Level [myblock.level;block=div;groupby level into players;sub1=players]</b>:</p>
		<ul>
			<li>[myblock_sub1.player;block=li]</li>
		</ul>
	</div>

Result:

	<div>
		<p>Players at the <b>Level 1</b>:</p>
		<ul>
			<li>Player 1</li>
			<li>Player 4</li>
		</ul>
	</div>
	<div>
		<p>Players at the <b>Level 2</b>:</p>
		<ul>
			<li>Player 2</li>
			<li>Player 3</li>
			<li>Player 5</li>
		</ul>
	</div>

### Calculating when groupby:

> [block...;groupby ...;_groupcalc **order** **FieldName1** [, **FieldName2**][ ...] into  **resultFieldName**]

The primary parameter **order** is the name of the calculating's rule. It may be **sum** (default), **count** or custom function.
One or more parameters **FieldName#** will be used in calculations.
The parameter **resultFieldName** determines where (what field name) the result will be placed.

You can add the custom calculating rule:

	clsTbsDataSource::$CalcOrders['sum'] = 'clsTbsDataSource::DataCalcSum'; // must be callable
	// or
	clsTbsDataSource::$CalcOrders['sum'] = function ($data) {
		$result = 0;
		foreach ($data as &$item) {
		    $result += array_sum($item);
		}
		return $result;
	};

## Grouping when values are objects or arrays

Format:

> [...;block...;_groupby FieldName1 [ on **key** ][ asFlags [ **flagFieldName** ]], ..., [ into GroupName]_;...]

## Filter

> [myBlock;block=...;_filter some.property || callable() && prop.subprop +- 0 || in_array('foo', prop, 1) || !is_null(prop1)_;...]

&, && = AND \
|, || = OR \
__some.property__ - compare property as bool \
__callable()__ - run callable($element); All callable functions collect in `clsTbsDataSource::$FilterOrders` \
__prop.subprop +- 0__ - conditions... [see about +- -+ +=- -=+ and other](http://www.tinybutstrong.com/manual.php#html_field_prm_if) \
__in_array('foo', prop, 1)__ - run callable with custom params \
__!is_null(prop1)__ - you can use `!` before callable or property as logical NOT: `!callable()`, `!property`

---

/!\ for more information, see the tests: `testunit/testcase/DataSourceTestCase.php` \
Compatible with php 5 and php 7!\
To run the test, run `composer test`. But before that run `composer update` \

