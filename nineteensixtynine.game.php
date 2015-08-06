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
  * nineteensixtynine.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */


require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );


class nineteensixtynine extends Table
{

	function nineteensixtynine( )
	{
        	
        parent::__construct();
        self::initGameStateLabels( array( 
            "current_income_turn" => 10,
            "current_purchase_turn" => 11,
            "current_mission_turn" => 12,
            "current_intelligence_turn" => 13,

            "current_round" => 14,

			"pending_person_type" => 30
        ) );

        $this->playerBoardTypeList = array('astronautics','ground-control','insurance','intelligence','investors',
            'launchpad','lunar-module','orbital-module','robotics','simulation','technology','vector-rocket');
        
	}

    function personTypeToInt($personType) {
        $keys = array_keys($this->matScientistList);
        $search = array_search($personType, $keys);
        return $search === false ? 10 : $search;
    }

    function intToPersonType($personTypeInt) {
        if($personTypeInt == 10) return 'spy';
        $keys = array_keys($this->matScientistList);
        return $keys[$personTypeInt];
    }
	
    protected function getGameName( )
    {
        return "nineteensixtynine";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        $sql = "DELETE FROM player WHERE 1 ";
        self::DbQuery( $sql ); 

        $default_colors = array( "ff0000", "008000", "0000ff", "ffa500", "773300" );

        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player ) {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
        $sql .= implode( $values, ',' );
        self::DbQuery( $sql );
        self::reloadPlayersBasicInfos();

        $this->DbQuery( "UPDATE player SET player_score = 2" );
/*

        //create scientists
        foreach($this->matScientistList as $type => $scientist) {
            foreach(range(1, $scientist['quantity']) as $i) 
                $this->DbQuery( "INSERT INTO scientist(type, location) VALUES('$type', 'board')" );
        }
        //create spies
        foreach(range(1, 10) as $i) $this->DbQuery( "INSERT INTO spy(location) VALUES('board')" );
*/
        //create playerboard
        foreach($players as $player_id => $player) {
            foreach($this->playerBoardTypeList as $type) {
                foreach(range(1, 2) as $i) $this->DbQuery( "INSERT INTO playerboard(player_id, number, type) VALUES($player_id, $i, '$type')" );    
                $this->DbQuery( "INSERT INTO playerboard_spy(player_id, type) VALUES($player_id, '$type')" );                
            }
        }

        //new game bug
        $this->setGameStateValue("current_round", 1);
        $this->setGameStateValue("current_income_turn", 1);
        $this->activeNextPlayer();

    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array( 'players' => array() );
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score, player_money money FROM player ";
        $result['players'] = self::getCollectionFromDb( $sql );
  
        // TODO: Gather all information about current game situation (visible by player $current_player_id).

        $result['currentRound'] = $this->getGameStateValue("current_round");
        $result['playersheet'] = $this->getObjectListFromDB("SELECT player_id as playerId, type AS sheetType, scientist_type AS scientistType, number AS number FROM playerboard WHERE scientist_type IS NOT NULL" );
        $result['playersheetSpy'] = $this->getObjectListFromDB("SELECT player_id as playerId, type AS sheetType FROM playerboard_spy WHERE playerboard_id IS NOT NULL");
  
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        // TODO: compute and return the game progression

        return 0;
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

    function dbIncScorePoint($playerId, $scorePoint) {
        $this->DbQuery("UPDATE player SET player_score = player_score + $scorePoint WHERE player_id = $playerId");
    }

    function dbIncMoney($playerId, $money) {
        $this->DbQuery("UPDATE player SET player_money = player_money + $money WHERE player_id = $playerId");
    }

    function dbGetMoney($playerId) {
        $player = $this->getObjectFromDB( "SELECT player_money money FROM player WHERE player_id='$playerId'" );
        return $player['money'];
    }

    function dbGetPrestige($playerId) {
        $player = $this->getObjectFromDB( "SELECT player_score score FROM player WHERE player_id='$playerId'" );
        return $player['score'];
    }

	/*
	 * Return if a scientist type is still available to play
	 * @params $scientistType the type of scientist
	 * @return Boolean
	 */
    function dbAreScientistsAvailable($scientistType) {
        $counter = $this->getObjectFromDB( "SELECT COUNT(*) AS count FROM playerboard WHERE scientist_type = '$scientistType'" );
        return $counter['count'] < $this->matScientistList[$scientistType]['quantity'];
    }

	/*
	 * Return if spies are still available to play
	 * @params $scientistType the type of scientist
	 * @return Boolean
	 */
    function dbAreSpiesAvailable() {
		$counter = $this->getObjectFromDB( "SELECT COUNT(*) AS count FROM playerboard WHERE scientist_type = 'spy'" );
        return $counter['count'] < $this->matSpy['quantity']; 
    }

	/*
	 * Fill an empty playerboard with a scientist
     * @param $scientistType
	 * @param $playerId
	 * @param $sheetType
	 * @return Number scientistNumber just inserted
	 */
    function dbPurchaseScientist($scientistType, $playerId, $sheetType) {
        $toUpdate = $this->getObjectFromDB("SELECT id, number FROM playerboard WHERE player_id = $playerId AND type = '$sheetType' AND scientist_type IS NULL ORDER BY number LIMIT 1");
        $toUpdateId = $toUpdate['id'];
        $this->DbQuery("UPDATE playerboard SET scientist_type = '$scientistType' WHERE id = $toUpdateId");
        return $toUpdate;
    }

    /*
     * Fill an empty playerboard_spy with a spy
     * @param $playerId
     * @param $sheetType
     */
    function dbPurchaseSpy($playerId, $sheetType, $playerBoardId) {
        $this->DbQuery("UPDATE playerboard_spy SET playerboard_id = $playerBoardId WHERE player_id = $playerId AND type = '$sheetType'");
    }

    /*
     * Returns assumable spies 
     * @params $currentPlayer Id of current player
     * @return List of available sheet grouped by playerId
     */
    function dbGetAssumableSpies($currentPlayerId) {
        //TODO controllare spie già piazzate dal giocatore
		//posso piazzare tranne in questi casi: 1- ho già piazzato una spia del tipo corrente, 2- il destinatario ha già una spia sulla plancia
		$return = array();
		$availableSheetList = $this->getObjectListFromDB("SELECT type FROM playerboard_spy WHERE player_id = $currentPlayerId AND playerboard_id IS NULL");
		$playerIdList = array_keys($this->loadPlayersBasicInfos());
		foreach($playerIdList as $playerId) if($playerId != $currentPlayerId) {
			$tempPlayer = array();
			foreach($availableSheetList as $sheet) {
				$sheetType = $sheet['type'];
				$counter = $this->getObjectFromDB("SELECT COUNT(*) AS count FROM playerboard WHERE player_id = $playerId AND type = '$sheetType' AND scientist_type = 'spy'");
				if($counter['count'] == 0) {
					$tempPlayer[] = $sheetType;
				}
				
			}
			$return[$playerId] = $tempPlayer;
		}
		return $return;
		//return $this->getObjectListFromDB("SELECT owner_player_id AS player_id, owner_type AS type FROM playerboard_spy WHERE owner_player_id <> $currentPlayer AND spy_id IS NULL");
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Validation functions
////////////  

    function valUserHasMoney($playerId, $requiredMoney, $exception = true) {
        $money = $this->dbGetMoney($playerId);
        $hasMoney = $money >= $requiredMoney;
        if($exception && !$hasMoney) {
            throw new BgaUserException( self::_("You have not enough money to take this action") );
        }
        return $hasMoney;
    }

    function valUserHasPrestige($playerId, $prestige, $exception = true) {
        $score = $this->dbGetPrestige($playerId);
        $hasPrestige = $score >= $prestige;
        if($exception && !$hasPrestige) {
            throw new BgaUserException( self::_("You have not enough prestige points to take this action") );
        }
        return $hasPrestige;
    }

    function valScientistIsAvailable($scientist, $exception = true) {
        $available = $this->dbAreScientistsAvailable($scientist);
        if($exception && !$available) {
            throw new BgaUserException( self::_("This kind of scientist is no more available to hire") );
        }
        return $available;
    }

    function valSpiesAreAvailable($exception = true) {
        $available = $this->dbAreSpiesAvailable();
        if($exception && !$available) {
            throw new BgaUserException( self::_("Spies are no more available to hire") );
        }
        return $available;   
    }

    function valScientistIsPurchasable($playerId, $scientistType, $sheetType, $exception = true) {
        $fromDb = $this->getObjectListFromDB("SELECT scientist_type as type FROM playerboard WHERE player_id = $playerId AND type = '$sheetType' AND scientist_type IS NOT NULL");
        if(count($fromDb) == 0) {
            $purchasable = true;
        } else if(count($fromDb) > 1) {
            $purchasable = false;
        } else {
            if($scientistType == 'basic') $purchasable = true;
            else {
                $purchasable = $fromDb[0]['type'] != $scientistType;
            }
        }
        if($exception && !$purchasable) {
            throw new BgaUserException( self::_("You cannot assume this scientist on this research box") );
        }
        return $purchasable;
    }

    function valSpyIsPurchasable($currentPlayerId, $sheetType, $targetPlayerId, $exception = true) {
        if($currentPlayerId == $targetPlayerId) {
            $purchasable = false;
        } else {
            $availableSheetList = $this->getObjectListFromDB("SELECT type FROM playerboard_spy WHERE player_id = $currentPlayerId AND playerboard_id IS NULL");
            if(!in_array($sheetType, array_map(create_function('$o', 'return $o[\'type\'];'), $availableSheetList))) {
                $purchasable = false;
            } else {
                $counter = $this->getObjectFromDB("SELECT COUNT(*) AS count FROM playerboard WHERE player_id = $targetPlayerId AND type = '$sheetType' AND scientist_type = 'spy'");
                $purchasable = $counter['count'] == 0;
            }
        }
        if($exception && !$purchasable) {
            throw new BgaUserException( self::_("You cannot assume spy on this research box") );
        }
        return $purchasable;
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in nineteensixtynine.action.php)
    */

    function acConfirmIncome($status) {
        $playerId = $this->getActivePlayerId();
        $playerName = $this->getActivePlayerName();
        if($status > 0) $this->valUserHasPrestige($playerId, $status);
        $currentRound = $this->getGameStateValue("current_round");
        if($status == 0) {
            $income = $currentRound + 11;
            $this->notifyAllPlayers("message", clienttranslate('${playerName} takes default income: ${income}M'), array(
                "playerName" => $playerName,
                "income" => $income
            ));
        } else { 
            $income = $currentRound + 11 + $status * 2;
            $this->notifyAllPlayers("message", clienttranslate('${playerName} takes extra income: ${income}M and loses ${prestige} prestige'), array(
                "playerName" => $playerName,
                "income" => $income,
                "prestige" => $status
            ));
        }
        $this->dbIncMoney($playerId, $income);
        $this->notifyAllPlayers("moneyChanged", "", array(
            "playerId" => $playerId,
            "money" => $income
        ));
        if($status > 0) {
            $this->dbIncScorePoint($playerId, -($status));
            $this->notifyAllPlayers("scorePointAcquired", "", array(
                "playerId" => $playerId,
                "scorePoint" => -($status)
            ));
        }
        $this->gamestate->nextState('afterIncome');
    }

    function acPurchaseScientist($scientistType) {
        $playerId = $this->getActivePlayerId();
        $playerName = $this->getActivePlayerName();
        $this->valScientistIsAvailable($scientistType);
        $this->valUserHasMoney($playerId, $this->matScientistList[$scientistType]['price']);
		$this->setGameStateValue("pending_person_type", $this->personTypeToInt($scientistType));

        $this->notifyAllPlayers("message", clienttranslate('${playerName} purchases a ${type} scientist.'), array(
            "playerName" => $playerName,
            "type" => $this->matScientistList[$scientistType]['name']
        ));
        $this->gamestate->nextState('placeScientist');
    }

    function acPurchaseSpy() {
        $playerId = $this->getActivePlayerId();
        $playerName = $this->getActivePlayerName();
        $this->valSpiesAreAvailable();
        $this->valUserHasMoney($playerId, $this->matSpy['price']);
		$this->setGameStateValue("pending_person_type", $this->personTypeToInt('spy'));
        $this->notifyAllPlayers("message", clienttranslate('${playerName} purchases a spy.'), array(
            "playerName" => $playerName
        ));
        $this->gamestate->nextState('placeSpy');
    }

    function acConfirmScientistPlace($sheetType) {
        $pendingScientistType = $this->intToPersonType($this->getGameStateValue("pending_person_type"));
		//TODO ha senso rimuovere il pending o chissenefrega?
        $playerId = $this->getActivePlayerId();
        $pendingScientistCost = $this->matScientistList[$pendingScientistType]['price'];
        
        $this->valScientistIsPurchasable($playerId, $pendingScientistType, $sheetType);
        $purchasedScientist = $this->dbPurchaseScientist($pendingScientistType, $playerId, $sheetType);
        $this->dbIncMoney($playerId, -$pendingScientistCost);

        $this->notifyAllPlayers("moneyChanged", "", array(
            "playerId" => $playerId,
            "money" => -$pendingScientistCost
        ));
        $this->notifyAllPlayers("scientistPurchased", "", array(
            "playerId" => $playerId,
            "scientistType" => $pendingScientistType,
            "sheetType" => $sheetType,
            "number" => $purchasedScientist['number']
        ));

        //TODO notifica messaggio
        $this->gamestate->nextState('purchase');
    }

    function acConfirmSpyPlace($sheetType, $targetPlayerId) {
        $playerId = $this->getActivePlayerId();
        $spyCost = $this->matSpy['price'];

        $this->valSpyIsPurchasable($playerId, $sheetType, $targetPlayerId);
        $purchasedSpy = $this->dbPurchaseScientist('spy', $targetPlayerId, $sheetType);
        $this->dbPurchaseSpy($playerId, $sheetType, $purchasedSpy['id']);
        $this->dbIncMoney($playerId, -$spyCost);

        $this->notifyAllPlayers("moneyChanged", "", array(
            "playerId" => $playerId,
            "money" => -$spyCost
        ));
        $this->notifyAllPlayers("scientistPurchased", "", array(
            "playerId" => $targetPlayerId,
            "scientistType" => 'spy',
            "sheetType" => $sheetType,
            "number" => $purchasedSpy['number']
        ));
        $this->notifyAllPlayers("spyPurchased", "", array(
            "playerId" => $playerId,
            "sheetType" => $sheetType
        ));

        //TODO notifica messaggio
        $this->gamestate->nextState('purchase');
    }

    function acPass() {
        $this->gamestate->nextState('afterPurchase');
    }

    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    function argPurchase() {
        $availableScientists = array();
        $playerId = $this->getActivePlayerId();
        //TODO need to check also if player has free space for each kind of scientists
        foreach($this->matScientistList as $type => $scientist) {
            $quantity = $scientist['quantity'];
            if($this->valScientistIsAvailable($type, false)) {
                if($this->valUserHasMoney($playerId, $scientist['price'], false)) {
                    $availableScientists[] = $type;
                }
            }
        }
        return array(
            "availableScientists" => $availableScientists,
            "availableSpies" => $this->valSpiesAreAvailable(false) && $this->valUserHasMoney($playerId, $this->matSpy['price'], false)
        );
    }

    function argPlaceScientist() {
		$pendingScientistType = $this->intToPersonType($this->getGameStateValue("pending_person_type"));
        $playerId = $this->getActivePlayerId();
        $availableSheets = array();
        foreach($this->playerBoardTypeList as $type) {
            if($this->valScientistIsPurchasable($playerId, $pendingScientistType, $type, false)) $availableSheets[] = $type;
        }
        return array(
            "availableSheets" => $availableSheets
        );
    }

    function argPlaceSpy() {
        $return = array();
        $currentPlayerId = $this->getActivePlayerId();
        $playerIdList = array_keys($this->loadPlayersBasicInfos());
        foreach($playerIdList as $playerId) if($playerId != $currentPlayerId) {
            $tempPlayer = array();
            foreach($this->playerBoardTypeList as $sheetType) {                
                if($this->valSpyIsPurchasable($currentPlayerId, $sheetType, $playerId, false)) {
                    $tempPlayer[] = $sheetType;
                }
            }
            $return[$playerId] = $tempPlayer;
        }
        return array(
            "availableSheets" => $return
        );
    }


//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    function stBeforeRound() {
        $currentRound = $this->incGameStateValue("current_round", 1);        
        $this->setGameStateValue("current_income_turn", 0);
        $this->setGameStateValue("current_purchase_turn", 0);
        $this->notifyAllPlayers("message", clienttranslate('Round ${currentRound} started!'), array(
            "currentRound" => $currentRound
        ));
        $this->notifyAllPlayers("newRound", "", array(
            "currentRound" => $currentRound
        ));
        $this->activeNextPlayer(); //to activate the next from the previous first player
        $this->gamestate->nextState("beforeIncome");
    }

    function stBeforeIncome() {
        $this->incGameStateValue("current_income_turn", 1);
        $this->activeNextPlayer();
        $this->gamestate->nextState("income");
    }

    function stAfterIncome() {
        $currentIncomeTurn = $this->getGameStateValue("current_income_turn");
        if($currentIncomeTurn == $this->getPlayersNumber()) $this->gamestate->nextState("beforePurchase");
        else $this->gamestate->nextState("beforeIncome");
    }

    function stBeforePurchase() {
        $this->incGameStateValue("current_purchase_turn", 1);
        $this->activeNextPlayer();
        $this->gamestate->nextState("purchase");
    }

    function stAfterPurchase() {
        $currentPurchaseTurn = $this->getGameStateValue("current_purchase_turn");
        if($currentPurchaseTurn == self::getPlayersNumber()) $this->gamestate->nextState("afterRound");
        else $this->gamestate->nextState("beforePurchase");
    }

    function stAfterRound() {
        $currentRound = $this->getGameStateValue("current_round");
        if($currentRound == 7) $this->gamestate->nextState("gameEnd");
        else $this->gamestate->nextState("beforeRound");
    }
    

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        if ($state['type'] == "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState( "zombiePass" );
                	break;
            }

            return;
        }

        if ($state['type'] == "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $sql = "
                UPDATE  player
                SET     player_is_multiactive = 0
                WHERE   player_id = $active_player
            ";
            self::DbQuery( $sql );

            $this->gamestate->updateMultiactiveOrNextState( '' );
            return;
        }

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
}
