<?php

class AttTestCase extends TBSUnitTestCase {

	function AttTestCase() {
		$this->UnitTestCase('Att option Unit Tests');
	}

	function setUp() {
	}

	function tearDown() {
	}

	function testFields() {
		// tests with existing attribute with double quotes
		$this->assertEqualMergeFieldStrings("<div class=\"test1\" att1 att2=v2 att3>hello[move;att=class]</div>", array('move'=>'test2'), "<div class=\"test2\" att1 att2=v2 att3>hello</div>", "test fields #1");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\" att1 att2=v2 att3>hello[move;att=att1]</div>", array('move'=>'test2'), "<div class=\"test1\" att1=\"test2\" att2=v2 att3>hello</div>", "test fields #2");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\" att1 att2=v2 att3>hello[move;att=att2]</div>", array('move'=>'test2'), "<div class=\"test1\" att1 att2=test2 att3>hello</div>", "test fields #3");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\" att1 att2=v2 att3>hello[move;att=att3]</div>", array('move'=>'test2'), "<div class=\"test1\" att1 att2=v2 att3=\"test2\">hello</div>", "test fields #4");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\" att1 att2=v2 att3 >hello[move;att=att3]</div>", array('move'=>'test2'), "<div class=\"test1\" att1 att2=v2 att3=\"test2\" >hello</div>", "test fields #5");

		// tests with existing attribute with simple quotes
		$this->assertEqualMergeFieldStrings("<div class='test1' att1 att2=v2 att3 >hello[move;att=att3]</div>", array('move'=>'test2'), "<div class='test1' att1 att2=v2 att3='test2' >hello</div>", "test fields #6");
		$this->assertEqualMergeFieldStrings("<div class='test1' att1 att2=v2 att3=\"\">hello[move;att=att3]</div>", array('move'=>'test2'), "<div class='test1' att1 att2=v2 att3=\"test2\">hello</div>", "test fields #7");

		// tests with existing attribute with tag specification
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=+span#class] Mr. <span class=\"effect\">Patatoe</span></div>", array('move'=>'test'), "<div class=\"test1\">hello Mr. <span class=\"test\">Patatoe</span></div>", "test fields #8");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=+span#class] Mr. <span class=\"effect\">Patatoe</span></div>", array('move'=>'test'), "<div class=\"test1\"><span class=\"hello\">hello</span> Mr. <span class=\"test\">Patatoe</span></div>", "test fields #9");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=+span#class] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>'test'), "<div class=\"test1\"><span class=\"hello\">hello</span> <span class=\"test\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #10");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=div#class] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>'test2'), "<div class=\"test2\"><span class=\"hello\">hello</span> <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #11");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=div#class] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>''), "<div class=\"\"><span class=\"hello\">hello</span> <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #12");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=span#class] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>''), "<div class=\"test1\"><span class=\"\">hello</span> <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #13");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=+span#class] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>''), "<div class=\"test1\"><span class=\"hello\">hello</span> <span class=\"\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #14");

		// tests with existing attribute with autoclose tag
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><img width=\"50\" height=\"50\" />[move;att=img#width]hello</div>", array('move'=>'32'), "<div class=\"test1\"><img width=\"32\" height=\"50\" />hello</div>", "test fields #15");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">[move;att=+img#width]<img width=\"50\" height=\"50\" />hello</div>", array('move'=>'32'), "<div class=\"test1\"><img width=\"32\" height=\"50\" />hello</div>", "test fields #16");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><img width=\"50\" height=\"50\"[move;att=img#width] />hello</div>", array('move'=>'32'), "<div class=\"test1\"><img width=\"32\" height=\"50\" />hello</div>", "test fields #17");

		// tests with existing attribute with autoclose tag without ending space
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><img width=\"50\" height=\"50\"/>[move;att=img#width]hello</div>", array('move'=>'32'), "<div class=\"test1\"><img width=\"32\" height=\"50\"/>hello</div>", "test fields #18");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">[move;att=+img#width]<img width=\"50\" height=\"50\"/>hello</div>", array('move'=>'32'), "<div class=\"test1\"><img width=\"32\" height=\"50\"/>hello</div>", "test fields #19");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><img width=\"50\" height=\"50\"[move;att=img#width]/>hello</div>", array('move'=>'32'), "<div class=\"test1\"><img width=\"32\" height=\"50\"/>hello</div>", "test fields #20");

		// tests with missing attribute
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=id]</div>", array('move'=>'test2'), "<div class=\"test1\" id=\"test2\">hello</div>", "test fields #21");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=+span#class] Mr. <span>Patatoe</span></div>", array('move'=>'test'), "<div class=\"test1\">hello Mr. <span class=\"test\">Patatoe</span></div>", "test fields #22");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span>hello</span>[move;att=+span#class] Mr. <span>Patatoe</span></div>", array('move'=>'test'), "<div class=\"test1\"><span>hello</span> Mr. <span class=\"test\">Patatoe</span></div>", "test fields #23");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span>hello</span>[move;att=+span#class] <span>Mr.</span> <span>Patatoe</span></div>", array('move'=>'test'), "<div class=\"test1\"><span>hello</span> <span class=\"test\">Mr.</span> <span>Patatoe</span></div>", "test fields #24");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><img width=\"50\" height=\"50\" />[move;att=img#class]hello</div>", array('move'=>'center'), "<div class=\"test1\"><img width=\"50\" height=\"50\" class=\"center\" />hello</div>", "test fields #25");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><img width=\"50\" height=\"50\"/>[move;att=img#class]hello</div>", array('move'=>'center'), "<div class=\"test1\"><img width=\"50\" height=\"50\" class=\"center\"/>hello</div>", "test fields #26");

		// tests removing an attribute
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=id;magnet=#]</div>", array('move'=>''), "<div class=\"test1\">hello</div>", "test fields #27");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=class;magnet=#]</div>", array('move'=>''), "<div>hello</div>", "test fields #28");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=class;magnet=#]</div>", array('move'=>null), "<div>hello</div>", "test fields #29");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=class;magnet=#]</div>", array('move'=>false), "<div>hello</div>", "test fields #30");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=div#class;magnet=#] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>''), "<div><span class=\"hello\">hello</span> <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #31");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=span#class;magnet=#] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>''), "<div class=\"test1\"><span>hello</span> <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #32");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=+span#class;magnet=#] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>''), "<div class=\"test1\"><span class=\"hello\">hello</span> <span>Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #33");

		// tests for 'attadd' option
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=id;attadd]</div>", array('move'=>'test2'), "<div class=\"test1\" id=\"test2\">hello</div>", "test fields #34");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=class;attadd]</div>", array('move'=>'test2'), "<div class=\"test1 test2\">hello</div>", "test fields #35");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\">hello[move;att=class;attadd]</div>", array('move'=>''), "<div class=\"test1\">hello</div>", "test fields #36");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=div#class;attadd] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>'test2'), "<div class=\"test1 test2\"><span class=\"hello\">hello</span> <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #37");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=span#class;attadd] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>'test2'), "<div class=\"test1\"><span class=\"hello test2\">hello</span> <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #38");
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=+span#class;attadd] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>'test2'), "<div class=\"test1\"><span class=\"hello\">hello</span> <span class=\"mr test2\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test fields #39");
	}

	function testMergeBlocks() {
		$blk = array();
		$blk[] = array('id'=>'AttCache x12', 'val'=>'effect1');
		$blk[] = array('id'=>'AttCache x13', 'val'=>'effect2');
		$blk[] = array('id'=>'AttCache x14', 'val'=>'effect3');
		$blk[] = array('id'=>'AttCache x15', 'val'=>'effect4');

		// test normal
		$this->assertEqualMergeBlockFiles('att_test1.html', array('blk'=>$blk), 'att_test1_result.html', "test blocks #1");

		// test normal with tag specification
		$this->assertEqualMergeBlockFiles('att_test2.html', array('blk'=>$blk), 'att_test2_result.html', "test blocks #2");

		// test normal with parent tag specification
		$this->assertEqualMergeBlockFiles('att_test3.html', array('blk'=>$blk), 'att_test3_result.html', "test blocks #2");
	}

	function testOnShowMagnet() {
		// tests with 'onshow/magnet' directive with an empty value
		$GLOBALS['x'] = '';
		$this->assertEqualMergeFieldStrings("<div>[onshow.x;att=id;magnet=#]</div>", array(), "<div></div>", "test fields #40");
		$this->assertEqualMergeFieldStrings("<div >[onshow.x;att=id;magnet=#]</div>", array(), "<div ></div>", "test fields #41");
		$this->assertEqualMergeFieldStrings("<div class=test>hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=test>hello</div>", "test fields #42");
		$this->assertEqualMergeFieldStrings("<div class=\"test\">hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=\"test\">hello</div>", "test fields #43");
		$this->assertEqualMergeFieldStrings("<div class=test >hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=test >hello</div>", "test fields #44");
		$this->assertEqualMergeFieldStrings("<div class=\"test\" >hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=\"test\" >hello</div>", "test fields #45");
		$this->assertEqualMergeFieldStrings("<div class=test id=22>hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=test>hello</div>", "test fields #46");
		$this->assertEqualMergeFieldStrings("<div class=\"test\" id=\"22\">hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=\"test\">hello</div>", "test fields #47");
		$this->assertEqualMergeFieldStrings("<div class=test id=22 >hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=test >hello</div>", "test fields #48");
		$this->assertEqualMergeFieldStrings("<div class=\"test\" id=\"22\" >hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=\"test\" >hello</div>", "test fields #49");
		$this->assertEqualMergeFieldStrings("<div id=22 class=test>hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=test>hello</div>", "test fields #50");
		$this->assertEqualMergeFieldStrings("<div id=\"22\" class=\"test\">hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=\"test\">hello</div>", "test fields #51");
		$this->assertEqualMergeFieldStrings("<div id=22 class=test >hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=test >hello</div>", "test fields #52");
		$this->assertEqualMergeFieldStrings("<div id=\"22\" class=\"test\" >hello[onshow.x;att=id;magnet=#]</div>", array(), "<div class=\"test\" >hello</div>", "test fields #53");
		$this->assertEqualMergeFieldStrings("<div id=22>hello[onshow.x;att=id;magnet=#]</div>", array(), "<div>hello</div>", "test fields #54");
		$this->assertEqualMergeFieldStrings("<div id=\"22\">hello[onshow.x;att=id;magnet=#]</div>", array(), "<div>hello</div>", "test fields #55");
		$this->assertEqualMergeFieldStrings("<div id=22 >hello[onshow.x;att=id;magnet=#]</div>", array(), "<div >hello</div>", "test fields #56");
		$this->assertEqualMergeFieldStrings("<div id=\"22\" >hello[onshow.x;att=id;magnet=#]</div>", array(), "<div >hello</div>", "test fields #57");
	}

	function testBugs() {
		// merge error when tbs directive is inside the html tag
		$this->assertEqualMergeFieldStrings("<div id=\"\" class=\"test1[move;att=class]\">hello</div>", array('move'=>'test2'), "<div id=\"\" class=\"test2\">hello</div>", "test bug #1");
		$this->assertEqualMergeFieldStrings("<div id=\"[move;att=class]\" class=\"test1\">hello</div>", array('move'=>'test2'), "<div id=\"\" class=\"test2\">hello</div>", "test bug #2");
		$this->assertEqualMergeFieldStrings("<div[move;att=class] id=\"\" class=\"test1\">hello</div>", array('move'=>'test2'), "<div id=\"\" class=\"test2\">hello</div>", "test bug #3");
		$this->assertEqualMergeFieldStrings("<div id=\"\" class=\"test1\"[move;att=class]>hello</div>", array('move'=>'test2'), "<div id=\"\" class=\"test2\">hello</div>", "test bug #4");

		// space bug: merging attribute must add quote because some attribute like 'class' could have several values separate by a space
		// http://sourceforge.net/tracker/?func=detail&aid=3134436&group_id=324877&atid=1364379
		$this->assertEqualMergeFieldStrings("<div class=test1>hello[move;att=class]</div>", array('move'=>'test2 test3'), "<div class=\"test2 test3\">hello</div>", "test bug #5");

		// encaps tags bug: TBS merge close the closing SPAN tag instead of merge the parent DOM node
		$this->assertEqualMergeFieldStrings("<div class=\"test1\"><span class=\"hello\">hello</span>[move;att=class] <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", array('move'=>'test2'), "<div class=\"test2\"><span class=\"hello\">hello</span> <span class=\"mr\">Mr.</span> <span class=\"patatoe\">Patatoe</span></div>", "test bug #6");
	
		// quote bug: merging attribute could have value with quote, then tbs must display an error
		$this->assertEqualMergeFieldStrings("<a href='#'>hello[move;att=href]</a>", array('move'=>'javascript:alert(\'test\')'), "TinyButStrong Error: can't mixed quote in attribute value", "test bug #7");
		$this->assertEqualMergeFieldStrings("<a href=\"#\">hello[move;att=href]</a>", array('move'=>'javascript:alert("test")'), "TinyButStrong Error: can't mixed quote in attribute value", "test bug #8");
		$this->assertEqualMergeFieldStrings("<a>hello[move;att=href]</a>", array('move'=>'javascript:alert(\'test\')'), "<a href=\"javascript:alert('test')\">hello</a>", "test bug #9");

		// close tag bug: TBS must merge attribute in autoclose tags
		$this->assertEqualMergeFieldStrings("<hr[move;att=width] />", array('move'=>'50%'), "<hr width=\"50%\" />", "test bug #10");
		$this->assertEqualMergeFieldStrings("<[move;att=width]hr />", array('move'=>'50%'), "<hr width=\"50%\" />", "test bug #11");
		$this->assertEqualMergeFieldStrings("<h[move;att=width]r />", array('move'=>'50%'), "<hr width=\"50%\" />", "test bug #12"); // vicious
		$this->assertEqualMergeFieldStrings("<hr [move;att=width]/>", array('move'=>'50%'), "<hr width=\"50%\" />", "test bug #13");
	}
}

?>