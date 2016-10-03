<?php
/**
* Created by PhpStorm.
* User: Lolo Irie
* Date: 09/04/2016
* Time: 15:53
*/

if ( !defined( 'ABSPATH' ) ) die();


/* Class END */

    /* List of existing + Button Add */
    if( empty( $_POST ) ){

      /* List of existing elements */
      // Call the WP class file
      if ( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
      }


        die();
    }else{

      $html_last_update = '<div class="last_update">Aktualisiert am '.date( 'l j.n.Y' ).'</div>';
      if( $_POST[ 'bvgco_fromsite' ] != 1 ){
        require_once( 'nuliga_config.php' );
      }
      $form = file_get_contents( plugin_dir_path(__FILE__).'display_results.html' );

      $url = '';
      $team_id = -1;
      if( isset( $_POST[ 'team_id' ] ) ){
        $team_id = $_POST[ 'team_id' ];
      }

      if( $_POST[ 'type_data' ] == 1 ){

        $html_start = '';
        $html_end = '';
        $heads_toRemove = array();
        $cols_toRemove = array();

        switch( $_POST[ 'bvgco_action' ] ){
          // All games
          case '1':
            if( $_POST[ 'bvgco_fromsite' ] == 1 ){
              // Turnier.de
              $url = 'http://www.turnier.de/sport/clubmatches.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&cid=163';
              $html_start = '<table class="ruler">';
              $html_end = '<div class="leaderboard banner">';
              $heads_toRemove = array( 13, 12, 10 );
              $cols_toRemove = array( 14, 12, 10 );
            }else{
              //nuLiga
              $url = 'http://www.turnier.de/sport/clubmatches.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&cid=163';
              $html_start = '<table class="ruler">';
              $html_end = '<div class="leaderboard banner">';
              $heads_toRemove = array( 13, 12, 10 );
              $cols_toRemove = array( 14, 12, 10 );
            }

            break;

          // Games and table for one team
          case '2':
            if( $_POST[ 'bvgco_fromsite' ] == 1 ){
              // Turnier.de
              $url = 'http://www.turnier.de/sport/teamstandings.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&tid='.$team_id;
              $html_start = '<h3>Tabellen</h3>';
              $html_end = '<div class="leaderboard banner">';

              $url2 = 'http://www.turnier.de/sport/teammatches.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&tid='.$team_id;
              $html_start2 = '<div class="teammatch-table">';
              $html_end2 = '<div class="leaderboard banner">';
              $heads_toRemove = array( 13, 12, 11, 10, 2 );
              $cols_toRemove = array( 14, 13, 12, 11, 10, 2 );
            }else{
              //nuLiga

              $url = 'https://hbv-badminton.liga.nu/cgi-bin/WebObjects/nuLigaBADDE.woa/wa/groupPage?championship='.$NULIGA_TEAM_ID[ $team_id ];
              $html_start = '<h2>Tabelle</h2>';
              $html_end = '<h2>Spielplan (Aktuell)</h2>';

              //$url2 = 'https://hbv-badminton.liga.nu/cgi-bin/WebObjects/nuLigaBADDE.woa/wa/groupPage?displayTyp=gesamt&displayDetail=meetings&championship='.$NULIGA_TEAM_ID[ $team_id ];
              $url2 = 'https://hbv-badminton.liga.nu/cgi-bin/WebObjects/nuLigaBADDE.woa/wa/teamPortrait?teamtable='.$NULIGA_TEAMTABLE_ID[ $team_id ].'&pageState=vorrunde&championship='.$NULIGA_TEAM_ID[ $team_id ];
              $html_start2 = '<h2>Spieltermine&nbsp;(Vorrunde)';
              $html_end2 = '</table>';
              $heads_toRemove = array( 1 );
              $cols_toRemove = array( 3 );
            }



            break;

          // All tables
          case '3':
            if( $_POST[ 'bvgco_fromsite' ] == 1 ){
              // Turnier.de
              $url = 'http://www.turnier.de/sport/clubstandings.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&cid=163';
              $html_start = '<h3>Tabellen</h3>';
              $html_end = '<div class="leaderboard banner">';
            }else{
              //nuLiga
              $url = 'http://www.turnier.de/sport/clubstandings.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&cid=163';
              $html_start = '<h3>Tabellen</h3>';
              $html_end = '<div class="leaderboard banner">';
            }

            break;

          // Table for one team
          case '4':
            if( $_POST[ 'bvgco_fromsite' ] == 1 ){
              // Turnier.de
              $url = 'http://www.turnier.de/sport/teamstandings.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&tid='.$team_id;
              $html_start = '<h3>Tabellen</h3>';
              $html_end = '<div class="leaderboard banner">';
            }else{
              //nuLiga
              $url = 'http://www.turnier.de/sport/teamstandings.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&tid='.$team_id;
              $html_start = '<h3>Tabellen</h3>';
              $html_end = '<div class="leaderboard banner">';
            }

            break;

          default:

            break;

        }

        if( !empty( $html1_test ) ){
          $html_brut = $html1_test;
        }else{
          $html_brut = file_get_contents( $url );
        }


        if( isset( $url2 ) ){
          if( !empty( $html2_test ) ){
            $html_brut2 = $html2_test;
          }else{
            $html_brut2 = file_get_contents( $url2 );
          }

        }


        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        $html_brut = $data;
        */

        if( strpos( $html_brut , $html_start ) === false || strpos( $html_brut , $html_end ) === false ){

          $form = str_replace( '<div id="results"></div>', '<div id="results" class="view">Kritischer Fehler: Inhalt fehlt... </div>', $form );

        }else{

          $html_parts = explode( $html_start , $html_brut );
          $html_brut = $html_start . $html_parts[1];

          $html_parts = explode( $html_end , $html_brut );
          $html_final = $html_parts[0];

          $html_parts2 = explode( $html_start2 , $html_brut2 );
          if( $_POST[ 'bvgco_fromsite' ] == 1 ) {
            $html_brut2 = $html_start2 . $html_parts2[1];
          }else{
            $html_brut2 = '<h2>Spieltermine'.$html_parts2[1];
          }
          //var_dump($html_parts2);
          //echo '<code>'.$html_parts2[0].'</code>';
          $html_parts2 = explode( $html_end2 , $html_brut2 );
          $html_final2 = $html_parts2[0];
          if( $_POST[ 'bvgco_fromsite' ] == 2 ) {
            $html_final2 = $html_final2 . '</table>';
          }
          //var_dump($html_parts2); die();


          $html_wp_final = '';

          if( !empty( $heads_toRemove ) || !empty( $cols_toRemove ) ){
            $html_final2 = remove_columns( $html_final2, $heads_toRemove, $cols_toRemove );
          }



          $html_final = str_replace( '<p class="prodemotemessage">Die grau unterlegten Teams über bzw. unter den Strichen belegen Auf- oder Abstiegsplätze</p>' , '', $html_final );

          // Create Wordpress template$url = 'http://www.turnier.de/sport/teamstandings.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&tid='.$team_id;

          //if( isset( $_POST[ 'webpage_full_action' ] ) && $_POST[ 'webpage_full_action' ] == 1 ){

          $pattern = '/\<caption\>(.*)\<\/caption\>/s';
          preg_match( $pattern, $html_final, $matches );

          /*
          var_dump( $html_final );
          var_dump( $pattern );
          var_dump( $matches );
          */

          $html_final = str_replace( '<h3>Tabellen</h3>' , '<h1>Tabelle</h1><h1>'.trim( $matches[1] ).'</h1>' , $html_final);

          $html_final = preg_replace( $pattern, '', $html_final );

          if( $_POST[ 'bvgco_fromsite' ] == 1 ){
            // Turnier.de
            $url_team_players = 'http://www.turnier.de/sport/teamrankingplayers.aspx?id=202E29D6-0A3A-4769-9CCC-3F540EB6D56F&tid='.$team_id;
            $html_start = '<h3>Spieler</h3>';
            $html_end = '<div class="leaderboard banner">';

            if( !empty( $html3_test ) ){
              $html_brut = $html3_test;
            }else{
              $html_brut = file_get_contents( $url_team_players );
            }

            $html_parts = explode( $html_start , $html_brut );
            $html_brut = $html_start . $html_parts[1];


            $html_parts = explode( $html_end , $html_brut );
            $html_brut = $html_parts[0];

            $html_brut = str_replace( 'id="playercell"' , '', $html_brut );


            // BLOC TEAM / PLAYERS
            $html_wp_final = '<div id="mannschaft_spieler" class="bvg_block">
        <h2>Mannschaftsspieler</h2>
        <h3>Herren</h3>
        <ul>';


            $html_wp_final .= "\n".get_team_players( $html_brut, 'Herren' );


            $html_wp_final .= "</ul>\n";
            $html_wp_final .= '<h3>Damen</h3>
        <ul>';

            $html_wp_final .= "\n".get_team_players( $html_brut, 'Damen' );

            $html_wp_final .= "</ul>\n";

            $html_wp_final .= '</div>';
          }else{
            //nuLiga

          }


          // BLOC TABELLE
          $html_wp_final .= "\n\n";
          $html_wp_final .= '<div id="mannschaft_tabelle" class="bvg_block">';

          $html_wp_final .= "\n";
          $html_wp_final .= $html_final;

          $html_wp_final .= "\n";
          $html_wp_final .= $html_last_update.'</div>';

          if( isset( $url2 ) ){

            /*
            var_dump( $html_final );
            var_dump( $pattern );
            var_dump( $matches );
            */

            $html_final2 = str_replace( '<div class="teammatch-table">', '<h1>Spiele</h1>', $html_final2 );
            $html_final2 = preg_replace( $pattern, '', $html_final2 );


            // BLOC SPIELE
            $html_wp_final .= "\n\n";
            $html_wp_final .= '<div id="mannschaft_spiele" class="bvg_block">';

            $html_wp_final .= "\n";
            $html_wp_final .= $html_final2;

            $html_wp_final .= "\n\n";
            $html_wp_final .= '</div>';

          }

          $html_final = $html_wp_final;
          //var_dump($html_final); die();
          /*
          }else if( isset( $url2 ) ){
              $html_final .= '<br />'.$html_final2;
          }
          */
            if( $_POST[ 'bvgco_fromsite' ] == 1 ){
                $html_final = str_replace( 'href="' , 'target="_blank" href="http://turnier.de/', $html_final );
                $html_final = str_replace( 'turnier.de//' , 'turnier.de/', $html_final );
                $html_final = str_replace( '/./' , '/', $html_final );
            }else{
                $html_final = str_replace( 'href="' , 'target="_blank" href="https://hbv-badminton.liga.nu/', $html_final );
                $html_final = str_replace( 'hbv-badminton.liga.nu//' , 'turnier.de/', $html_final );
            }


          // Display content
          $form = str_replace( '<div id="results"></div>', '<div id="results" class="view">'.$html_final.'</div>', $form );


          // Display HTML Code
          $html_view = htmlspecialchars( $html_final );

          $form = str_replace( '<div id="results_html"></div>', '<div id="results_html" class="view copy"><pre>'.$html_view.'</pre></div>', $form );

        }


        echo $form;

        die();
      }
      else if( $_POST[ 'type_data' ] == 2 ){
        // Get default players (Stammspieler)

        $url = 'https://hbv-badminton.liga.nu/cgi-bin/WebObjects/nuLigaBADDE.woa/wa/clubPools?displayTyp=vorrunde&club=15434&contestType='.$NULIGA_TEAMPLAYER_CONTESTTYPES[ $team_id ][0].'&seasonName=2016%2F17';
        $html_start = 'Vereinsrangliste</h1>';
        $html_end = '<div id="content-col1">';

        $html_brut = file_get_contents( $url );

        if( strpos( $html_brut , $html_start ) === false || strpos( $html_brut , $html_end ) === false ){

          $form = str_replace( '<div id="results"></div>', '<div id="results" class="view">Kritischer Fehler: Inhalt fehlt... </div>', $form );

        }else{

            $html_parts = explode( $html_start , $html_brut );
            $html_brut = $html_start . $html_parts[1];

            $html_parts = explode( $html_end , $html_brut );
            $html_final = '<div><h1>'.trim( $html_parts[0] );

            $players_heads_toRemove = array( 6, 5, 4, 2, 0 );
            $players_cols_toRemove = array( 6, 5, 4, 2, 0 );
            if( !empty( $players_cols_toRemove ) ){
                $html_final = remove_columns( $html_final, $players_heads_toRemove, $players_cols_toRemove );
            }


            $html_wp_final = '';

            $html_wp_final .= "\n".get_team_players_from_nuliga( $html_final, $NULIGA_TEAMPLAYER_ID[ $team_id ] );
            $html_wp_final = str_replace( '<ul>' , '<ul><li class="li_head" style="font-weight: bold;">Stammspieler</li>', $html_wp_final );
            $html_final = $html_wp_final;

            if( isset( $NULIGA_TEAMPLAYER_CONTESTTYPES[ $team_id ][1] ) ){

                $url = 'https://hbv-badminton.liga.nu/cgi-bin/WebObjects/nuLigaBADDE.woa/wa/clubPools?displayTyp=vorrunde&club=15434&contestType='.$NULIGA_TEAMPLAYER_CONTESTTYPES[ $team_id ][1].'&seasonName=2016%2F17';
                $html_start = 'Vereinsrangliste</h1>';
                $html_end = '<div id="content-col1">';

                $html_brut = file_get_contents( $url );

                if( strpos( $html_brut , $html_start ) === false || strpos( $html_brut , $html_end ) === false ){

                    $form = str_replace( '<div id="results"></div>', '<div id="results" class="view">Kritischer Fehler: Inhalt fehlt... </div>', $form );

                }else {

                    $html_parts = explode($html_start, $html_brut);
                    $html_brut = $html_start . $html_parts[1];

                    $html_parts = explode($html_end, $html_brut);
                    $html_final2 = '<div><h1>' . trim($html_parts[0]);

                    $players_heads_toRemove = array(6, 5, 4, 2, 0);
                    $players_cols_toRemove = array(6, 5, 4, 2, 0);
                    if (!empty($players_cols_toRemove)) {
                        $html_final2 = remove_columns($html_final2, $players_heads_toRemove, $players_cols_toRemove);
                    }

                    $html_wp_final2 = '';

                    $html_wp_final2 .= "\n" . get_team_players_from_nuliga($html_final2, $NULIGA_TEAMPLAYER_ID[ $team_id ]);
                    $html_wp_final2 = str_replace( '<ul>' , '<ul><li></li>', $html_wp_final2 );

                    $html_final .= $html_wp_final2;
                }

            }







            // Display content
            $form = str_replace( '<div id="results"></div>', '<div id="results" class="view">'.$html_final.'</div>', $form );


            // Display HTML Code
            $html_view = htmlspecialchars( $html_final );

            $form = str_replace( '<div id="results_html"></div>', '<div id="results_html" class="view copy"><pre>'.$html_view.'</pre></div>', $form );

            echo $form;

            die();
        }


        die();


      }


    }
    

function get_team_players_from_nuliga( $html='', $teamID='I' ){

    $html_players_list = '<ul>';

    // Only used for debug
    /*
    echo '<hr />';
    echo $html;
    echo '<hr />';
    */

    $html_return = 'Spieler !!!';

    $html = str_replace( '&', '&amp;', $html );
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
    // a new dom object
    $dom = new domDocument;

    $dom->loadHTML( $html );

    // discard white space
    $dom->preserveWhiteSpace = false;

    // Get all rows
    $trList = $dom->getElementsByTagName('tr');

    $tr_head = 1;
    foreach ($trList as $tr) {
        //echo 'X<br />';
        if( $tr_head === 1 ){ $tr_head++; continue; }

        $tdList = $tr->getElementsByTagName('td');
        // Content rows
        //echo $teamID.'x'.$tdList->item( 0 )->nodeValue.'<br />';
        if( $tdList->item( 0 )->nodeValue == $teamID ){
            $html_players_list .= '<li>'.$tdList->item( 1 )->nodeValue.'</li>';
        }
    }

    return $html_players_list.'</ul>';
}

    
function get_team_players( $html='', $gender ){
    $html_return = '';

    $html = str_replace( '&', '&amp;', $html );
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
    // a new dom object
    $dom = new domDocument;

    // load the html into the object
    /*
    echo '<pre>';
    var_dump( $html );
    echo '</pre>';
    */
    $dom->loadHTML( $html );

    // discard white space
    $dom->preserveWhiteSpace = false;

    // Get all tables
    //$tableList = $dom->getElementsByTagName('table');

    $table_no = 1;

    $finder = new DomXPath($dom);
    $classname="ruler";
    $tableList = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");

    $already = array();

    foreach ($tableList as $table) {
        // Herren
        if( $gender == 'Herren' && $table_no%2 == 1 ){
            $aList = $table->getElementsByTagName('a');
            foreach($aList as $aDom){
                $aHtml = $aDom->ownerDocument->saveHTML( $aDom );
                if( !in_array( $aHtml, $already ) && strpos( $aHtml, 'xxx' ) === false ){
                    $already[] = $aHtml;
                    if( $table_no == 1 ){
                        $html_return .= '<li class="bvg_stamm">'.$aHtml."</li>\n";
                    }else{
                        $html_return .= '<li class="bvg_ersatz">'.$aHtml."</li>\n";
                    }
                }

            }
        }else if( $gender == 'Damen' && $table_no%2 == 0 ){
            $aList = $table->getElementsByTagName('a');
            foreach($aList as $aDom){
                $aHtml = $aDom->ownerDocument->saveHTML( $aDom );
                if( !in_array( $aHtml, $already ) && strpos( $aHtml, 'xxx' ) === false ){
                    $already[] = $aHtml;
                    if( $table_no == 2 ){
                        $html_return .= '<li class="bvg_stamm">'.$aHtml."</li>\n";
                    }else{
                        $html_return .= '<li class="bvg_ersatz">'.$aHtml."</li>\n";
                    }
                }
            }
        }

        $table_no++;
    }



    return $html_return;
}



function remove_columns( $html='', $heads_toRemove = array(), $cols_toRemove = array() ){

    $html_return = '';

    $html = str_replace( '&', '&amp;', $html );
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");

    // a new dom object
    $dom = new domDocument;

    // load the html into the object
    $dom->loadHTML( $html );

    // discard white space
    $dom->preserveWhiteSpace = false;

    // Get all rows
    $trList = $dom->getElementsByTagName('tr');

    $tr_head = 1;
    foreach ($trList as $tr) {

        // Header
        if( $tr_head === 1 && !empty( $heads_toRemove ) ){
            $tdList = $tr->getElementsByTagName('th');
            foreach( $heads_toRemove as $column_nb ){
                $tr->removeChild( $tdList->item( $column_nb ) );
            }
            $tr_head++;
        }else if( $tr_head > 1 && !empty( $cols_toRemove ) ){
            $tdList = $tr->getElementsByTagName('td');
            // Content rows
            foreach( $cols_toRemove as $column_nb ){
                /*
                echo $column_nb.': ';
                $xml = $tr->ownerDocument->saveXML($tr);
                echo $xml;
                echo '<br />';
                */
                $tr->removeChild( $tdList->item( $column_nb ) );
            }

        }

    }

    $html = $dom->saveHTML();

    $html_return = $html;

    $html_return = str_replace( '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">' , '' , $html_return );
    $html_return = str_replace( '<html><body>' , '' , $html_return );
    $html_return = str_replace( '</body></html>' , '' , $html_return );

    $html_return = str_replace( '&amp;', '&', $html_return );
    return $html_return;

}



?>