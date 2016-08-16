<?php
/*/
Phoenix2
Version 0.7 alpha, Build 12
===
Project Lead: Prof. Martin-Dietrich Glessgen, University of Zurich
Code by: Samuel Laeubli, University of Zurich
Contact: samuel.laeubli@uzh.ch
===
Module Name: Variants
Module Signature: com.ph2.modules.ann.gra.grp
Description:
Define grapheme variants (groups) and assign occurrences.
---
/*/
//! MODULE BODY

?>
<script type="text/javascript">
	$(document).ready( function() {
		var matchingOccurrences = PH2Component.OccContextBox('occbox1');
		var variantDetailsWindow = PH2Component.DetailsWindow('detailswindow1', 'ann_gra_grp_variants_load', 'ann_gra_grp_variants_save', false);
		var variantsGroupSelector = PH2Component.GroupSelectorGraphvariants('groupselector1', matchingOccurrences, variantDetailsWindow);
		
		// handle action buttons for occbox1
		$('#occ_action').submit( function(e) {
			e.preventDefault();
			// switch action
			var action = $('#select_action').val();
			if (action == 'remove_occurrences') {
				var action_url = 'actions/php/ajax.php?action=removeOccurrencesFromGraph&graphID=' + variantsGroupSelector.getActiveGraphID() + '&occurrenceIDs=' + matchingOccurrences.getSelected();
				// remove items from occurrence box
				matchingOccurrences.removeSelected();
				// refresh counts for variants
				variantsGroupSelector.reload();
				// remove assignment on the database
				$.get( action_url );
				// confirm to user
				pushNotification(1, 'The selected Occurrences have been removed from this Grapheme.');
			}
		});		
		
	});
</script>

<div id="mod_top">
    <?php include PH2_WP_INC . '/modules/menus/ann/gra.modulemenu.php'; ?>
</div>
<div id="mod_status"><?php htmlModuleStatusBarMessages($ps); ?></div>
<div id="mod_body">

	<!-- Occurrence Context Box -->
    <div class="w100">
        <div class="modulebox OccContextBox" id="occbox1">
            <div class="title">Assigned Occurrences</div>
            
            <div class="title_extension">
            	<form id="occ_action" action="" method="post">
                    <select id="select_action" name="select_action">
                        <option value="remove_occurrences">Remove Selected</option>
                        <!--<option value="2">Reassign Selected</option>-->
                    </select>
                    <input type="submit" class="button" value="OK" />
                </form>
            </div>

            <div class="body">
            	<!-- tabs -->
                <!-- end tabs -->
                
                <div id="occ_progress" class="hidden">loading <span id="current"></span>/<span id="total"></span></div>
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
            
            	<div id="occ_matches_meta" class="h200">
                	<table>
                    	<!-- occ meta lines -->
                    </table>
                </div>
                
            	<div id="occ_matches" class="scrollbox h200">
                	<!-- occ context lines -->
              	</div>
                
            </div>
        </div>
    </div>
                
    <div class="w33">
        <div class="modulebox GroupSelector Graphvariants" id="groupselector1">
            <div class="title">Variants</div>
            <div class="title_extension">
            	<a href="#" class="tablink" rel="tab1" id="add_variant_tab_button" title="Add variant">Add</a>
                <a href="#" class="tablink" rel="tab2" id="delete_variant_tab_button" title="Delete selected variant">Delete</a>
            </div>
            <div class="body">
            
            <!-- tabs -->
            	<div id="tab1" class="tab hidden">
                	<form id="add_variant_form">
                        <fieldset>
                            <label class="inline" for="f1">Bezeichnung:</label>
                            <input type="text" class="text small required" name="new_Name" value="" />
                            <label class="inline" for="f2">Nummer:</label>
                            <input type="text" class="text tiny required digits_and_points_only" name="new_Number" value="" />
                            <br />
                            <input type="button" id="submit_button" class="button" value="Add" />
                            <input type="button" id="cancel_button" class="button" value="Cancel" />
                        </fieldset>
                    </form>
                </div>
                <div id="tab2" class="tab hidden">
                	<p>By confirming this operation, the variant <span id="active_variant_name" class="bold"></span> will be deleted. This operation is not reversible. Note that all occurrences that are currently assigned to this variant will be removed from the Grapheme. To avoid this, please re-assign the occurrences to another variant beforehand.</p>
                	<form id="delete_variant_form">
                        <br />
                        <fieldset>
                            <input type="button" id="delete_button" class="button" value="Delete <name>" />
                            <input type="button" id="cancel_delete_button" class="button" value="Cancel" />
                        </fieldset>
                    </form>
                </div>
            <!-- end tabs -->
            
                <table class="selectable" id="groups">
                    <thead>
                        <tr>
                            <td></td>
                            <td><a class="tooltipp" title="Numerical variant identifier" href="#">Nr.</a></td>
                            <td class="variant_name">Variant</td>
                            <td>Occ.</td>
                            <td>Texts</td>
                            <td>Corp.</td>
                        </tr>
                    </thead>
                    <tbody />
                </table>
            </div>
        </div>
    </div>
    
    <div class="w66">
        <div class="modulebox" id="detailswindow1">
            <div class="title">Variant Details</div>

            <div class="title_extension">
                <a class="save_button" href="#" title="Save changes to variant">Save</a>
                <a class="restore_button" href="#" title="Discard changes and restore original values">Restore</a>
            </div>
            <div class="body">
                <form class="mainform" action="" method="post">
                    <fieldset>
                        <label for="f1">Bezeichnung</label>
                        <input type="text" class="text small required" name="Name" value="" />
                        <label class="inline" for="f2">Nummer:</label>
                        <input type="text" class="text tiny required digits_and_points_only" name="Number" value="" />
                    </fieldset>
                    <!-- <fieldset>
                    <legend>Weitere Felder</legend>
                    	<p>In den Migrationsdaten sind keine weiteren Informationen zu den Graphemvarianten (-gruppen) enthalten (i.e. die Tabellen für Filiation und Diasystematik sind für alle Einträge leer). Welche Angaben sollten an dieser Stelle noch ermöglicht werden?</p>
                    </fieldset> -->
                </form>
            </div>
        </div>

    </div>
    
</div>

</div>