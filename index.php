<html>
<head>
	<title>Playing Cards</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<style>
		#player { background-color: lime; }
		#bot { background-color: orange; }
	</style>
	<script>

	
	//The heart of a card, which has a suite and a value
	function card(suite, value) {
		if (suite == "") { this.suite = "Hearts" } 
		else { this.suite = suite; }
		
		if (value == "") { this.value = "1" } 
		else { this.value = value; }
		
		this.filename = "cards/" + value + "_of_" + suite + ".png";
		this.rank = 0;
	}
	
	//Creates a orderly (deck) of cards
	function createDeck(array) {
		for (var i = 0; i < 4; i++) {
			var suit = "";
			switch(i) {
				case 0: 
					suit = "Spades";
					break;
				case 1: 
					suit = "Hearts";
					break;
				case 2:
					suit = "Clubs";
					break;
				case 3:
					suit = "Diamonds";
					break;
				default:
					suit = "Error";
					break;
			}
			var rank = 0;
			for (var j = 1; j < 14; j++) {
				var val = "";
				switch(j) {
					case 1:
						val = "Ace";
						rank = 14;
						break;
					case 11:
						val = "Jack";
						rank = 11;
						break;
					case 12:
						val = "Queen";
						rank = 12;
						break;
					case 13: 
						val = "King";
						rank = 13;
						break;
					default:
						val = j;
						rank = j;
						break;
				}
				
				var addCard = new card(suit, val);
				addCard.rank = rank;
				array.push(addCard);
			}	
		}
		
		return array;
	}
	
	//shuffles the (deck) of cards
	function shuffle(array) {
		var m = array.length, t, i;

		// While there remain elements to shuffle…
		while (m) {

			// Pick a remaining element…
			i = Math.floor(Math.random() * m--);

			// And swap it with the current element.
			t = array[m];
			array[m] = array[i];
			array[i] = t;
		}
		return array;
	}
	
	//Gets a specific (number) of cards from a (deck)
	function drawCards(number, deck) {
		var array = [];
		for (var i = 0; i < number; i++) {
			array.push(deck[0]);
			//remove the elements you got
			deck.shift();
		}
		return array;
	}
	
	//Returns a string from a (card)
	function decodeCard(card) {
		var string = "";
		string = card.value + " of " + card.suite;
		return string;
	}
	
	//Returns a long string from a (deck)
	function decodeDeck(deck) {
		var longText = "";
		for (var i = 0; i < deck.length; i++) {
			longText = longText + decodeCard(deck[i]) + "<br>";
		}
		
		return longText;
	}
	
	//Returns (your hand) from the (deck) with a (number) of cards
	function addToHand(yourHand, theDeck, number) {
		for (var i = 0; i < number; i++) {
			yourHand.push(theDeck[0]);
			//console.log(decodeCard(theDeck[0]));
			theDeck.shift();
		}
		
		updatePoker();
		return yourHand;
	}
	
	function checkInDeck(deck, card) {
		for (var i = 0; i < deck.length; i++) {
			if (card.value == deck[i].value) {
				if (card.suite == deck[i].suite) {
					return true;
				}
			}
		}
		return false;
	}
	
	//check to see if there is a suite in the deck equal to the card's
	function checkSuite(deck, card) {
		for (var i = 0; i < deck.length; i++) {
			if (card.suite == deck[i].suite) {
				return true;
			}
		}
		return false;
	}
	
	//check to see if there is a value in the deck equal to the card's
	function checkValue(deck, card) {
		for (var i = 0; i < deck.length; i++) {
			if (card.value == deck[i].value) {
				return true;
			}
		}
		return false;
	}
	
	//compares two cards and returns true if they are equal to each other
	function compareCards(card1, card2) {
		if (card1.value == card2.value) {
			if (card1.suite == card2.suite) {
				return true;
			}
		}
		return false;
	}
	
	function botBehavior(deck, dealer) {
		
		
		var pairTrip = 0;
		var flush = 0;
		for (var i = 0; i < deck.length; i++) {
			
			for (var j = 0; j < dealer.length; j++) {
				//Check for pairs and triples
				if (deck[i].value == dealer[j].value) {
					pairTrip++;
				}
				
				//Checks own hand
				if (deck[i].value == deck[i + 1]) {
					pairTrip++;
				}
				
				//Check for flushes 
				if (deck[i].suite == dealer[j].suite) {
					flush += 2; //increment by two -- flushes are worth more than pairs or triples
				}
			}
		}
	
		if (pairTrip == 1) { console.log("BOT: I have a pair!"); }
		else if (pairTrip == 2) { console.log("BOT: I have a triple or two pairs!"); }
		if (flush >= 5) { console.log("BOT: I might have a flush!"); }
		var randomFactor = Math.floor((Math.random() * 3) - 1); //number from -1 to 1
		
		var confidence = randomFactor + pairTrip + flush;
		console.log("Bot confidence: " + confidence);
		return confidence;
	}
	
	function printOutCards(div, deck) {
		var toPrint = "";
		for (var i = 0; i < deck.length; i++) {
			toPrint = toPrint + '<img src="' + deck[i].filename + '" width="100px">';
		}
		
		$(div).html(toPrint);
	}
	
	function updatePoker() {
		printOutCards(player, humanDeck);
		//printOutCards(bot, botDeck);
		printOutCards(stream, streamDeck);
		
		//console.log("Human: " + checkWinningCondition(humanDeck, streamDeck));
		//console.log("Bot: " + checkWinningCondition(botDeck, streamDeck));
	}
	
	//condition keys for poker
	//0 : nothing
	//1 : 1 pair
	//2 : 2 pair
	//3 : 3 of a kind
	//4 : straight
	//5 : flush
	//6 : full house
	//7 : 4 of a kind
	//8 : straigh flush
	//9 : royal flush
	function checkWinningCondition(yourDeck, dealer) {
		var condition = 0;
		
		var testDeck = yourDeck.concat(dealer);
					var equal1 = 0;
			var equal2 = 0;
		//if hand is equal
		if (yourDeck[0].value == yourDeck[1].value) {
			//
			//console.log("Pocket pairs");
			//
			condition = 1;
			
			//check if there are otheres
			var equal = 0;
			for (var i = 0; i < dealer.length; i++) {
				if (yourDeck[0].value == dealer[i].value) {
					equal++;
				}
			}	
			
			//3 of a kind
			if (equal == 1) {
				condition = 3;
				
				//check to see if there's a full house in the river
				
				//needs work on
			} 
			//4 of a kind
			else if (equal == 2) {
				condition = 7;
			}
		} 
		//if hand is not equal
		else {
			for (var i = 0; i < dealer.length; i++) {
				if (yourDeck[0].value == dealer[i].value) {
					//if found a match, increment by 1
					equal1++;
				}
				if (yourDeck[1].value == dealer[i].value) {
					//if found a match, increment by 1
					equal2++;
				}
			}
			
			//full house
			if ( ((equal1 == 2) && (equal2 == 1)) || ((equal2 == 2) && (equal1 == 1)) ) {
				//If three of a kind plus a pair for each hand (scenario)
				condition = 6;
			}
			//three of a kind
			else if ((equal1 == 2) || (equal2 == 2)) {
				condition = 3;
			} 
			//two pair
			else if ((equal1 == 1) && (equal2 == 1)) {
				condition = 2;
			}
			//normal ole' one pair
			else if ((equal1 == 1) || (equal2 == 1)) {
				condition = 1;
			}
			//
			//console.log("Equals: " + equal1 + " - " + equal2);
			//
		}
		
		//straight
		var straight = false;
		var root = 0;
		for (var i = 0; i < testDeck.length; i++) {
			var five = [0, 0, 0, 0, 0];
			var testCard = testDeck[i];
			root = testCard.rank;
			five[0] = 1;
			for (var j = 0; j < testDeck.length; j++) {
				//check all the cards and see if they are close to the root
				if (testDeck[j].rank == (root + 1)) {
					five[1] = 1;
				} else if (testDeck[j].rank == (root + 2)) {
					five[2] = 1;
				} else if (testDeck[j].rank == (root + 3)) {
					five[3] = 1;
				} else if (testDeck[j].rank == (root + 4)) {
					five[4] = 1;
				}	
			}
			
			//assume that there is a straight
			straight = true;
			for (var z = 0; z < five.length; z++) {
				//if all the five rankings are not satisfied, then set straight as false.
				if (five[z] == 0) {
					straight = false;
				}
			//if it found a solution, stop looking for one!
			} if (straight == true) {
				condition = 4;
				break;
			}
		}		
		
		//
		//console.log("Straight: " + straight + " - " + five);
		//
		
		//flush
		for (var i = 0; i < testDeck.length; i++) {
			var flushc = 0;
			for (var j = i; j < testDeck.length; j++) {
				if (testDeck[i].suite == testDeck[j].suite) {
					flushc++;
				}
			}
			if (flushc >= 5) {
				condition = 5;
				break;
			}
		}	
		
		//
		//console.log("Condition : " + condition);
		//
		return condition;
	} //end of function
	
	//The actual game
	var round = 0;
	function game(round) {
		//The first round of betting
		if (round == -1) {
			$('#advRound').html('<a href="javascript:;" onclick="game(0)">Start the game</a>');
			
		}
		
		if (round == 0) {
			$('#advRound').html('<a href="javascript:;" onclick="game(1)">Advance to Flop (make sure to bet!)</a>');
			//bet("#botBet", 0);
			//bet("#playBet", 0);
			
		} 
		
		//The flop
		if (round == 1) {
			$('#advRound').html('<a href="javascript:;" onclick="game(2)">Advance to Turn (make sure to bet!)</a>');
			addToHand(streamDeck, shuffled, 3);
			bet("#botBet", 0);
			bet("#playBet", 0);
		}
		
		//the river
		if (round == 2) {
			$('#advRound').html('<a href="javascript:;" onclick="game(3)">Advance to River (make sure to bet!)</a>');
			addToHand(streamDeck, shuffled, 1);
			bet("#botBet", 0);
			bet("#playBet", 0);
		}
		
		//the end
		if (round == 3) {
			$('#advRound').html('<a href="javascript:;" onclick="game(4)">Finish round (make sure to bet!)</a>');
			addToHand(streamDeck, shuffled, 1);
			bet("#botBet", 0);
			bet("#playBet", 0);
		}
		
		if (round == 4) {
			console.log("We're done here!");
			console.log("Human: " + checkWinningCondition(humanDeck, streamDeck));
			console.log("Bot: " + checkWinningCondition(botDeck, streamDeck));
			bet("#botBet", 0);
			bet("#playBet", 0);
			
			if (checkWinningCondition(humanDeck, streamDeck) === checkWinningCondition(botDeck, streamDeck)) {
				console.log("TIE");
			} else if (checkWinningCondition(humanDeck, streamDeck) > checkWinningCondition(botDeck, streamDeck)) {
				console.log("Player wins!");
			} else {
				console.log("Bot wins!");
			}
			//Show the bot's hand
			printOutCards(bot, botDeck);
		}
	}
	
	//This automatically updates the betting div
	var playBet = 0;
	var botBet = 0;
	function bet(div, amount) {
		if (div === "#botBet") {
			//so betting is random - generates number from 0 to 1
			var seed = Math.floor((Math.random() * 3));
			
			//The bot will check his hand and if he thinks he will win
			//this is where the magic of bluffing and taking bot risks are:
			var bs = (((checkWinningCondition(botDeck, streamDeck)) * 2)+seed) * 5;
			botBet = botBet + bs;
			$(div).html(botBet + " chips");
		} else {
			var amount = $('#pBet').val();
			playBet = playBet + parseInt(amount);
			$(div).html(playBet + " chips");
		}
	}
	
	var deck = [];
	
	//Poker defaults
	var shuffled = shuffle(createDeck(deck));
	var humanDeck = drawCards(2, shuffled);
	var botDeck = drawCards(2, shuffled);
	var streamDeck = drawCards(0, shuffled);
	
	
	</script>
	
	<style>
		#botThoughts { font-size: 8vw; }
		#playBet, #botBet { background-color: black; color: white; padding: 10px;}
	</style>
</head>
<body>
	<span id="advRound">..</span>
	<div id="player">
	</div>	
	<input type="text" id="pBet">
	<div id="playBet">0 chips</div>
	<hr>
	<div id="bot">
	</div>
	<div id="botBet">0 chips</div>
	<hr>
	<div id="stream">
	</div>
	<script>
	updatePoker();
	checkWinningCondition(humanDeck, streamDeck);
	printOutCards(player, humanDeck);
	//printOutCards(bot, botDeck);
	printOutCards(stream, streamDeck);
	game(-1);
	</script>
</body>
</html>