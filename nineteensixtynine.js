/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * nineteensixtynine implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * nineteensixtynine.js
 *
 * nineteensixtynine user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter"
],
function (dojo, declare) {

    dojo.forEachObject = function(obj, f, scope){
        for(var key in obj){
            if(obj.hasOwnProperty(key)){
                f.call(scope, key, obj[key]);
            }
        }
    }

    var nineteensixtynine = {
        constructor: function(){
            this.currentRound = 0;
            this.purchaseScientistConnections = [];
            this.purchaseSpyConnections = [];
            this.purchaseCardConnections = [];
            this.playerSheetConnections = [];
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup", gamedatas);

            // Setting up player boards
            for( var playerId in gamedatas.players )
            {
                var player = gamedatas.players[playerId];
                var playerBoard = $('player_board_'+playerId);
                dojo.place( this.format_block('jsptl_player_flag', {
                    country: player.country
                }), playerBoard );
                dojo.place( this.format_block('jstpl_player_money_counter', { 
                    id: playerId,
                    money: player.money
                }), playerBoard );
                dojo.place( this.format_block('jstpl_player_card_counter', { 
                    id: playerId,
                    card: gamedatas.playerCardNumber[playerId].countCard
                }), playerBoard );                
            }
            this.addTooltipToClass('player-money-counter',  _('Available millions'), '' );
            this.addTooltipToClass('player-card-counter',  _('Number of intelligence cards in hand'), '' );

            var that = this;

            this.currentRound = gamedatas.currentRound;

            dojo.forEach(gamedatas.playersheet, function(playersheet) {
                that.addScientistOnBoard(playersheet.playerId, playersheet.sheetType, playersheet.scientistType, playersheet.number);
            });
            dojo.forEach(gamedatas.playersheetSpy, function(playersheetSpy) {
                that.addSpyOnBoard(playersheetSpy.playerId, playersheetSpy.sheetType);
            });
            dojo.forEach(gamedatas.myIntelligenceCard, function(card) {
                that.addCardOnBoard(that.player_id ,card);
            });

            // Setup game notifications to handle (see "setupNotifications" method below)
            this.setupNotifications();

            console.log( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log('[ENTERING STATE]', stateName, args);
            
            switch( stateName )
            {
            
            case 'purchase':
                if(this.isCurrentPlayerActive()) {
                    this.enablePurchaseScientistTrigger(args.args.availableScientists);
                    this.enablePurchaseSpyTrigger(args.args.availableSpies);
                    this.enablePurchaseIntelligenceCardTrigger(args.args.availableIntelligenceCards);
                }
                break;

            case 'placeScientist':
                if(this.isCurrentPlayerActive()) this.enablePlaceScientistTrigger(this.getActivePlayerId(), args.args.availableSheets);
                break;

            case 'placeSpy':
                if(this.isCurrentPlayerActive()) this.enablePlaceSpiesTrigger(args.args.availableSheets);
                break;

            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log('[LEAVING STATE]', stateName);
            
            switch( stateName )
            {
            
            case 'purchase':
                if(this.isCurrentPlayerActive()) {
                    this.disablePurchaseScientistTrigger();
                    this.disablePurchaseSpyTrigger();
                }
                break;

            case 'placeScientist':
                if(this.isCurrentPlayerActive()) this.disablePlaceScientistTrigger(this.getActivePlayerId());
                break;

            case 'placeSpy':
                if(this.isCurrentPlayerActive()) this.disablePlaceSpiesTrigger(this.getActivePlayerId());
                break;

            }               
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log('[ACTION BUTTONS]', stateName, args);
                      
            if( this.isCurrentPlayerActive() )
            {       

                switch( stateName )
                {

                case 'income':
                    this.addActionButton( 'income-button-0', dojo.string.substitute('Take default income: ${income}M', {
                        income: parseInt(this.currentRound, 10) + 11
                    }), 'onIncome' );
                    this.addActionButton( 'income-button-1', dojo.string.substitute('Take extra income: ${income}M (lose ${prestige} prestige)', {
                        income: parseInt(this.currentRound, 10) + 11 + 2,
                        prestige: 1
                    }), 'onIncome' );
                    if(this.currentRound > 2) {
                        this.addActionButton( 'income-button-2', dojo.string.substitute('Take extra income: ${income}M (lose ${prestige} prestige)', {
                            income: parseInt(this.currentRound, 10) + 11 + 4,
                            prestige: 2
                        }), 'onIncome' );
                    }
                    if(this.currentRound > 5) {
                        this.addActionButton( 'income-button-3', dojo.string.substitute('Take extra income: ${income}M (lose ${prestige} prestige)', {
                            income: parseInt(this.currentRound, 10) + 11 + 6,
                            prestige: 3 
                        }), 'onIncome' );
                    }
                    break;

                case 'purchase':
                    this.addActionButton( 'purchase-button-pass', dojo.string.substitute('I\'m done!'), 'onPass' );
                    break;

                }

               
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        updatePlayerMoney: function(playerId, money) {
            console.log("[UTILITY] Update player money", playerId, money);
            this.getPlayerMoneyCounterItem(playerId).innerHTML = money;
        },

        incPlayerMoney: function(playerId, money) {
            console.log("[UTILITY] Increase player money", playerId, money);
            var el = this.getPlayerMoneyCounterItem(playerId);
            el.innerHTML = parseInt(el.innerHTML, 10) + money;
        },

        incPlayerIntelligenceCards: function(playerId, card) {
            console.log("[UTILITY] Increase player intelligence card", playerId, card);
            var el = this.getPlayerIntelligenceCardCounterItem(playerId);
            el.innerHTML = parseInt(el.innerHTML, 10) + card;
        },

        addScientistOnBoard: function(playerId, sheetType, scientistType, number) {
            var node = this.getPlayerSheetItem(playerId, sheetType);
            node.addClass("player-board-scientist-" + number +"-" + scientistType);
        },

        addSpyOnBoard: function(playerId, sheetType) {
            var node = this.getPlayerSheetItem(playerId, sheetType);
            node.addClass("player-board-spy");
        },

        addCardOnBoard: function(playerId, card) {
            console.log("[UTILITY] Add Intelligence Card on Board", playerId, card);
            var container = this.getPlayerCardContainer(playerId);
            dojo.place( this.format_block('jstpl_player_card', { 
                point: card.point,
                id: card.id
            }), container);
        },

        ///////////////////////////////////////////////////
        //// DOM methods   

        getPurchaseScientistTriggerList: function() {
            return dojo.query("#board .purchase-scientist-trigger");
        },

        getPurchaseScientistTriggerListAvailable: function(availableScientists) {
            var that = this;
            return this.getPurchaseScientistTriggerList().filter(function(node) {
                var scientistType = node.id.match('(.*)-trigger')[1];
                return dojo.indexOf(availableScientists, scientistType) != -1;
            });
        },

        getPurchaseScientistTriggerListNotAvailable: function(availableScientists) {
            var that = this;
            return this.getPurchaseScientistTriggerList().filter(function(node) {
                var scientistType = node.id.match('(.*)-trigger')[1];
                return dojo.indexOf(availableScientists, scientistType) == -1;
            });
        },

        getPurchaseSpyTrigger: function() {
            return dojo.query("#board #spy-trigger");
        },

        getPurchaseIntelligenceCardTrigger: function() {
            return dojo.query('#board #intelligence-card-trigger');
        },

        getPlayerSheetListAvailable: function(playerId, availableSheets) {
            var that = this;
            return this.getPlayerSheetList(playerId).filter(function(node) {
                var sheetName = node.id.match('player-sheet-' + playerId + '-(.*)')[1];
                return dojo.indexOf(availableSheets, sheetName) != -1;
            });
        },

        getPlayerSheetListNotAvailable: function(playerId, availableSheets) {
            var that = this;
            return this.getPlayerSheetList(playerId).filter(function(node) {
                var sheetName = node.id.match('player-sheet-' + playerId + '-(.*)')[1];
                return dojo.indexOf(availableSheets, sheetName) == -1;
            });
        },

        getPlayerSheetList: function(playerId) {
            return dojo.query("#player-sheet-" + playerId + " .player-sheet");
        },

        getPlayerSheetAllList: function() {
            return dojo.query("[id^='player-sheet'] .player-sheet");
        },

        getPlayerSheetItem: function(playerId, sheetType) {
            return dojo.query("#player-sheet-" + playerId + " .player-sheet-" + sheetType);
        },

        getPlayerCardContainer: function(playerId) {
            return dojo.query("#player-card-" + playerId)[0];
        },

        getPlayerMoneyCounterItem: function(playerId) {
            return dojo.query("#player-"+playerId+"-money-counter .value")[0];
        },

        getPlayerIntelligenceCardCounterItem: function(playerId) {
            return dojo.query("#player-"+playerId+"-card-counter .value")[0];
        },

        ///////////////////////////////////////////////////
        //// Event methods

        enablePurchaseScientistTrigger: function(availableScientists) {
            console.log("[EVENT] Enable purchase scientist trigger");
            var that = this;
            var availableScientistTriggerList = this.getPurchaseScientistTriggerListAvailable(availableScientists);
            var notAvailableScientistTriggerList = this.getPurchaseScientistTriggerListNotAvailable(availableScientists);
            this.purchaseScientistConnections = availableScientistTriggerList.map(function(node) {
                return dojo.connect(node, "onclick", that, "onPurchaseScientist"); 
            });
            availableScientistTriggerList.addClass("clickable");
            notAvailableScientistTriggerList.addClass("not-clickable");
        },

        disablePurchaseScientistTrigger: function() {
            console.log("[EVENT] Disable purchase scientist trigger");
            dojo.forEach(this.purchaseScientistConnections, function(connection) {
                dojo.disconnect(connection);
            });
            this.purchaseScientistConnections = [ ];
            this.getPurchaseScientistTriggerList().removeClass("clickable").removeClass("not-clickable");
        },

        enablePurchaseSpyTrigger: function(availableSpy) {
            console.log("[EVENT] Enable purchase spy trigger");
            var that = this;
            var trigger = this.getPurchaseSpyTrigger();
            if(availableSpy) {
                this.purchaseSpyConnections = [ dojo.connect(trigger[0], "onclick", that, "onPurchaseSpy") ];
                trigger.addClass("clickable");
            } else {
                trigger.addClass("not-clickable");
            }
        },

        disablePurchaseSpyTrigger: function() {
            console.log("[EVENT] Disable purchase spy trigger");
            dojo.forEach(this.purchaseSpyConnections, function(connection) {
                dojo.disconnect(connection);
            });
            this.getPurchaseSpyTrigger().removeClass("clickable").removeClass("not-clickable");
        },

        enablePurchaseIntelligenceCardTrigger: function(availableCard) {
            console.log("[EVENT] Enable purchase intelligence card trigger");
            var that = this;
            var trigger = this.getPurchaseIntelligenceCardTrigger();
            if(availableCard) {
                this.purchaseCardConnections = [ dojo.connect(trigger[0], "onclick", that, "onPurchaseCard") ];
                trigger.addClass("clickable");
            } else {
                trigger.addClass("not-clickable");
            }
        },

        disablePurchaseIntelligenceCardTrigger: function() {
            console.log("[EVENT] Disable purchase intelligence card trigger");
            dojo.forEach(this.purchaseCardConnections, function(connection) {
                dojo.disconnect(connection);
            });
            this.getPurchaseIntelligenceCardTrigger().removeClass("clickable").removeClass("not-clickable");
        },

        enablePlaceScientistTrigger: function(playerId, availableSheets) {
            var that = this;
            var availableTriggerList = this.getPlayerSheetListAvailable(playerId, availableSheets);
            var notAvailableTriggerList = this.getPlayerSheetListNotAvailable(playerId, availableSheets);
            this.playerSheetConnections = availableTriggerList.map(function(node) {
                return dojo.connect(node, "onclick", that, "onConfirmPlaceScientist"); 
            });
            availableTriggerList.addClass("clickable");
            notAvailableTriggerList.addClass("not-clickable");
        },

        disablePlaceScientistTrigger: function(playerId) {
            dojo.forEach(this.playerSheetConnections, function(connection) {
                dojo.disconnect(connection);
            });
            this.playerSheetConnections = [ ];
            this.getPlayerSheetList(playerId).removeClass("clickable").removeClass("not-clickable");
        },

        enablePlaceSpiesTrigger: function(availableSpies) {
            var that = this;
            var availableTriggerList;
            var notAvailableTriggerList;
            dojo.forEachObject(availableSpies, function(playerId, availableSheets) {
                availableTriggerList = 
                    availableTriggerList != undefined ? 
                    availableTriggerList.concat(that.getPlayerSheetListAvailable(playerId, availableSheets)) : 
                    that.getPlayerSheetListAvailable(playerId, availableSheets);
                notAvailableTriggerList = 
                    notAvailableTriggerList != undefined ? 
                    notAvailableTriggerList.concat(that.getPlayerSheetListNotAvailable(playerId, availableSheets)) : 
                    that.getPlayerSheetListNotAvailable(playerId, availableSheets);
            });
            this.playerSheetConnections = availableTriggerList.map(function(node) {
                return dojo.connect(node, "onclick", that, "onConfirmPlaceSpy"); 
            });
            availableTriggerList.addClass("clickable");
            notAvailableTriggerList.addClass("not-clickable");
        },

        disablePlaceSpiesTrigger: function() {
            dojo.forEach(this.playerSheetConnections, function(connection) {
                dojo.disconnect(connection);
            });
            this.playerSheetConnections = [ ];
            this.getPlayerSheetAllList().removeClass("clickable").removeClass("not-clickable");
        },

        ///////////////////////////////////////////////////
        //// Player's action
        
        onIncome: function(evt) {
            dojo.stopEvent(evt);
            var status = evt.target.id.match(/income-button-([0-3])/)[1]
            if(this.checkAction("confirmIncome")) {
                this.ajaxcall('/nineteensixtynine/nineteensixtynine/confirmIncome.html', { lock: true,
                    status: status
                }, this, function() { });
            }
        },

        onPass: function(evt) {
            dojo.stopEvent(evt);
            if(this.checkAction("pass")) {
                this.ajaxcall('/nineteensixtynine/nineteensixtynine/pass.html', { lock: true,
                }, this, function() { });
            }
        },

        onPurchaseScientist: function(evt) {
            dojo.stopEvent(evt);
            var type = evt.target.id;
            if(this.checkAction("purchaseScientist")) {
                var scientist = type.match(/([a-z]*)-trigger/)[1];
                this.ajaxcall('/nineteensixtynine/nineteensixtynine/purchaseScientist.html', { lock: true,
                    scientist: scientist
                }, this, function() { });
            }
        },

        onPurchaseSpy: function(evt) {
            dojo.stopEvent(evt);
            if(this.checkAction("purchaseSpy")) {
                this.ajaxcall('/nineteensixtynine/nineteensixtynine/purchaseSpy.html', { lock: true,
                }, this, function() { });
            }
        },

        onPurchaseCard: function(evt) {
            dojo.stopEvent(evt);
            if(this.checkAction("purchaseIntelligenceCard")) {
                this.ajaxcall('/nineteensixtynine/nineteensixtynine/purchaseIntelligenceCard.html', { lock: true,
                }, this, function() { });
            }
        },

        onConfirmPlaceScientist: function(evt) {
            dojo.stopEvent(evt);
            if(this.checkAction("confirmScientistPlace")) {
                var sheet = evt.target.id.match(/player-sheet-([0-9]*)-([a-z\-]*)/)[2];
                this.ajaxcall('/nineteensixtynine/nineteensixtynine/confirmScientistPlace.html', { lock: true,
                    sheet: sheet
                }, this, function() { });
            }
        },

        onConfirmPlaceSpy: function(evt) {
            dojo.stopEvent(evt);
            if(this.checkAction("confirmSpyPlace")) {
                var matches = evt.target.id.match(/player-sheet-([0-9]*)-([a-z\-]*)/) 
                var player = matches[1];
                var sheet = matches[2];
                this.ajaxcall('/nineteensixtynine/nineteensixtynine/confirmSpyPlace.html', { lock: true,
                    player: player,
                    sheet: sheet
                }, this, function() { });
            }
        },

        
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            dojo.subscribe( "newRound", this, this.notifNewRound );
            dojo.subscribe( "scorePointAcquired", this, this.notifScorePointAcquired );
            dojo.subscribe( "moneyChanged", this, this.notifMoneyChanged);
            dojo.subscribe( "scientistPurchased", this, this.notifScientistPurchased);
            dojo.subscribe( "spyPurchased", this, this.notifSpyPurchased);
            dojo.subscribe( "intelligenceCardPurchased", this, this.notifIntelligenceCardPurchased);
            dojo.subscribe( "intelligenceCardDrawn", this, this.notifIntelligenceCardDrawn);
        },  

        notifNewRound: function(notif) {
            this.currentRound = notif.args.currentRound;
        },

        notifScorePointAcquired: function(notif) {
            console.log("Notification: scorePointAcquired", notif);
            var scorePoint = notif.args.scorePoint,
                playerId = notif.args.playerId || this.getActivePlayerId();
            this.scoreCtrl[playerId].incValue(scorePoint);
        },

        notifMoneyChanged: function(notif) {
            console.log("Notification: moneyChanged", notif);
            var money = notif.args.money,
                playerId = notif.args.playerId || this.getActivePlayerId();
            this.incPlayerMoney(playerId, money);
        },

        notifScientistPurchased: function(notif) {
            console.log("Notification: scientistPurchased", notif);
            var playerId = notif.args.playerId || this.getActivePlayerId(),
                scientistType = notif.args.scientistType,
                sheetType = notif.args.sheetType,
                number = notif.args.number;
            this.addScientistOnBoard(playerId, sheetType, scientistType, number);
        },

        notifSpyPurchased: function(notif) {
            console.log("Notification: spyPurchased", notif);
            var playerId = notif.args.playerId || this.getActivePlayerId(),
                sheetType = notif.args.sheetType;
            this.addSpyOnBoard(playerId, sheetType);
        },

        notifIntelligenceCardPurchased: function(notif) {
            console.log("Notification: intelligenceCardPurchased", notif);
            var playerId = notif.args.playerId || this.getActivePlayerId();
            this.incPlayerIntelligenceCards(playerId, 1);
        },

        notifIntelligenceCardDrawn: function(notif) {
            console.log("Notification: intelligenceCardDrawn", notif);
            var playerId = notif.args.playerId || this.getActivePlayerId(),
                card = notif.args.intelligenceCard;
            this.addCardOnBoard(playerId, card);
        }
        
    }

    window.nineteensixtynine = nineteensixtynine;

    return declare("bgagame.nineteensixtynine", ebg.core.gamegui, nineteensixtynine);

});
