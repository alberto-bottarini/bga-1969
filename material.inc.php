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
 * material.inc.php
 *
 * nineteensixtynine game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

$this->matScientistList = array(
	'rookie' => array(
		'name' => clienttranslate('rookie'),
        'nametr' => self::_('rookie'),
        'price' => 3,
        'quantity' => 40
    ),
    'basic' => array(
		'name' => clienttranslate('basic'),
		'nametr' => self::_('basic'),
        'price' => 5,
        'quantity' => 10
    ),
    'genious' => array(
		'name' => clienttranslate('genious'),
        'nametr' => self::_('genious'),
        'price' => 11,
        'quantity' => 10
    ),
    'famous' => array(
		'name' => clienttranslate('famous'),
        'nametr' => self::_('famouse'),
        'price' => 9,
        'quantity' => 10
    )
);

$this->matSpy = array(
	'name' => clienttranslate('spy'),
    'nametr' => self::_('spy'),
    'price' => 7,
    'quantity' => 10
);

$this->matIntelligenceCards = array(
    array(
        'point' => 1,
        'quantity' => 13
    ),
    array(
        'point' => 2,
        'quantity' => 13
    ),
    array(
        'point' => 3,
        'quantity' => 13
    )
);

$this->matCountries = array('usa', 'france', 'russia', 'germany', 'canada');