<?php

class DataSourceTestCase extends TBSUnitTestCase {

    function __construct() {
        parent::__construct('DataSource Unit Tests');
    }

    function setUp() {
    }

    function tearDown() {
    }

    function testGetVal() {
        $data = array(
            0 => $line1 = array('a' => 1, 'b' => '2', 'c' => 'ab100', 'd' => array('1' => 't')),
            1 => $line2 = array('a' => 2, 'b' => '1', 'c' => 'ab20',  'd' => array('2' => 't')),
            2 => $line3 = array('a' => 0, 'b' => 'a', 'c' => 'ab35',  'd' => array('1' => 't')),
            3 => $line4 = array('a' => 5, 'b' => '0', 'c' => 'ab000', 'd' => array('3' => 't')),
            4 => (object)$line1,
        );
        // must pass
        $results = array(
            '0'             => array('param1' => 0, 'param2' => null, 'result' => $line1,),
            '1'             => array('param1' => 1, 'param2' => null, 'result' => $line2,),
            '[1 a]'         => array('param1' => array(1, 'a'), 'param2' => null, 'result' => 2,),
            '1 a'           => array('param1' => 1, 'param2' => 'a', 'result' => 2,),
            '[1] a'         => array('param1' => array(1), 'param2' => 'a', 'result' => 2,),
            '1 [a]'         => array('param1' => 1, 'param2' => array('a'), 'result' => 2,),
            '1 [d 2]'       => array('param1' => 1, 'param2' => array('d', 2), 'result' => 't',),
            'obj [4] [d 1]' => array('param1' => array(4), 'param2' => array('d', 1), 'result' => 't',),
        );
        foreach ($results as $str => $arr) {

            $res = clsTbsDataSource::getVal($data, $arr['param1'], $arr['param2']);

            if ($res != $arr['result']) {
                var_dump($res);
                var_dump($arr['result']);
            }
            $this->assertEqual($res, $arr['result'], 'getVal error. Task: "' . $str . '"');
        }
    }

    function testReplaceQuotedValues() {
        $this->assertEqual(
            clsTbsDataSource::ReplaceQuotedValues(
                " a random string with 'single' quotes. Values in 'quotes must be 'replaced. \n" .
                " 'Escaped \\'single \\'quotes' should not be replaced"
            ),
            array(
                " a random string with  $1  quotes. Values in  $2 replaced. \n" .
                "  $3  should not be replaced",
                array(
                    1 => array(
                        'index' => 1,
                        'start' => 22,
                        'stop' => 30,
                        'value' => 'single'
                    ),
                    2 => array(
                        'index' => 2,
                        'start' => 49,
                        'stop' => 66,
                        'value' => 'quotes must be '
                    ),
                    3 => array(
                        'index' => 3,
                        'start' => 78,
                        'stop' => 105,
                        'value' => 'Escaped \'single \'quotes'
                    ),
                )
            ),
            'FUUU'
        );

    }

    function testSortBy() {
        $data = array(
            0 => array('a' => 1, 'b' => '2', 'c' => 'ab100', 'd' => '1'),
            1 => array('a' => 2, 'b' => '1', 'c' => 'ab20',  'd' => '2'),
            2 => array('a' => 0, 'b' => 'a', 'c' => 'ab35',  'd' => '1'),
            3 => array('a' => 5, 'b' => '0', 'c' => 'ab000', 'd' => '3')
        );
        // must pass
        $results = array(
            'a'             => array(2, 0, 1, 3),
            'a as int'      => array(2, 0, 1, 3),
            'a as int asc'  => array(2, 0, 1, 3),
            'a asc'         => array(2, 0, 1, 3),
            'a as int desc' => array(3, 1, 0, 2),
            'a desc'        => array(3, 1, 0, 2),
            'a as str'      => array(2, 0, 1, 3),
            'b as str'      => array(3, 1, 0, 2),
            'b as str asc'  => array(3, 1, 0, 2),
            'b as str desc' => array(2, 0, 1, 3),
            'b as nat'      => array(3, 1, 0, 2),
            'b as int'      => version_compare('7.0', phpversion()) <= 0 ? array(2, 3, 1, 0) : array(3, 2, 1, 0), // php5: array(3, 2, 1, 0); php7: array(2, 3, 1, 0)
            'c as nat'      => array(3, 1, 2, 0),
            'd, a'                  => array(2, 0, 1, 3),
            'd as int asc, a asc'   => array(2, 0, 1, 3),
            'd as int desc, a asc'  => array(3, 1, 2, 0),
            'd as int desc, a desc' => array(3, 1, 0, 2),
            'd asc, a as int desc'  => array(0, 2, 1, 3),
            // 'E'  => array(3, 2, 1, 0),
        );
        foreach ($results as $str => $rests) {
            $this->createTbsDataSourceInstance($data);
            $this->tbs->NoErr = TRUE;
            $true = $this->dataSrc->DataSort($str);
            if (!$true) {
                if ($rests === false) {
                    $this->pass();
                } else {
                    $this->fail('SORTBY error. The function DataSort returns false. Query string: [sortby ' . $str . ']');
                }
                continue;
            }
            if ($rests === false) {
                $this->fail('SORTBY error. The function DataSort should return false in this case. Query string: [sortby ' . $str . ']');
                continue;
            }
            $result = array();
            foreach ($rests as $rkey) {
                $result[] = $data[$rkey];
            }
            $this->assertEqual($this->dataSrc->SrcId, $result, 'SORTBY error. Query string: [sortby ' . $str . ']');
        }
    }

    function testGroupBy() {
        $el1 = array('a' => 1, 'b' => 'a', 'd' => '-2');
        $el2 = array('a' => 1, 'b' => 'b', 'd' => '+8');
        $el3 = array('a' => 2, 'b' => 'b', 'd' => '+8');
        $el4 = array('a' => 2, 'b' => 'a', 'd' => '+8');
        $data = array(
            $el1,
            $el2,
            $el3,
            $el4,
        );
        // must pass
        $variant1 = array(
            array(
                'a' => 1,
                'group' => array(
                    $el1,
                    $el2,
                ),
            ),
            array(
                'a' => 2,
                'group' => array(
                    $el3,
                    $el4
                ),
            ),
        );
        $variant2 = array(
            array(
                'b' => 'a',
                'd' => '-2',
                'group' => array(
                    $el1,
                ),
            ),
            array(
                'b' => 'b',
                'd' => '+8',
                'group' => array(
                    $el2,
                    $el3,
                ),
            ),
            array(
                'b' => 'a',
                'd' => '+8',
                'group' => array(
                    $el4
                ),
            ),
        );
        $variant3 = array(
            array(
                'a' => 1,
                'b' => 'a',
                'd' => '-2',
                'group' => array(
                    $el1,
                ),
            ),
            array(
                'a' => 1,
                'b' => 'b',
                'd' => '+8',
                'group' => array(
                    $el2,
                ),
            ),
            array(
                'a' => 2,
                'b' => 'b',
                'd' => '+8',
                'group' => array(
                    $el3,
                ),
            ),
            array(
                'a' => 2,
                'b' => 'a',
                'd' => '+8',
                'group' => array(
                    $el4
                ),
            ),
        );
        $variant4 = array(
            array(
                'A' => null,
                'B' => null,
                'D' => null,
                'group' => array(
                    $el1,
                    $el2,
                    $el3,
                    $el4
                ),
            ),
        );
        $results = array(
            'a '              => $variant1,
            'a into group'    => $variant1,
            'b, d'            => $variant2,
            'd, b'            => $variant2,
            'b, d into group' => $variant2,
            'a, b, d'         => $variant3,
            'a     , b ,d'    => $variant3,
            'd, a, b'         => $variant3,
            'd, a, b, d, a'   => $variant3,
            'a,a,a,a,a,d,b'   => $variant3,
            'A,B,D'           => $variant4,
        );
        foreach ($results as $str => $result) {
            $this->createTbsDataSourceInstance($data);
            $this->tbs->NoErr = TRUE;
            $true = $this->dataSrc->DataGroup($str);
            if (!$true) {
                if ($result === false) {
                    $this->pass();
                } else {
                    $this->fail('GROUPBY error. The function DataGroup returns false. Query string: [groupby ' . $str . ']');
                }
                continue;
            }
            if ($result === false) {
                $this->fail('GROUPBY error. The function DataGroup should return false in this case. Query string: [groupby ' . $str . ']');
                continue;
            }
            // if ($this->dataSrc->SrcId != $result) {
                // print_r($result);
                // print_r($this->dataSrc->SrcId);
            // }
            $this->assertEqual($this->dataSrc->SrcId, $result, 'GROUPBY error. Query string: [groupby ' . $str . ']');
        }
    }

    function testGroupByFlags() {
        $el1 = array('a' => 1, 'b' => 'a', 'd' => '-2', 'e' => array(1, 2,));
        $el2 = array('a' => 1, 'b' => 'b', 'd' => '+8', 'e' => array(2, 3,));
        $el3 = array('a' => 2, 'b' => 'b', 'd' => '+8', 'e' => array(null, 3,));
        $el4 = array('a' => 2, 'b' => 'a', 'd' => '+8', 'e' => null);
        $data = array(
            $el1,
            $el2,
            $el3,
            $el4,
        );
        // must pass
        $variant1 = array(
            array(
                'e' => array(1, 2,),
                'group' => array(
                    $el1,
                ),
            ),
            array(
                'e' => $el2['e'],
                'group' => array(
                    $el2,
                ),
            ),
            array(
                'e' => $el3['e'],
                'group' => array(
                    $el3,
                ),
            ),
            array(
                'e' => $el4['e'],
                'group' => array(
                    $el4,
                ),
            ),
        );
        $variant2 = array(
            array(
                'group' => array(
                    $el1,
                ),
                'e' => 1,
            ),
            array(
                'group' => array(
                    $el1,
                    $el2,
                ),
                'e' => 2,
            ),
            array(
                'group' => array(
                    $el2,
                    $el3,
                ),
                'e' => 3,
            ),
            array(
                'group' => array(
                    $el3,
                    $el4,
                ),
                'e' => null,
            ),
        );
        $variant3 = array(
            array(
                'a' => 1,
                'e' => 1,
                'group' => array(
                    $el1,
                ),
            ),
            array(
                'a' => 1,
                'e' => 2,
                'group' => array(
                    $el1,
                    $el2,
                ),
            ),
            array(
                'a' => 1,
                'e' => 3,
                'group' => array(
                    $el2,
                ),
            ),
            array(
                'a' => 2,
                'e' => null,
                'group' => array(
                    $el3,
                    $el4,
                ),
            ),
            array(
                'a' => 2,
                'e' => 3,
                'group' => array(
                    $el3,
                ),
            ),
        );
        $results = array(
            'e '           => $variant1,
            'e asFlags'    => $variant2,
            'a, e asFlags' => $variant3,
        );
        foreach ($results as $str => $result) {
            $this->createTbsDataSourceInstance($data);
            $this->tbs->NoErr = TRUE;
            $true = $this->dataSrc->DataGroup($str);
            if (!$true) {
                if ($result === false) {
                    $this->pass();
                } else {
                    $this->fail('GROUPBY FLAG error. The function DataGroup returns false. Query string: [groupby ' . $str . ']');
                }
                continue;
            }
            if ($result === false) {
                $this->fail('GROUPBY FLAG error. The function DataGroup should return false in this case. Query string: [groupby ' . $str . ']');
                continue;
            }
            if ($this->dataSrc->SrcId != $result) {
                echo "\r\n<hr />\r\n";
                echo json_encode($result);
                echo "\r\n<hr />\r\n";
                echo json_encode($this->dataSrc->SrcId);
                echo "\r\n<hr />\r\n";
            }
            $this->assertEqual($this->dataSrc->SrcId, $result, 'GROUPBY FLAG error. Query string: [groupby ' . $str . ']');
        }
    }

    function testGroupByOnKeyFlag() {
        $el1 = array('a' => 1, 'b' => $val_b1 = array('key1' => 'a', 'var' => 1), 'c' => array('key2' => '-2'), 'e' => array(
            $zf1 = array('key-flag' => 1, 'z' => 'z1'),
            $zf2 = array('key-flag' => 2, 'z' => 'z2'),
        ));
        $el2 = array('a' => 1, 'b' => $val_b2 = array('key1' => 'b', 'var' => 2), 'c' => array('key2' => '+8'), 'e' => array(
            $zf3 = array('key-flag' => 2, 'z' => 'z3'),
            $zf4 = array('key-flag' => 3, 'z' => 'z4'),
        ));
        $el3 = array('a' => 2, 'b' => $val_b3 = array('key1' => 'b', 'var' => 3), 'c' => array('key2' => '+8'), 'e' => array(
            $zf5 = array('key-flag' => null, 'z' => 'z5'),
            $zf6 = array('key-flag' => 3, 'z' => 'z6'),
        ));
        $el4 = array('a' => 2, 'b' => $val_b4 = array('key1' => 'a', 'var' => 4), 'c' => array('key2' => '+8'), 'e' => array(
            $zf7 = array('key-flag' => null, 'z' => 'z7'),
        ));
        $data = array($el1, $el2, $el3, $el4);
        // must pass
        $variant1 = array(
            array(
                'group' => array($el1, $el4,),
                'b' => $val_b1,
            ),
            array(
                'group' => array($el2, $el3,),
                'b' => $val_b2,
            ),
        );
        $variant2 = array(
            array(
                'group' => array($el1),
                'e' => 1,
            ),
            array(
                'group' => array($el1, $el2),
                'e' => 2,
            ),
            array(
                'group' => array($el2, $el3),
                'e' => 3,
            ),
            array(
                'group' => array($el3, $el4),
                'e' => null,
            ),
        );
        $variant3 = array(
            array(
                'group' => array(
                    array_merge($el1, array('flfl' => $zf1)),
                ),
                'e' => 1,
            ),
            array(
                'group' => array(
                    array_merge($el1, array('flfl' => $zf2)),
                    array_merge($el2, array('flfl' => $zf3)),
                ),
                'e' => 2,
            ),
            array(
                'group' => array(
                    array_merge($el2, array('flfl' => $zf4)),
                    array_merge($el3, array('flfl' => $zf6)),
                ),
                'e' => 3,
            ),
            array(
                'group' => array(
                    array_merge($el3, array('flfl' => $zf5)),
                    array_merge($el4, array('flfl' => $zf7)),
                ),
                'e' => null,
            ),
        );
        $results = array(
            'b on key1'                  => $variant1,
            'e on key-flag asFlags'      => $variant2,
            'e on key-flag asFlags flfl' => $variant3,
        );
        foreach ($results as $str => $result) {
            $this->createTbsDataSourceInstance($data);
            $this->tbs->NoErr = TRUE;
            $true = $this->dataSrc->DataGroup($str);
            if (!$true) {
                if ($result === false) {
                    $this->pass();
                } else {
                    $this->fail('GROUPBY FLAG error. The function DataGroup returns false. Query string: [groupby ' . $str . ']');
                }
                continue;
            }
            if ($result === false) {
                $this->fail('GROUPBY FLAG error. The function DataGroup should return false in this case. Query string: [groupby ' . $str . ']');
                continue;
            }
            if ($this->dataSrc->SrcId != $result) {
                echo "\r\n<hr />\r\n";
                echo json_encode($result, JSON_PRETTY_PRINT);
                echo "\r\n<hr />\r\n";
                echo json_encode($this->dataSrc->SrcId, JSON_PRETTY_PRINT);
                echo "\r\n<hr />\r\n";
            }
            $this->assertEqual($this->dataSrc->SrcId, $result, 'GROUPBY FLAG error. Query string: [groupby ' . $str . ']');
        }
    }

    function testFilter() {
        $data = array(
            1 => (object)array(
                'id'   => 1,
                'int'  => 10,
                'mass' => '400',
                'arr'  => array(
                    'flag' => true,
                    'foo'  => 'bar',
                ),
            ),
            2 => (object)array(
                'id'   => 2,
                'int'  => 40,
                'mass' => '200',
                'arr'  => array(
                    'flag' => true,
                    'foo'  => 'baz\'s',
                ),
            ),
            3 => array(
                'id'   => 3,
                'int'  => 38,
                'mass' => '600',
                'arr'  => array(
                    'flag' => false,
                    'foo'  => true,
                ),
            ),
            4 => (object)array(
                'id'   => 4,
                'int'  => -9,
                'mass' => 600.0,
                'arr'  => array(
                    'flag' => false,
                    'foo'  => 'bar',
                ),
            ),
            5 => (object)array(
                'id'   => 0,
                'int'  => null,
                'mass' => '50',
                'arr'  => array(
                    'flag' => null,
                ),
            ),
        );
        // must pass
        $results = array(
            'id'                          => array(1, 2, 3, 4),
            '!id'                         => array(5),
            ' mass +- 500 '               => array(3, 4),
            ' mass == 600 '               => array(3, 4),
            ' mass == \'600\' '           => array(3, 4),
            ' int '                       => array(1, 2, 3, 4),
            ' !   int '                   => array(5),
            ' is_null(int) '              => array(5),
            ' arr.foo == \'bar\' '        => array(1, 4),
            '!arr.flag'                   => array(3, 4, 5),
            '  is_string(arr.foo) '       => array(1, 2, 4),
            '! is_string(arr.foo) '       => array(3, 5),
            ' |!|  |&&&|| !!&|||&&'       => false,
            ' mass +=- 599 && mass-+ 700' => array(3, 4),
            ' mass+=-100&mass-=+200'      => array(2),
            ' mass-+100|mass+-500'        => array(3, 4, 5),
            ' id &&& int &!arr.flag |||'  => array(3, 4),
            ' is_null(int)| id=3 & !arr.flag & arr.foo' => array(3, 5),
            ' mass == \'200\''            => array(2),
            ' is_array  ()'               => array(3),
            ' in_array(\'bar\', arr, 1 )' => array(1, 4),
            ' in_array(, arr, 1 )'        => array(5),
            ' arr.foo == \'baz\\\'s\''    => array(2), # WARNING: will throw error in TBS when will be used in template
        );
        foreach ($results as $str => $rests) {
            $this->createTbsDataSourceInstance($data);
            $this->tbs->NoErr = false;
            $true = $this->dataSrc->DataFilter($str);
            if (!$true) {
                if ($rests === false) {
                    $this->pass();
                } else {
                    $this->fail('BLOCK FILTER error. The function DataFilter returns false. Query string: [filter ' . $str . ']');
                }
                continue;
            }
            if ($rests === false) {
                $this->fail('BLOCK FILTER error. The function DataFilter should return false in this case. Query string: [filter ' . $str . ']');
                continue;
            }
            $result = array();
            foreach ($rests as $rkey) {
                $result[$rkey] = $data[$rkey];
            }
            $this->assertEqual($this->dataSrc->SrcId, $result, 'BLOCK FILTER error. Query string: [filter ' . $str . '] (' . implode(',', array_keys($this->dataSrc->SrcId)) . ')');
        }
    }

    function testGroupCalc() {
        $el1 = array('a' => 1, 'b' => 'a', 'd' => 'x', 'e' => 32);
        $el2 = array('a' => 2, 'b' => 'a', 'd' => 'a', 'e' => 64);
        $el3 = array('a' => 4, 'b' => 'a', 'd' => 'a', 'e' => 128);
        $el4 = array('a' => 8, 'b' => 'x', 'd' => 'a', 'e' => 16);
        $data = array(
            $el1,
            $el2,
            $el3,
            $el4,
        );
        // must pass
        $variant1 = array(
            array(
                'b' => $el1['b'],
                'group' => array(
                    $el1,
                    $el2,
                    $el3,
                ),
                'a_sum' => 7,
            ),
            array(
                'b' => $el4['b'],
                'group' => array(
                    $el4,
                ),
                'a_sum' => 8,
            ),
        );
        $variant2 = array(
            array(
                'b' => $el1['b'],
                'group' => 231,
            ),
            array(
                'b' => $el4['b'],
                'group' => 24,
            ),
        );
        $variant3 = array(
            array(
                'b' => $el1['b'],
                'd' => $el1['d'],
                'group' => 33,
            ),
            array(
                'b' => $el2['b'],
                'd' => $el2['d'],
                'group' => 198,
            ),
            array(
                'b' => $el4['b'],
                'd' => $el4['d'],
                'group' => 24,
            ),
        );
        $variant4 = array(
            array(
                'b' => $el1['b'],
                'd' => $el1['d'],
                'group' => 33,
                'group2' => 33,
            ),
            array(
                'b' => $el2['b'],
                'd' => $el2['d'],
                'group' => 198,
                'group2' => 198,
            ),
            array(
                'b' => $el4['b'],
                'd' => $el4['d'],
                'group' => 24,
                'group2' => 24,
            ),
        );
        $results = array(
            array (
                'b',
                'sum a into a_sum',
                $variant1,
            ),
            array (
                'b',
                'sum a e into group',
                $variant2,
            ),
            array (
                'b, d into group',
                'sum a,  e into group',
                $variant3,
            ),
            array (
                'b, d into group',
                'sum a,  e into group , sum e, a into group2',
                $variant4,
            ),
            array (
                'b, d into group',
                'sum a,  e into group | sum e, a into group2',
                $variant4,
            ),
            array (
                'b, d into group',
                'sum a,  e into group sum e, a into group2',
                $variant4,
            ),
        );
        foreach ($results as $item) {
            $groupStr = $item[0];
            $calcStr = $item[1];
            $result = $item[2];
            $this->createTbsDataSourceInstance($data);
            $this->tbs->NoErr = TRUE;
            $true = $this->dataSrc->DataGroup($groupStr, $calcStr);
            if (!$true) {
                if ($result === false) {
                    $this->pass();
                } else {
                    $this->fail("GROUPBY CALC error. The function DataGroup returns false. Query string: [groupby {$groupStr};groupcalc {$calcStr}]");
                }
                continue;
            }
            if ($result === false) {
                $this->fail("GROUPBY CALC error. The function DataGroup should return false in this case. Query string: [groupby {$groupStr};groupcalc {$calcStr}]");
                continue;
            }
            // if ($this->dataSrc->SrcId != $result) {
            //     print_r($result);
            //     print_r($this->dataSrc->SrcId);
            // }
            $this->assertEqual($this->dataSrc->SrcId, $result, "GROUPBY CALC error. Query string: [groupby {$groupStr};groupcalc {$calcStr}]");
        }
    }

}
