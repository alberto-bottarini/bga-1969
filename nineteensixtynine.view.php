<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * nineteensixtynine implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * nineteensixtynine.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in nineteensixtynine_nineteensixtynine.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
  require_once( APP_BASE_PATH."view/common/game.view.php" );
  
  class view_nineteensixtynine_nineteensixtynine extends game_view
  {
    function getGameName() {
        return "nineteensixtynine";
    }    
  	function build_page( $viewArgs )
  	{		
  	    // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );
        global $g_user;
        $current_player_id = $g_user->get_id(); 

        /*********** Place your code below:  ************/


        $this->tpl['YOUR_RESEARCH_BOARDS'] = self::_("Your research boards");
        $this->tpl['OTHER_PLAYERS_BOARDS'] = self::_("Other players' boards");
        $this->tpl['YOUR_CARD_HAND']       = self::_("Your cards hand");
        
        foreach($players as $player) {
          if($player['player_id'] == $current_player_id) {
            $this->tpl['MY_PLAYER_ID'] = $current_player_id;
            $this->tpl['MY_PLAYER_NAME'] = $player['player_name'];
          }
        }

        $this->page->begin_block("nineteensixtynine_nineteensixtynine", "playerboard");
        foreach($players as $player) {
          if($player['player_id'] != $current_player_id) {
            $this->page->insert_block("playerboard", array(
              "PLAYER_ID" => $player['player_id'],
              "PLAYER_NAME" => $player['player_name']
            ));
          }
        }


        /*********** Do not change anything below this line  ************/
  	}
  }
  

