<?php
/*/
Phoenix2
Version 0.2 Alpha, Build 139
===
Project Lead: Prof. Martin-Dietrich Glessgen, University of Zurich
Code by: Samuel Laeubli, University of Zurich
Contact: samuel.laeubli@uzh.ch
===
Module Name: Search
Module Signature: com.ph2.modules.ann.fnd
Description:
Search texts using various strategies/methods and select a set of search
results (= selected occurrences).
---
/*/
//! MODULE BODY

// if a search was performed, get the matching OccurrenceIDs from $_POST['matching_occurrences']
if($_POST['matching_occurrences']) {
	
	// prepare results for html view in two different arrays/tables:
	// 1) for #occ_matches_meta
	// 2) for #occ_matches
	
	$occ_matches_meta = array();
	$occ_matches = array();
	
	foreach ($_POST['matching_occurrences'] as $occ_id) {
		$occ = new Occurrence( (int) $occ_id );
		// write meta section
		$occ_matches_meta[] = array($occ->getAssignedCorpusID(), $occ->getTextID(), $occ->getTextsectionNames(), $occ->getDiv(), $occ_id);
		// write match section (context line)
		$context = $occ->getContext();
		$left_context = $context[0];
		$right_context = $context[1];
		// adjust lines for even display
		$left_width = 220;
		$right_width = 225;
		for ($i=0; $i <= $left_width; $i++) {
			$left_context = ' ' . $left_context;
		}
		for ($i=0; $i <= $right_width; $i++) {
			$right_context .= ' ';
		}
		$left_context = mb_substr($left_context, -$left_width, $left_width, 'UTF-8');
		$right_context = mb_substr($right_context, 0, $right_width, 'UTF-8');
		$occ_matches[] = array($left_context, $occ->getSurface(), $right_context, $occ_id);
	}
}

?>
<script type="text/javascript">

// toggle selected/not selected for a matching occurrence line
function toggleOccSelection (id) {
	// @param check (bool): whether to set the corresponding checkbox to 'checked'
	// select span
	var span = $("span.match#" + id);
	var checkbox = $("#checkbox-" + id);
	if (span.hasClass('selected')) {
		if (checkbox.attr('checked')) checkbox.attr('checked', false);
		span.removeClass('selected');
	} else {
		if (!checkbox.attr('checked')) checkbox.attr('checked', true);
		span.addClass('selected');
	}
}

function searchTypes (query) {
	if (query == '') {
		$('#typeSearchResult').html('');
	} else {
		// load results
		$.get('actions/php/ajax.php?action=searchTypes&q=' + query, function(data) {
			if(data.substr(0,3) == '<ul') {
				// only display valid resultset (lists)
				$('#typeSearchResult').html(data);
			}
			//successHandler goes here
		});
	}
}

$(document).ready( function() {
	
	// center horizontal scrollbar on load
	$("#occ_matches").scrollTo('50%', 0);
	$("#occ_matches").scrollTop(0);
	
	// bind scroll-behaviour of #occ_matches_meta to #occ_matches
	$("#occ_matches").bind( "scroll", function () {
		$("#occ_matches_meta").scrollTop($("#occ_matches").scrollTop());
		$("#monitor").html($("#occ_matches_meta").scrollTop())
	});
	
	// bind checkbox and matching occurrence span on click event
	$("input.occ_selection").bind( "click", function () {
		var span = $("span.match#" + $(this).attr('id').trim('checkbox-'));
		if (span.hasClass('selected')) {
			span.removeClass('selected');
		} else {
			span.addClass('selected');
		}
	});
	
	$("span.match").bind( "click", function () {
		toggleOccSelection($(this).attr('id'));
	});
	
	// bind type search box to keypress events
	$("#typeSearchBox").bind( "keyup", function () {
		delay( function() {
			searchTypes($("#typeSearchBox").val());
		}, 500 );
	});
	
	// prevent typeSearchBox from form submission
	$("#typeSearchForm").submit( function(e) {
		e.preventDefault();
	});
	
});
</script>
<div id="mod_top">
    <?php include PH2_WP_INC . '/modules/menus/ann/fnd.modulemenu.php'; ?>
</div>
<div id="mod_status"><?php htmlModuleStatusBarMessages($ps); ?></div>
<div id="mod_body">
    <div class="w80">
        <div class="modulebox occ-matches">
            <div class="title">Matching Occurrences</div>
            <div class="title_extension">
            	<a href="<?php modal('assign_lemma_morph'); ?>" rel="facebox" id="assign_button-lemma_morph" title="Assign a lemma to the selected occurrences. [CTRL] [ALT] [L]">+Lemma</a>
                <a href="#" id="assign_button" title="Assign a graph to the selected occurrences. [CTRL] [ALT] [G]">+Graph</a>
                <a href="<?php modal('search_occurrences'); ?>" rel="facebox" id="search_button" title="Refine or formulate a new search. [CTRL] [ALT] [F]">Search</a>
            </div>
            <div class="body">
            
            <table>
                <thead>
                  <tr>
                    <td><input type="checkbox" class="select_all" rel="occ_selection" name=""/></td>
                    <th><a href="#" class="tooltipp" title="Corpus ID. Hover to display the name of the corpus.">Crp</a></th>
                    <th><a href="#" class="tooltipp" title="Text ID. Hover to display the name of the text.">Txt</a></th>
                    <th class="wider"><a href="#" class="tooltipp" title="Text Section. Hover to display the corresponding description.">Sct</a></th>
                    <th><a href="#" class="tooltipp" title="Involved text division.">Div</a></th>
                    <th class="padded">Context</th>
                  </tr>
                </thead>
                <tbody></tbody>
            </table>
            
            	<div id="occ_matches_meta" class="h250">
                	<table>
                    	<?php if (!empty($occ_matches_meta)) foreach($occ_matches_meta as $meta) {
							$textsections_html = '';
							foreach ($meta[2] as $ts) {
								$textsections_html .= '<a href="#" title="'.$ts[1].'">'.$ts[0].'</a> ';
							}
							echo('<tr>
                        	<td><input type="checkbox" class="occ_selection" name="selected_occ[]" id="checkbox-'.$meta[4].'" /></td>
                            <td>'.$meta[0].'</td>
                            <td>'.$meta[1].'</td>
                            <td class="wider">'.$textsections_html.'</td>
                            <td>'.$meta[3].'</td>
                        </tr>');
						} ?>
                    </table>
                </div>
                
            	<div id="occ_matches" class="scrollbox h250">
                	<?php if (!empty($occ_matches)) foreach ($occ_matches as $match) {
						echo('<pre class="occ_line">'.$match[0].'<span id="'.$match[3].'" class="match">'.$match[1].'</span> '.$match[2].'</pre>'."\n");
					} ?>
                </div>
                
            </div>
        </div>
    </div>
    
    <div class="w20">
    	<div class="modulebox">
        	<div class="title">Browse Types</div>
            <div class="body">
            	<form id="typeSearchForm">
            	<p>Type your search below:</p>
                <input type="text" name="typeSearchBox" id="typeSearchBox" class="text w80" />
                </form>
                <br />
            	<div class="h200 scrollbox" id="typeSearchResult">
                	
                </div>
            </div>
        </div>
    </div>
    
    <!-- Occurrence hover details / bigger text snippet / ... could go here -->
    
</div>