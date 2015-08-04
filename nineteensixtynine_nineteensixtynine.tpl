{OVERALL_GAME_HEADER}

<div style="float: left; margin-right: 20px;" class="whiteblock">

	<div id="board">

		<div id="intelligence-card-trigger" 	class="purchase-trigger"></div>
		<div id="genious-trigger" 				class="purchase-trigger purchase-scientist-trigger"></div>
		<div id="rookie-trigger" 				class="purchase-trigger purchase-scientist-trigger"></div>
		<div id="spy-trigger" 					class="purchase-trigger"></div>
		<div id="basic-trigger" 				class="purchase-trigger purchase-scientist-trigger"></div>
		<div id="famous-trigger" 				class="purchase-trigger purchase-scientist-trigger"></div>

	</div>
	<div class="clear"></div>

</div>

<div style="float: left" class="whiteblock" id="player-sheet-{MY_PLAYER_ID}">
	<h3>{YOUR_RESEARCH_BOARDS}</h3>
	<div class="player-sheet-container">
		<div class="player-sheet player-sheet-ground-control" 		id="player-sheet-{MY_PLAYER_ID}-ground-control">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<div class="player-sheet player-sheet-robotics" 			id="player-sheet-{MY_PLAYER_ID}-robotics">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<div class="player-sheet player-sheet-simulation" 			id="player-sheet-{MY_PLAYER_ID}-simulation">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<br/>
		<div class="player-sheet player-sheet-investors" 			id="player-sheet-{MY_PLAYER_ID}-investors">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<div class="player-sheet player-sheet-intelligence" 		id="player-sheet-{MY_PLAYER_ID}-intelligence">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<div class="player-sheet player-sheet-insurance" 			id="player-sheet-{MY_PLAYER_ID}-insurance">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
	</div>
	<br/>
	<br/>
	<div class="player-sheet-container">
		<div class="player-sheet player-sheet-orbital-module" 		id="player-sheet-{MY_PLAYER_ID}-orbital-module">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<div class="player-sheet player-sheet-technology" 			id="player-sheet-{MY_PLAYER_ID}-technology">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<br/>
		<div class="player-sheet player-sheet-lunar-module" 		id="player-sheet-{MY_PLAYER_ID}-lunar-module">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<div class="player-sheet player-sheet-launchpad" 			id="player-sheet-{MY_PLAYER_ID}-launchpad">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<br/>
		<div class="player-sheet player-sheet-astronautics" 		id="player-sheet-{MY_PLAYER_ID}-astronautics">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
		<div class="player-sheet player-sheet-vector-rocket" 		id="player-sheet-{MY_PLAYER_ID}-vector-rocket">
			<span class="player-sheet-scientist-1"></span>
			<span class="player-sheet-scientist-2"></span>
			<span class="player-sheet-spy"></span>
		</div>
	</div>
	<div class="clear"></div>
</div>

<hr class="clear"/>

<div  class="whiteblock">
	<h3>{OTHER_PLAYERS_BOARDS}</h3>
</div>

<!-- BEGIN playerboard -->
<div style="float: left; margin-right: 20px" id="player-sheet-{PLAYER_ID}">
	<div  class="whiteblock">
		<h3>{PLAYER_NAME}</h3>
		<div class="player-sheet-container">
			<div class="player-sheet player-sheet-ground-control" 	id="player-sheet-{PLAYER_ID}-ground-control">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<div class="player-sheet player-sheet-robotics" 		id="player-sheet-{PLAYER_ID}-robotics">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<div class="player-sheet player-sheet-simulation" 		id="player-sheet-{PLAYER_ID}-simulation">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<br/>
			<div class="player-sheet player-sheet-investors" 		id="player-sheet-{PLAYER_ID}-investors">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<div class="player-sheet player-sheet-intelligence" 	id="player-sheet-{PLAYER_ID}-intelligence">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<div class="player-sheet player-sheet-insurance" 		id="player-sheet-{PLAYER_ID}-insurance">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
		</div>
		<br/>
		<br/>
		<div class="player-sheet-container">
			<div class="player-sheet player-sheet-orbital-module" 	id="player-sheet-{PLAYER_ID}-orbital-module">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<div class="player-sheet player-sheet-technology" 		id="player-sheet-{PLAYER_ID}-technology">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<br/>
			<div class="player-sheet player-sheet-lunar-module" 	id="player-sheet-{PLAYER_ID}-lunar-module">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<div class="player-sheet player-sheet-launchpad" 		id="player-sheet-{PLAYER_ID}-launchpad">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<br/>
			<div class="player-sheet player-sheet-astronautics" 	id="player-sheet-{PLAYER_ID}-astronautics">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
			<div class="player-sheet player-sheet-vector-rocket" 	id="player-sheet-{PLAYER_ID}-vector-rocket">
				<span class="player-sheet-scientist-1"></span>
				<span class="player-sheet-scientist-2"></span>
			</div>
		</div>
	</div>
</div>
<!-- END playerboard -->


<script type="text/javascript">

var jstpl_player_money_counter = '<div class="player-money-counter" id="player-${id}-money-counter">${money}</div>';

</script>  

{OVERALL_GAME_FOOTER}
