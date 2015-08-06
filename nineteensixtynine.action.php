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
 * nineteensixtynine.action.php
 *
 * nineteensixtynine main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/nineteensixtynine/nineteensixtynine/myAction.html", ...)
 *
 */
  
  
  class action_nineteensixtynine extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "nineteensixtynine_nineteensixtynine";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 
  	
    public function confirmIncome() {
      $this->setAjaxMode();
      $status = $this->getArg("status", AT_posint, true);
      $this->game->acConfirmIncome($status);
      $this->ajaxResponse();
    }

    public function pass() {
      $this->setAjaxMode();
      $this->game->acPass();
      $this->ajaxResponse();
    }

    public function purchaseScientist() {
      $this->setAjaxMode();
      $typeList = array_keys($this->game->matScientistList);
      $scientist = $this->getArg("scientist", AT_enum, true, $typeList[0], $typeList);
      $this->game->acPurchaseScientist($scientist);
      $this->ajaxResponse();
    }

    public function purchaseSpy() {
      $this->setAjaxMode();
      $this->game->acPurchaseSpy();
      $this->ajaxResponse();  
    }

    public function confirmScientistPlace() {
      $this->setAjaxMode();
      $sheet = $this->getArg("sheet", AT_enum, true, $this->game->playerBoardTypeList[0], $this->game->playerBoardTypeList);
      $this->game->acConfirmScientistPlace($sheet);
      $this->ajaxResponse();  
    }

    public function confirmSpyPlace() {
      $this->setAjaxMode();
      $sheet = $this->getArg("sheet", AT_enum, true, $this->game->playerBoardTypeList[0], $this->game->playerBoardTypeList);
      $playerId = $this->getArg("player", AT_int);
      $this->game->acConfirmSpyPlace($sheet, $playerId);
      $this->ajaxResponse(); 
    }

  }
  

