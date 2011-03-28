<?php

/* This file is part of Scrabble-Words.
 * Copyright (C) 2011 by James Nylen.
 *
 * Scrabble-Words is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Scrabble-Words is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Scrabble-Words.  If not, see <http://www.gnu.org/licenses/>.
 */

require 'JSON.php';
require 'debuglib.php';
require 'private/config.php';

header('Content-type: text/html; charset=UTF-8');

$db = mysql_connect($db_host, $db_username, $db_password);
if(!$db) die('Could not connect to MySQL: ' . mysql_error());
mysql_select_db($db_database);

$html_version = 2;
$json_svc = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);

function definition_json_to_html($json) {
  global $json_svc;
  $data = $json_svc->decode($json);

  $defs = array();
  $root = $data['primaries'];
  if(!$root) $root = $data['webDefinitions'];
  if($root) {
    foreach($root as $item) {
      $pos = '';
      foreach($item['terms'] as $term) {
        if($term['labels'][0]['title'] == 'Part-of-speech') {
          $pos = $term['labels'][0]['text'];
        }
      }
      foreach($item['entries'] as $entry) {
        if($entry['type'] == 'meaning') {
          array_push($defs, array(
            'pos' => $pos,
            'def' => urldecode(str_replace('\x', '%', $entry['terms'][0]['text']))
          ));
        }
      }
    }
  } else {
    return '<span class="no-definitions">No definitions for this word</span>';
  }

  $html = "<ol>\n";
  foreach($defs as $def) {
    $html .= "<li>";
    if($def['pos']) {
      $html .= "<span class=\"part-of-speech\">$def[pos]</span> - ";
    }
    $html .= "<span class=\"definition\">$def[def]</span></li>\n";
  }
  $html .= "</ol>\n";
  return $html;
}


if($_GET['words']) {
  set_time_limit(0);

  $result = mysql_query("SELECT word FROM $db_table");
  if(!$result) die('Error querying DB: ' . mysql_error());
  $words_db = array();
  while($row = mysql_fetch_row($result)) {
    $words_db[$row[0]] = true;
  }

  $words_new = array_unique(explode(' ',
    preg_replace('@[^ a-z]@', '', strtolower($_GET['words']))));

  foreach($words_new as $word) {
    if(strlen($word) > 1 && !$words_db[$word]) {
      // http://googlesystem.blogspot.com/2009/12/on-googles-unofficial-dictionary-api.html
      $json = file_get_contents(
        "http://www.google.com/dictionary/json?"
         . "callback=dictCallback&q=$word&sl=en&tl=en"
         . "&restrict=pr%2Cde&client=te");
      $json = preg_replace('@^dictCallback\(@', '',
        preg_replace('@,200,null\)$@', '', $json));
      $html = mysql_real_escape_string(definition_json_to_html($json));
      $json = mysql_real_escape_string($json);
      $user_name = mysql_real_escape_string($user_name);

      $result = mysql_query(<<<SQL
        INSERT INTO $db_table (word, date_added, added_by, json, html_version, html)
        VALUES ('$word', NOW(), '$user_name', '$json', $html_version, '$html')
SQL
      );
      if(!$result) die('Error querying DB: ' . mysql_error());
    }
  }

  header('Location: .');
}

$result = mysql_query(<<<SQL
  SELECT word, UNIX_TIMESTAMP(date_added) date_added, added_by, json, html_version, html
  FROM $db_table
  ORDER BY date_added DESC
SQL
);
if(!$result) die('Error querying DB: ' . mysql_error());
$words = array();
while($row = mysql_fetch_assoc($result)) {
  if($row['html_version'] < $html_version) {
    $row['html'] = definition_json_to_html($row['json']);
    $html = mysql_real_escape_string($row['html']);

    $result2 = mysql_query(<<<SQL
      UPDATE $db_table
      SET html_version = $html_version,
        html = '$html'
      WHERE word = '$row[word]'
SQL
    );
    if(!$result2) die('Error querying DB: ' . mysql_error());
  }
  array_push($words, $row);
}

?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script type="text/javascript" src="tablesort.js"></script>
    <script type="text/javascript" src="jquery.min.js"></script>
    <script type="text/javascript" src="jquery.scrollTo-1.4.2-min.js"></script>
    <script type="text/javascript" src="script.js"></script>
    <title><?php echo $site_title ?></title>
  </head>
  <body>
    <table id="words" class="sortable-onload-show-1r rowstyle-odd">
      <tr class="header">
        <th class="sortable-text word">Word</th>
        <th class="sortable-sortCustomDate favour-reverse date-added">Date added</th>
        <th class="sortable-text added-by">Added by</th>
        <th class="definition">
          <form id="add-words" method="get" action=".">
            Definition
            <input id="jump-to-word" type="text" />
            <button type="button" id="jump-to-top">Jump to top</button>
            <input id="add-words-text" type="text" name="words" value="" />
            <input id="add-words-submit" type="submit" value="Add word(s)" />
          </form>
        </th>
      </tr>
<?php
foreach($words as $row) {
  $date_added = str_replace(' ', '<br />', date('n/j/Y g:ia', $row['date_added']));
  echo <<<HTML
      <tr>
        <td class="word" id="word-$row[word]">$row[word]</td>
        <td class="date-added" data-timestamp="$row[date_added]">$date_added</td>
        <td class="added-by">$row[added_by]</td>
        <td class="definition">$row[html]</td>
      </tr>

HTML;
}
?>
    </table>
  </body>
</html>
