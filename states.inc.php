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
 * states.inc.php
 *
 * nineteensixtynine game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => clienttranslate("Game setup"),
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 15 ) //new game bug
    ),

    5 => array(
        "name" => "beforeRound",
        "type" => "game",
        "action" => "stBeforeRound",
        "transitions" => array( "beforeIncome" => 10 )
    ),

    10 => array(
        "name" => "beforeIncome",
        "type" => "game",
        "action" => "stBeforeIncome",
        "transitions" => array( "income" => 15 )
    ),
  
    15 => array(
        "name" => "income",
        "description" => clienttranslate('${actplayer} must takes an income'),
        "descriptionmyturn" => clienttranslate('${you} must take an income'),
        "type" => "activeplayer",
        "possibleactions" => array( "confirmIncome" ),
        "transitions" => array( "afterIncome" => 20 )
    ),

    20 => array(
        "name" => "afterIncome",
        "type" => "game",
        "action" => "stAfterIncome",
        "transitions" => array( "beforeIncome" => 10, "beforePurchase" => 25 )
    ),

    25 => array(
        "name" => "beforePurchase",
        "type" => "game",
        "action" => "stBeforePurchase",
        "transitions" => array( "purchase" => 30 )
    ),

    30 => array(
        "name" => "purchase",
        "description" => clienttranslate('${actplayer} must purchases scientists or intelligence cards'),
        "descriptionmyturn" => clienttranslate('${you} must purchase scientists or intelligence cards'),
        "type" => "activeplayer",
        "args" => "argPurchase",
        "possibleactions" => array( "drawIntelligenceCard", "purchaseScientist", "purchaseSpy", "pass" ),
        "transitions" => array( "placeScientist" => 31, "placeSpy" => 32 )
    ),

    31 => array(
        "name" => "placeScientist",
        "description" => clienttranslate('${actplayer} must place the scientist on an empty slot on his research sheets'),
        "descriptionmyturn" => clienttranslate('${you} must place the scientist on an empty slot on your research sheets'),
        "type" => "activeplayer",
        "args" => "argPlaceScientist",
        "possibleactions" => array( "confirmScientistPlace" ),
        "transitions" => array( "purchase" => 30 )
    ),

    32 => array(
        "name" => "placeSpy",
        "description" => clienttranslate('${actplayer} must place the spy on an empty slot on some other player\'s research sheets'),
        "descriptionmyturn" => clienttranslate('${you} must place the spy on an empty slot on some other player\'s your research sheets'),
        "type" => "activeplayer",
        "args" => "argPlaceSpy",
        "possibleactions" => array( "confirmSpyPlace" ),
        "transitions" => array( "purchase" => 30 )        
    ),    

    35 => array(
        "name" => "afterPurchase",
        "type" => "game",
        "action" => "stAfterPurchase",
        "transitions" => array( "beforePurchase" => 25, "afterRound" => 80 )
    ),    

    80 => array(
        "name" => "afterRound",
        "type" => "game",
        "action" => "stAfterRound",
        "transitions" => array( "beforeRound" => 5, "gameEnd" => 99 )
    ),

    // Final state.
    // Please do not modify.
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);


