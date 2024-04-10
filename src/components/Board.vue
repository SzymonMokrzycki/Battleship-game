<script setup>
import { ref, watch } from 'vue'
import { storeToRefs } from "pinia";
import { useAuthStore } from "../stores/useAuth";
import { useWorldStore } from '../stores/useWorld';
import useEventsBus from "@/composables/eventBus";
import GameDataService from '../services/GameDataService';
import { useFleetStore } from "../stores/useFleet";

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const fleetStore = useFleetStore();
const { currentFleet } = storeToRefs(fleetStore);
const { bus, emit } = useEventsBus();

let recipientDestroyed = 0, senderDestroyed = 0;

async function exit(){
    emit('exit');
    stopInterval()
    stopTimer()

    if(gameData.value.recipient_id == userData.value.user_id){
        gameData.value.ships_recipient.forEach((ship) => {
            if(ship.hp == 0){
                recipientDestroyed++;
            }
        });
    }
        
    if(gameData.value.sender_id == userData.value.user_id){
        gameData.value.ships_sender.forEach((ship) => {
            if(ship.hp == 0){
                senderDestroyed++;
            }
        });
    }

    console.log("Recipient destroyed:"+recipientDestroyed);
    console.log("Sender destroyed:"+senderDestroyed);

    if(gameData.value.recipient_id == userData.value.user_id){
            recipientGameData.user_id = gameData.value.recipient_id;
            recipientGameData.fleet_id = useFleetStore().id;

            recipientGameData.result = gameData.value.result;
            console.log("Historia zapisana - recipient");

            worldData.value.exp += recipientGameData.experience;
            worldData.value.battlescore += recipientGameData.battle_points;

            GameDataService.saveHistory(recipientGameData);
            
        }

        if(gameData.value.sender_id == userData.value.user_id){
            senderGameData.user_id = gameData.value.sender_id;
            senderGameData.fleet_id = useFleetStore().id;

            senderGameData.result = gameData.value.result;
            console.log("Historia zapisana - sender");
            
            worldData.value.exp += senderGameData.experience;
            worldData.value.battlescore += senderGameData.battle_points;
            
            GameDataService.saveHistory(senderGameData);
            
        }

    if(recipientDestroyed > senderDestroyed){
        GameDataService.gameOver(userData.value.current_query_id, {
            result: gameData.value.sender_id
        })
    }else if(recipientDestroyed < senderDestroyed){
        GameDataService.gameOver(userData.value.current_query_id, {
            result: gameData.value.recipient_id
        })
    }else if(recipientDestroyed == senderDestroyed){
        GameDataService.gameOver(userData.value.current_query_id, {
            result: 0
        })
    }
}

const setWaitingText = ref(false);
const moving = ref(false)

const gameData = ref([])
const opponentName = ref("")

const ships = ref([])
const shipsHealths = ref([])
const shipsHealthsOpponent = ref([])

let recipientGameData = {
    number_lost_ships: 0,
    result: 0,
    experience: 0,
    battle_points: 0,
    user_id: 0,
    fleet_id: 0
}

let senderGameData = {
    number_lost_ships: 0,
    result: 0,
    experience: 0,
    battle_points: 0,
    user_id: 0,
    fleet_id: 0
}

let counter = 15;

async function getGameData(id){
  await GameDataService.getGameData(id)
  .then((res) => {
    gameData.value = res.data.data;

    /*if(gameData.value.sender_id == userData.value.user_id){
        intervalTime = 2000;
    }

    if(gameData.value.recipient_id == userData.value.user_id){
        intervalTime = 3000;
    }*/

    if(gameData.value.recipient_id == userData.value.user_id){
        ships.value = gameData.value.ships_recipient;
        shipsHealths.value = gameData.value.ships_recipient;
        shipsHealthsOpponent.value = gameData.value.ships_sender;
    }

    if(gameData.value.sender_id == userData.value.user_id){
        ships.value = gameData.value.ships_sender;
        shipsHealths.value = gameData.value.ships_sender;
        shipsHealthsOpponent.value = gameData.value.ships_recipient;
    }

    if(gameData.value.recipient_id == userData.value.user_id){
        setWaitingText.value = false
    }else if(gameData.value.sender_id == userData.value.user_id){
        setWaitingText.value = true
    }

    if(gameData.value.recipient_positions != undefined){
        if(Object.keys(gameData.value.recipient_positions).length != 0 && 
        gameData.value.recipient_id == userData.value.user_id){
            setWaitingText.value = true
        }
    }

    if(gameData.value.sender_positions != undefined){
        if(Object.keys(gameData.value.sender_positions).length != 0 && 
        gameData.value.recipient_id == userData.value.user_id){
            setWaitingText.value = false
        }

        if(Object.keys(gameData.value.sender_positions).length != 0 && 
        gameData.value.sender_id == userData.value.user_id){
            setWaitingText.value = false 
        }
        
    }

    if(gameData.value.recipient_positions != undefined){
        if(Object.keys(gameData.value.recipient_positions).length != 0 && 
        gameData.value.sender_id == userData.value.user_id && check){
            setWaitingText.value = false
            emit('showPlacingModal');
            GameDataService.updateGameStatus(userData.value.current_query_id);
            check = false
        }
    }

    if(gameData.value.current_moving == userData.value.user_id){
        setWaitingMoving.value = false;
    }else{
        counter = 15
        setWaitingMoving.value = true;
    }

    if(!moving.value){
        displayShips()
    }

    if(gameData.value.game_status == 'game over'){
        if(gameData.value.recipient_id == userData.value.user_id){
            recipientGameData.user_id = gameData.value.recipient_id;
            recipientGameData.fleet_id = useFleetStore().id;

            recipientGameData.result = gameData.value.result;
            console.log("Historia zapisana - recipient");

            worldData.value.exp += recipientGameData.experience;
            worldData.value.battlescore += recipientGameData.battle_points;

            GameDataService.saveHistory(recipientGameData);
            stopInterval()
        }

        if(gameData.value.sender_id == userData.value.user_id){
            senderGameData.user_id = gameData.value.sender_id;
            senderGameData.fleet_id = useFleetStore().id;

            senderGameData.result = gameData.value.result;
            console.log("Historia zapisana - sender");
            
            worldData.value.exp += senderGameData.experience;
            worldData.value.battlescore += senderGameData.battle_points;
            
            GameDataService.saveHistory(senderGameData);
            stopInterval()
        }
    }
    
    console.log(gameData.value)
    //console.log(id)
    //console.log("Moving:"+moving.value)

  })
  .catch(() => {

  })
}

/*const interval = setInterval(() => {
    getGameData(userData.value.current_query_id);
}, 2000)*/

const setWaitingMoving = ref(false)

let timer = setInterval(() => {
    if(Object.keys(gameData.value.sender_positions).length != 0){
        counter--;
        document.getElementById('timeDis').innerHTML = counter+' s'
        if(counter == 0){
            if(!setWaitingMoving.value){
                randomMove()
            }
            saveMove();
            displayShips()
        }
    }
}, 1000)

let check = true

const queryData = ref({})

let switchRound

async function getQuery(query_id){
    await GameDataService.getQuery(query_id)
    .then((res) => {
        queryData.value = res.data.data;

        if(userData.value.user_id == queryData.value.user_id){
            switchRound = setInterval(() => {
                getGameData(userData.value.current_query_id);
            }, 1000)
        }else if(userData.value.user_id == queryData.value.sender_id){
            switchRound = setInterval(() => {
                getGameData(userData.value.current_query_id);
            }, 1500)
        }
    })
    .catch(() => {

    })
}

getQuery(userData.value.current_query_id);

async function stopInterval(){
    clearInterval(switchRound);
}

async function stopTimer(){
    clearInterval(timer);
}

let healthBars;

let procentHealth = 0

async function displayShips(){
    recipientGameData.number_lost_ships = 0;
    senderGameData.number_lost_ships = 0;

    let arrayFields = document.querySelectorAll('.boardField');

    arrayFields.forEach((field) => {
        field.style.borderColor = "white";
    })
    
    if(gameData.value.curent_moving != userData.value.user_id
    && setWaitingMoving.value == false
    && gameData.value.last_move != '-')
        document.getElementById(gameData.value.last_move).style.borderColor = "red";
    
    if(Object.keys(gameData.value.recipient_positions).length != 0 &&
    Object.keys(gameData.value.sender_positions).length != 0){
        if(gameData.value.recipient_id == userData.value.user_id){

            arrayFields.forEach(field => {
                let i = 0;
                Object.keys(gameData.value.recipient_positions).forEach((key) => {
                    //console.log(key, gameData.value.recipient_positions[key]);
                        if(field.attributes['id'].value == gameData.value.recipient_positions[key]){
                            //console.log(gameData.value.ships_recipient[i])
                            if(gameData.value.ships_recipient[i].hp != 0){
                                field.innerHTML = '<img src="/ships/'+gameData.value.ships_recipient[i].type+'.png" id="'+key+'"/>'; 
                            }
                        } 
                    i++;
                });
            });

            
        }
        
        if(gameData.value.sender_id == userData.value.user_id){

            arrayFields.forEach(field => {
                let i = 0;
                Object.keys(gameData.value.sender_positions).forEach((key) => {
                    //console.log(key, gameData.value.sender_positions[key]);
                        if(field.attributes['id'].value == gameData.value.sender_positions[key]){
                            //console.log(gameData.value.ships_sender[i])
                            if(gameData.value.ships_sender[i].hp != 0){
                                field.innerHTML = '<img src="/ships/'+gameData.value.ships_sender[i].type+'.png" id="'+key+'"/>';
                            }
                        } 
                    i++;
                });
            });
        }
    }

    if(gameData.value.recipient_id == userData.value.user_id){
        opponentName.value = gameData.value.sender_name;
    }

    if(gameData.value.sender_id == userData.value.user_id){
        opponentName.value = gameData.value.recipient_name;
    }

    healthBars = document.querySelectorAll('.healthpoints');

    for(let i = 0; i < healthBars.length; i++){
        for (let j = 0; j < ships.value.length; j++) {
            
            if(healthBars[i].attributes['id'].value == ships.value[j].id){

                if(gameData.value.recipient_id == userData.value.user_id){
                    procentHealth = Math.floor(ships.value[i].hp * 100 / gameData.value.ships_recipient_hp[healthBars[i].attributes['id'].value]);
                    //console.log('Wartośc procentowa: '+procentHealth)
                    //console.log("Ships HP: " + gameData.value.ships_recipient_hp[healthBars[i].attributes['id'].value]);
                    //console.log("Ships HP2: " + ships.value[i].hp);
                }

                if(gameData.value.sender_id == userData.value.user_id){
                    procentHealth = Math.floor(ships.value[i].hp * 100 / gameData.value.ships_sender_hp[healthBars[i].attributes['id'].value]);
                    //console.log('Wartośc procentowa: '+procentHealth)
                    //console.log("Ships HP: " + gameData.value.ships_sender_hp[healthBars[i].attributes['id'].value]);
                    //console.log("Ships HP2: " + ships.value[i].hp);
                }

                healthBars[i].style.width = procentHealth+'%';
                
            }else{
                //console.log('Wartośc procentowa: '+procentHealth)
            }
        }
    }    
        
    //moving.value = false
}

let healthBarsOpponent;

async function clearBoard(){
    let arrayFields = document.querySelectorAll('.boardField');
    arrayFields.forEach(field => {
        if(field.children.length > 0){
            field.innerHTML = ''
        }
    }); 

    moving.value = true

    document.querySelector('#textChoose').innerHTML = "Choose your ship:";

    //startTimer();
}

const selectedShip = ref({})

let selectedId = ""
let updatedHp = 0;

async function clickField(field){
    let check = false
    if(Object.keys(selectedShip.value).length != 0){
        if(moving.value){
            //console.log("kliknięto")
            if(gameData.value.recipient_id == userData.value.user_id){
                Object.keys(gameData.value.sender_positions).forEach((key) => {
                    let shipId = 0;
                    console.log(key, gameData.value.sender_positions[key]);
                    console.log("ID i pozycja: " + field.target.attributes['id'].value + " - " + gameData.value.sender_positions[key])
                    
                    if(field.target.attributes['id'].value === gameData.value.sender_positions[key]){
                        check = true
                        shipId = key;
                    }
                    
                    if(check){
                        for(let i = 0; i < gameData.value.ships_sender.length; i++){
                            if(gameData.value.ships_sender[i].id == shipId){
                                gameData.value.ships_sender[i].hp = gameData.value.ships_sender[i].hp - selectedShip.value.damage_canons;
                                
                                if(gameData.value.ships_sender[i].hp < 0){
                                    gameData.value.ships_sender[i].hp = 0;
                                }
                                
                                console.log(gameData.value.ships_sender[i].hp)
                                selectedId = field.target.attributes['id'].value;

                                if(gameData.value.ships_sender[i].hp == 0){
                                    if(gameData.value.ships_sender[i].damage_canons >= 1 && gameData.value.ships_sender[i].damage_canons <= 100){
                                        recipientGameData.experience += 5;
                                        recipientGameData.battle_points += 200;
                                    } 

                                    if(gameData.value.ships_sender[i].damage_canons >= 101 && gameData.value.ships_sender[i].damage_canons <= 1000){
                                        recipientGameData.experience += 10;
                                        recipientGameData.battle_points += 500;
                                    } 

                                    if(gameData.value.ships_sender[i].damage_canons >= 1001 && gameData.value.ships_sender[i].damage_canons <= 5000){
                                        recipientGameData.experience += 15;
                                        recipientGameData.battle_points += 1000;
                                    } 

                                    let txt = "Ship sunk.";

                                    emit('showInfoModal', txt);

                                    senderGameData.number_lost_ships++;
                                }

                                saveMove()
                            }
                        }

                        field.target.innerHTML = 'X';

                        let txt = "Ship hit.";

                        emit('showInfoModal', txt);

                        healthBarsOpponent = document.querySelectorAll('.healthpointsopponent');

                        for(let i = 0; i < healthBarsOpponent.length; i++){
                            for (let j = 0; j < shipsHealthsOpponent.value.length; j++) {
                                if(healthBarsOpponent[i].attributes['id'].value == shipsHealthsOpponent.value[j].id){
                                    
                                    if(gameData.value.recipient_id == userData.value.user_id){
                                        //console.log("GameData wartość: " + gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                        //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString()); 
                                        procentHealth = Math.floor(shipsHealthsOpponent.value[j].hp * 100 / gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                    }

                                    if(gameData.value.sender_id == userData.value.user_id){
                                        //console.log("GameData wartość: " + gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                        //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());  
                                        procentHealth = Math.floor(shipsHealthsOpponent.value[j].hp * 100 / gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                    }

                                    healthBarsOpponent[i].style.width = procentHealth+'%';
                                }
                            }
                        }
                    }else{
                        field.target.innerHTML = 'O';

                        selectedId = field.target.attributes['id'].value;

                        let txt = "You missed.";

                        emit('showInfoModal', txt);

                        saveMove();
                    }
                });
            }

            if(gameData.value.sender_id == userData.value.user_id){
                rounds.value++;
                Object.keys(gameData.value.recipient_positions).forEach((key) => {
                    let shipId = 0;
                    console.log(key, gameData.value.recipient_positions[key]);
                    if(field.target.attributes['id'].value === gameData.value.recipient_positions[key]){
                        check = true
                        shipId = key;
                    }
                    
                    if(check){
                        for(let i = 0; i < gameData.value.ships_recipient.length; i++){
                            if(gameData.value.ships_recipient[i].id == shipId){
                                gameData.value.ships_recipient[i].hp = gameData.value.ships_recipient[i].hp - selectedShip.value.damage_canons;
                                
                                if(gameData.value.ships_recipient[i].hp < 0){
                                    gameData.value.ships_recipient[i].hp = 0;
                                }

                                updatedHp = gameData.value.ships_recipient[i].hp;
                                //console.log("HP PO TRAFIENIU: " + updatedHp);
                                //console.log(gameData.value.ships_recipient[i].hp)
                                selectedId = field.target.attributes['id'].value;


                                if(gameData.value.ships_recipient[i].hp == 0){
                                    if(gameData.value.ships_recipient[i].damage_canons >= 1 && gameData.value.ships_recipient[i].damage_canons <= 100){
                                        senderGameData.experience += 5;
                                        senderGameData.battle_points += 200;
                                    } 

                                    if(gameData.value.ships_recipient[i].damage_canons >= 101 && gameData.value.ships_recipient[i].damage_canons <= 1000){
                                        senderGameData.experience += 10;
                                        senderGameData.battle_points += 500;
                                    } 

                                    if(gameData.value.ships_recipient[i].damage_canons >= 1001 && gameData.value.ships_recipient[i].damage_canons <= 5000){
                                        senderGameData.experience += 15;
                                        senderGameData.battle_points += 1000;
                                    } 

                                    let txt = "Ship sunk.";

                                    emit('showInfoModal', txt);

                                    recipientGameData.number_lost_ships++;
                                }

                                saveMove()
                            }
                        }
                        field.target.innerHTML = 'X';

                        let txt = "Ship hit.";

                        emit('showInfoModal', txt);

                        healthBarsOpponent = document.querySelectorAll('.healthpointsopponent');

                        for(let i = 0; i < healthBarsOpponent.length; i++){
                            for (let j = 0; j < shipsHealthsOpponent.value.length; j++) {
                                if(healthBarsOpponent[i].attributes['id'].value == shipsHealthsOpponent.value[j].id){
                                    
                                    if(gameData.value.recipient_id == userData.value.user_id){
                                        //console.log("GameData wartość: " + gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                        //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());
                                        procentHealth = Math.floor(shipsHealthsOpponent.value[j].hp * 100 / gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                    }

                                    if(gameData.value.sender_id == userData.value.user_id){
                                        //console.log("GameData wartość: " + gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                        //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());
                                        procentHealth = Math.floor(shipsHealthsOpponent.value[j].hp * 100 / gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                    }

                                    healthBarsOpponent[i].style.width = procentHealth+'%';
                                }
                            }
                        }
                    }else{
                        field.target.innerHTML = 'O';

                        selectedId = field.target.attributes['id'].value;

                        let txt = "You missed.";

                        emit('showInfoModal', txt);

                        saveMove();
                    }
                });
            }
            
        }

        console.log("setWaitingMoving: "+setWaitingMoving.value)

        moving.value = false

        selectedShip.value = {}
    }

    for(let i = 0; i < healthBars.length; i++){
        for (let j = 0; j < ships.value.length; j++) {
            
            if(healthBars[i].attributes['id'].value == ships.value[j].id){

                if(gameData.value.recipient_id == userData.value.user_id){
                    procentHealth = Math.floor(ships.value[i].hp * 100 / gameData.value.ships_recipient_hp[healthBars[i].attributes['id'].value]);
                    //console.log('Wartośc procentowa: '+procentHealth)
                    //console.log("Ships HP: " + gameData.value.ships_sender_hp[healthBars[i].attributes['id'].value]);
                    //console.log("Ships HP2: " + ships.value[i].hp);
                    //console.log("GameData wartość: " + gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                    //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());
                }

                if(gameData.value.sender_id == userData.value.user_id){
                    procentHealth = Math.floor(ships.value[i].hp * 100 / gameData.value.ships_sender_hp[healthBars[i].attributes['id'].value]);
                    //console.log('Wartośc procentowa: '+procentHealth)
                    //console.log("Ships HP: " + gameData.value.ships_sender_hp[healthBars[i].attributes['id'].value]);
                    //console.log("Ships HP2: " + ships.value[i].hp);
                    //console.log("GameData wartość: " + gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                    //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());
                }

                healthBars[i].style.width = procentHealth+'%';
                
            }else{
                //console.log('Wartośc procentowa: '+procentHealth)
            }
        }
    }  

}

async function randomMove(){
    
    let arrayFields = document.querySelectorAll('.boardField');
    let listShips = ships.value
    
    let randomNumberField = Math.round(Math.random() * (arrayFields.length - 1));
    let randomNumberShip = Math.round(Math.random() * (listShips.length - 1));

    let randomField = arrayFields[randomNumberField];

    selectedShip.value = listShips[randomNumberShip];
    
    console.log(arrayFields[randomNumberField])

    let check = false

    if(selectedShip.value != null){
        //if(moving.value){
            if(gameData.value.recipient_id == userData.value.user_id){
                Object.keys(gameData.value.sender_positions).forEach((key) => {
                    let shipId = 0;
                    console.log(key, gameData.value.sender_positions[key]);
                    console.log("ID i pozycja: " + randomField.attributes['id'].value + " - " + gameData.value.sender_positions[key])
                    if(randomField.attributes['id'].value === gameData.value.sender_positions[key]){
                        check = true
                        shipId = key;
                    }
                    
                    if(check){
                        for(let i = 0; i < gameData.value.ships_sender.length; i++){
                            if(gameData.value.ships_sender[i].id == shipId){
                                gameData.value.ships_sender[i].hp = gameData.value.ships_sender[i].hp - selectedShip.value.damage_canons;
                                
                                if(gameData.value.ships_sender[i].hp < 0){
                                    gameData.value.ships_sender[i].hp = 0;
                                }
                                
                                if(gameData.value.ships_sender[i].hp == 0){
                                    if(gameData.value.ships_sender[i].damage_canons >= 1 && gameData.value.ships_sender[i].damage_canons <= 100){
                                        recipientGameData.experience += 5;
                                        recipientGameData.battle_points += 200;
                                    } 

                                    if(gameData.value.ships_sender[i].damage_canons >= 101 && gameData.value.ships_sender[i].damage_canons <= 1000){
                                        recipientGameData.experience += 10;
                                        recipientGameData.battle_points += 500;
                                    } 

                                    if(gameData.value.ships_sender[i].damage_canons >= 1001 && gameData.value.ships_sender[i].damage_canons <= 5000){
                                        recipientGameData.experience += 15;
                                        recipientGameData.battle_points += 1000;
                                    } 

                                    let txt = "Ship sunk.";

                                    emit('showInfoModal', txt);

                                    senderGameData.number_lost_ships++;
                                }
                                
                                console.log(gameData.value.ships_sender[i].hp)
                                selectedId = randomField.attributes['id'].value;
                            }
                        }
                        randomField.innerHTML = 'X';

                        let txt = "Ship hit.";

                        emit('showInfoModal', txt);

                        healthBarsOpponent = document.querySelectorAll('.healthpointsopponent');

                        for(let i = 0; i < healthBarsOpponent.length; i++){
                            for (let j = 0; j < shipsHealthsOpponent.value.length; j++) {
                                if(healthBarsOpponent[i].attributes['id'].value == shipsHealthsOpponent.value[j].id){
                                    
                                    if(gameData.value.recipient_id == userData.value.user_id){
                                        //console.log("GameData wartość: " + gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                        //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString()); 
                                        procentHealth = Math.floor(shipsHealthsOpponent.value[j].hp * 100 / gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                    }

                                    if(gameData.value.sender_id == userData.value.user_id){
                                        //console.log("GameData wartość: " + gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                        //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());  
                                        procentHealth = Math.floor(shipsHealthsOpponent.value[j].hp * 100 / gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                    }

                                    healthBarsOpponent[i].style.width = procentHealth+'%';
                                }
                            }
                        }
                    }else{
                        selectedId = randomField.attributes['id'].value;
                        randomField.innerHTML = 'O';

                        let txt = "You missed.";

                        emit('showInfoModal', txt);
                    }
                });
            }

            if(gameData.value.sender_id == userData.value.user_id){
                rounds.value++;
                Object.keys(gameData.value.recipient_positions).forEach((key) => {
                    let shipId = 0;
                    console.log(key, gameData.value.recipient_positions[key]);
                    if(randomField.attributes['id'].value === gameData.value.recipient_positions[key]){
                        check = true
                        shipId = key;
                    }
                    
                    if(check){
                        for(let i = 0; i < gameData.value.ships_recipient.length; i++){
                            if(gameData.value.ships_recipient[i].id == shipId){
                                gameData.value.ships_recipient[i].hp = gameData.value.ships_recipient[i].hp - selectedShip.value.damage_canons;
                                
                                if(gameData.value.ships_recipient[i].hp < 0){
                                    gameData.value.ships_recipient[i].hp = 0;
                                }

                                if(gameData.value.ships_recipient[i].hp == 0){
                                    if(gameData.value.ships_recipient[i].damage_canons >= 1 && gameData.value.ships_sender[i].damage_canons <= 100){
                                        senderGameData.experience += 5;
                                        senderGameData.battle_points += 200;
                                    } 

                                    if(gameData.value.ships_recipient[i].damage_canons >= 101 && gameData.value.ships_sender[i].damage_canons <= 1000){
                                        senderGameData.experience += 10;
                                        senderGameData.battle_points += 500;
                                    } 

                                    if(gameData.value.ships_recipient[i].damage_canons >= 1001 && gameData.value.ships_sender[i].damage_canons <= 5000){
                                        senderGameData.experience += 15;
                                        senderGameData.battle_points += 1000;
                                    } 

                                    let txt = "Ship sunk.";

                                    emit('showInfoModal', txt);

                                    recipientGameData.number_lost_ships++;
                                }
                                
                                updatedHp = gameData.value.ships_recipient[i].hp;
                                //console.log("HP PO TRAFIENIU: " + updatedHp);
                                //console.log(gameData.value.ships_recipient[i].hp)
                                selectedId = randomField.attributes['id'].value;
                            }
                        }
                        randomField.innerHTML = 'X';

                        let txt = "Ship hit.";

                        emit('showInfoModal', txt);

                        healthBarsOpponent = document.querySelectorAll('.healthpointsopponent');

                        for(let i = 0; i < healthBarsOpponent.length; i++){
                            for (let j = 0; j < shipsHealthsOpponent.value.length; j++) {
                                if(healthBarsOpponent[i].attributes['id'].value == shipsHealthsOpponent.value[j].id){
                                    
                                    if(gameData.value.recipient_id == userData.value.user_id){
                                        //console.log("GameData wartość: " + gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                        //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());
                                        procentHealth = Math.floor(shipsHealthsOpponent.value[j].hp * 100 / gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                    }

                                    if(gameData.value.sender_id == userData.value.user_id){
                                        //console.log("GameData wartość: " + gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                        //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());
                                        procentHealth = Math.floor(shipsHealthsOpponent.value[j].hp * 100 / gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                                    }

                                    healthBarsOpponent[i].style.width = procentHealth+'%';
                                }
                            }
                        }
                    }else{
                        selectedId = randomField.attributes['id'].value;

                        randomField.innerHTML = 'O';

                        let txt = "You missed.";

                        emit('showInfoModal', txt);
                    }
                });
            }
            
        //}

        console.log("setWaitingMoving: "+setWaitingMoving.value)

        //moving.value = false

        selectedShip.value = {}
    }

    for(let i = 0; i < healthBars.length; i++){
        for (let j = 0; j < ships.value.length; j++) {
            
            if(healthBars[i].attributes['id'].value == ships.value[j].id){

                if(gameData.value.recipient_id == userData.value.user_id){
                    procentHealth = Math.floor(ships.value[i].hp * 100 / gameData.value.ships_recipient_hp[healthBars[i].attributes['id'].value]);
                    //console.log('Wartośc procentowa: '+procentHealth)
                    //console.log("Ships HP: " + gameData.value.ships_recipient_hp[healthBars[i].attributes['id'].value]);
                    //console.log("Ships HP2: " + ships.value[i].hp);
                    //console.log("GameData wartość: " + gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                    //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());
                }

                if(gameData.value.sender_id == userData.value.user_id){
                    procentHealth = Math.floor(ships.value[i].hp * 100 / gameData.value.ships_sender_hp[healthBars[i].attributes['id'].value]);
                    //console.log('Wartośc procentowa: '+procentHealth)
                    //console.log("Ships HP: " + gameData.value.ships_sender_hp[healthBars[i].attributes['id'].value]);
                    //console.log("Ships HP2: " + ships.value[i].hp);
                    //console.log("GameData wartość: " + gameData.value.ships_recipient_hp[shipsHealthsOpponent.value[j].id.toString()]);
                    //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());
                }

                healthBars[i].style.width = procentHealth+'%';
                
            }else{
                //console.log('Wartośc procentowa: '+procentHealth)
            }
        }
    }  
}

function selectShip(e){
    if(moving.value){

        ships.value.forEach(ship => {
            if(ship.id == e.target.attributes['id'].value){
                selectedShip.value = ship;
            }
        });

        document.querySelectorAll('.ship').forEach((ship) => {
            ship.remove()
        })

        document.querySelector('#textChoose').innerHTML = "";
        console.log(selectedShip.value)
    }
    
}

let rounds = ref(1)

async function saveMove(){
    let dataMoving = {};

    if(gameData.value.recipient_id == userData.value.user_id){
        dataMoving = {
            user_name: gameData.value.sender_name,
            ships: gameData.value.ships_sender,
            current_moving: gameData.value.sender_id,
            last_move: selectedId,
            round: rounds.value
        };
        console.log(dataMoving)
    }

    if(gameData.value.sender_id == userData.value.user_id){
        dataMoving = {
            user_name: gameData.value.recipient_name,
            ships: gameData.value.ships_recipient,
            current_moving: gameData.value.recipient_id,
            last_move: selectedId,
            round: rounds.value
        };
    }

    let arrayFields = document.querySelectorAll('.boardField');
    arrayFields.forEach(field => {
        field.innerHTML = ''
    }); 

    await GameDataService.updateMoving(userData.value.current_query_id, dataMoving)
    .then(() => {

    })
    .catch(() => {

    })

    //window.location.reload(true);

    if(gameData.value.recipient_id == userData.value.user_id){
        gameData.value.ships_recipient.forEach((ship) => {
            if(ship.hp == 0){
                recipientDestroyed++;
            }
        });
    }
        
    if(gameData.value.sender_id == userData.value.user_id){
        gameData.value.ships_sender.forEach((ship) => {
            if(ship.hp == 0){
                senderDestroyed++;
            }
        });
    }

    console.log("Recipient destroyed:"+recipientDestroyed);
    console.log("Sender destroyed:"+senderDestroyed);

    if(recipientDestroyed == gameData.value.ships_recipient.length && 
    senderDestroyed != gameData.value.ships_sender.length){
        console.log("Sender wygrał");
        GameDataService.gameOver(userData.value.current_query_id, {
            result: gameData.value.sender_id
        });

        //GameDataService.saveHistory(userGameData);
        //clearInterval(switchRound);
        finish = true
    }

    if(recipientDestroyed != gameData.value.ships_recipient.length && 
    senderDestroyed == gameData.value.ships_sender.length){
        console.log("Recipient wygrał");
        GameDataService.gameOver(userData.value.current_query_id, {
            result: gameData.value.recipient_id
        });

        //GameDataService.saveHistory(userGameData);
        //clearInterval(switchRound);
        finish = true
    }

    recipientDestroyed = 0;
    senderDestroyed = 0;

    setWaitingMoving.value = true
    moving.value = false
    document.getElementById('textChoose').innerHTML = ''
}

</script>

<template>
    <div v-show="gameData.game_status == 'game over' && gameData.result == userData.user_id" class="text-white items-center grid justify-center bg-gray-600 h-96 text-2xl">
        <span class="text-green-500">Victory!</span>
        <span class="text-lg" v-show="gameData.recipient_id == userData.user_id">Exp: {{ recipientGameData.experience }}</span>
        <span class="text-lg" v-show="gameData.recipient_id == userData.user_id">Battle score: {{ recipientGameData.battle_points }} <font-awesome-icon :icon="['fas', 'medal']" /></span>
        <span class="text-lg" v-show="gameData.recipient_id == userData.user_id">Lost ships: {{ recipientGameData.number_lost_ships }}</span>
        
        <span class="text-lg" v-show="gameData.sender_id == userData.user_id">Exp: {{ senderGameData.experience }}</span>
        <span class="text-lg" v-show="gameData.sender_id == userData.user_id">Battle score: {{ senderGameData.battle_points }} <font-awesome-icon :icon="['fas', 'medal']" /></span>
        <span class="text-lg" v-show="gameData.sender_id == userData.user_id">Lost ships: {{ senderGameData.number_lost_ships }}</span>
    </div>

    <div v-show="gameData.game_status == 'game over' && gameData.result != userData.user_id" class="text-white items-center grid justify-center bg-gray-600 h-96 text-2xl">
        <span class="text-red-500">Lose.</span>
        <span class="text-lg" v-show="gameData.recipient_id == userData.user_id">Exp: {{ recipientGameData.experience }}</span>
        <span class="text-lg" v-show="gameData.recipient_id == userData.user_id">Battle score: {{ recipientGameData.battle_points }} <font-awesome-icon icon="fa-solid fa-star" /></span>
        <span class="text-lg" v-show="gameData.recipient_id == userData.user_id">Lost ships: {{ recipientGameData.number_lost_ships }}</span>
        
        <span class="text-lg" v-show="gameData.sender_id == userData.user_id">Exp: {{ senderGameData.experience }}</span>
        <span class="text-lg" v-show="gameData.sender_id == userData.user_id">Battle score: {{ senderGameData.battle_points }} <font-awesome-icon icon="fa-solid fa-star" /></span>
        <span class="text-lg" v-show="gameData.sender_id == userData.user_id">Lost ships: {{ senderGameData.number_lost_ships }}</span>
    </div>

    <div v-show="setWaitingText == true && gameData.game_status != 'game over'" class="text-white items-center flex justify-center bg-gray-600 h-96 text-2xl">Waiting for opponent...</div>
    <div v-show="setWaitingText != true && gameData.game_status != 'game over'" class="bg-gray-600 block p-2 justify-center">
        <div class="text-white items-center flex justify-center bg-gray-600 text-xl">Round {{ gameData.round }}</div>
        <div v-show="!moving" class="text-white items-center flex justify-center bg-gray-600 text-xl mb-4">Your ships placing</div>
        <div v-show="moving" class="text-white items-center flex justify-center bg-gray-600 text-xl">Opponent: {{ opponentName }}</div>
        
        <div v-show="!moving" v-for="ship in ships" :key="ship.id" class="text-white block space-x-2 justify-center grid">
            <span>{{ ship.name }}</span>
            <div class="flex items-start mb-2">
                <span class="mr-1">{{ Math.round(ship.hp) }}</span> 
                <div class="mt-2 w-40 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-green-600 h-2.5 rounded-full healthpoints" :id="ship.id"></div>
                </div>
            </div>
        </div>

        <div v-show="moving" v-for="ship in shipsHealthsOpponent" :key="ship.id" class="text-white block space-x-2 justify-center grid">
            <span>{{ ship.name }}</span>
            <div class="flex items-start mb-2">
                <span class="mr-1">{{ Math.round(ship.hp) }}</span> 
                <div class="mt-2 w-40 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-rose-600 h-2.5 rounded-full healthpointsopponent" :id="ship.id"></div>
                </div>
            </div>
        </div>
        
        <div class="mt-12 inline-block flex justify-center text-white">
            <div class="w-12 h-8 block text-right">A</div>
            <div class="w-12 h-8 block text-right">B</div>
            <div class="w-12 h-8 block text-right">C</div>
            <div class="w-12 h-8 block text-right">D</div>
            <div class="w-12 h-8 block text-right">E</div>
            <div class="w-12 h-8 block text-right">F</div>
            <div class="w-12 h-8 block text-right">G</div>
            <div class="w-12 h-8 block text-right">H</div>
            <div class="w-12 h-8 block text-right">I</div>
            <div class="w-12 h-8 block text-right">J</div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">1</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J1" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">2</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J2" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">3</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J3" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">4</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J4" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">5</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J5" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">6</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J6" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">7</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J7" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">8</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J8" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">9</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J9" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">10</div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="A10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="B10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="C10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="D10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="E10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="F10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="G10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="H10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="I10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardField text-white" id="J10" @click="clickField"></div>
        </div>

        <div class="text-white mt-4" id="textChoose"></div>
        
        <ul v-if="moving" v-for="ship in ships" :key="ship.id" id="listShips" class="list-disc text-white">
            <li :id="ship.id" @click="selectShip" class="ship hover:bg-orange-500 hover:cursor-pointer"><font-awesome-icon icon="fa-solid fa-sailboat" class="mr-2"/>{{ ship.name }} - damage: {{ ship.damage_canons }}</li>
        </ul>

        <div v-show="setWaitingMoving" class="text-white mt-4">Waiting for opponent...</div>
        
        <button v-show="!setWaitingMoving && !moving" @click="clearBoard" type="button" class="mt-4 text-white bg-orange-600 rounded-lg text-sm p-1.5 inline-flex hover:bg-orange-800 hover:text-white">Make a move</button>
        <div v-show="!setWaitingMoving" class="text-white" id="timeDis"></div>
        <div class="text-white mt-4"><font-awesome-icon :icon="['fas', 'square']" class="text-red-700"/> last move opponent</div>
    </div>
    <button v-show="!setWaitingText" @click="exit" type="button" class="mt-6 text-white">Exit battle</button>
</template>