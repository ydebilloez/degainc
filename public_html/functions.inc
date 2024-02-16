<?php

/* functions.inc
 * Generic functions for application
 */

function PrintAssociateLine($report) {
    if (!$report) return;
    echo "<br />\n";
    echo "<table class='pme-main'><tr class='pme-header'>\n";
    foreach($report as $key => $cell) echo "<th class='pme-header'>$key</th>";
    echo '</tr><tr>' . "\n";
    foreach($report as $key => $cell) echo "<td class='pme-value'>$cell</td>";
    echo '</tr></table>' . "\n";
}

function PrintAssociateTable($inst, $sql) {
    $rows = $inst->FetchDB($sql, 'a');
    if (!$rows) return;
    echo "<br />\n";
    echo "<table class='pme-main'><tr class='pme-header'>\n";
    foreach($rows[0] as $key => $cell) echo "<th class='pme-header'>$key</th>";
    echo '</tr>' . "\n";
    foreach ($rows as $row) {
        echo "<tr class='pme-row'>";
        foreach($row as $key => $cell) echo "<td class='pme-value'>$cell</td>";
        echo "</tr>\n";
    }
    echo '</table>' . "\n";
    return $rows;
}
