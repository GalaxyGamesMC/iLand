<?php

namespace davidglitch04\iLand\Form;

use davidglitch04\iLand\iLand;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;

class CreateLandForm {

	public function __construct(Player $player){
		$this->createLand($player);
	}

	public function createLand(Player $player){
		$form = new SimpleForm(function(Player $player, $data){
			if(!isset($data)){
				return true;
			}
			switch($)
		});
		$form->setTitle("iLand");
		$form->addButton("Buy Island");
		$form->addButton("Start Position");
		$form->addButton("End Position");
		$player->sendForm($form);
	}
}