<?php

	class BracketUtils {

		public static function highestBase($n) {
			$i = 0;
			while (pow(2, $i) < $n) {
				$i++;
			}

			return $i;
		}

		// calculate number of byes for $n players
		public static function calcByes($n) {
			$rounds = BracketUtils::highestBase($n);

			return pow(2, $rounds) - $n;
		}
		
		public static function matchBrackets(Tournament $t, $br, $brackets, $players){
			// Add scores to TournamentPlayers
			// convert players to hashmap
			$tournamentPlayers = array();
			foreach ($t->getPlayers() as $p){
				$tournamentPlayers[$p->getId()] = $p;
			}
			
			foreach ($players as $p){
				$tournamentPlayers[$p['player_id']]->setBracketScore($p['bracket_id'], $p['score']);
			}
			
			// hashmap for rounds too
			$rounds = array();
			foreach ($br as $round){
				$t->addRound($round);
				
				$rounds[$round->getRoundNr()] = $round;
			}
			
			// now make brackets
			$tournamentBrackets = array();
			foreach ($brackets as $b){
				$bObj = new Bracket();
				$bObj->setId($b['id']);
				
				$tournamentBrackets[$b['id']] = $bObj;
			}
			
			// add children and players
			foreach ($brackets as $b){
				if ($b['child_bracket_id'] != 0){
					$tournamentBrackets[$b['id']]->setChild($tournamentBrackets[$b['child_bracket_id']]);
				}
				
				$rounds[$b['round']]->addBracket($tournamentBrackets[$b['id']]);
			}
			
			foreach ($players as $p){
				$tournamentBrackets[$p['bracket_id']]->addPlayer($tournamentPlayers[$p['player_id']]);
			}
		}

	}