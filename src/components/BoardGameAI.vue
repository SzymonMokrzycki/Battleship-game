<script setup>
import { ref, watch } from 'vue'
import { storeToRefs } from "pinia";
import { useAuthStore } from "../stores/useAuth";
import { useWorldStore } from '../stores/useWorld';
import useEventsBus from "@/composables/eventBus";
import { useFleetStore } from "../stores/useFleet";
import GameDataService from '../services/GameDataService';

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const fleetStore = useFleetStore();
const { shipsData } = storeToRefs(fleetStore);
const { bus, emit } = useEventsBus();

let aiGameInterval
let timer

let healthBars;

let healthBarsOpponent;

let procentHealth = 0

const setWaitingText = ref(false)

let rounds = ref(1)

let currentPlayer = "player"

let playerPositions = {}, aiPositions = {}

let shipsCurrentAI = [
    {id: 1, name:"Ship 1", damage_canons: 500, hp: 2000},
    {id: 2, name:"Ship 2", damage_canons: 450, hp: 2000},
    {id: 3, name:"Ship 3", damage_canons: 1000, hp: 2000},
    {id: 4, name:"Ship 4", damage_canons: 800, hp: 2000},
    {id: 5, name:"Ship 5", damage_canons: 200, hp: 2000},
    {id: 6, name:"Ship 6", damage_canons: 320, hp: 2000},
    {id: 7, name:"Ship 7", damage_canons: 120, hp: 2000},
    {id: 8, name:"Ship 8", damage_canons: 280, hp: 2000},
    {id: 9, name:"Ship 9", damage_canons: 105, hp: 2000},
    {id: 10, name:"Ship 10", damage_canons: 900, hp: 2000}
]

const shipsCurrentPlayer = ref([])
const shipsPlayerHp = []

let shipsAI = [
    {id: 1, name:"Ship 1", damage_canons: 500, hp: 2000},
    {id: 2, name:"Ship 2", damage_canons: 450, hp: 2000},
    {id: 3, name:"Ship 3", damage_canons: 1000, hp: 2000},
    {id: 4, name:"Ship 4", damage_canons: 800, hp: 2000},
    {id: 5, name:"Ship 5", damage_canons: 200, hp: 2000},
    {id: 6, name:"Ship 6", damage_canons: 320, hp: 2000},
    {id: 7, name:"Ship 7", damage_canons: 120, hp: 2000},
    {id: 8, name:"Ship 8", damage_canons: 280, hp: 2000},
    {id: 9, name:"Ship 9", damage_canons: 105, hp: 2000},
    {id: 10, name:"Ship 10", damage_canons: 900, hp: 2000}
]

let boardFields = document.getElementsByClassName('boardFieldAI');

//console.log(shipsData.value)

const getFleetShips = async (fleet_id) => {
    return await GameDataService.getFleetShips(fleet_id)
    .then((res) => {
        shipsCurrentPlayer.value = res.data.data.ships;
        
        for(let i = 0; i < shipsCurrentPlayer.value.length; i++){
            shipsPlayerHp.push(shipsCurrentPlayer.value[i].hp)
        }
    })
    .catch((error) => {
        console.log(error.response.data);
    });
};

getFleetShips(useFleetStore().id)

if(Object.keys(playerPositions).length == 0){
    emit('showPlacingModalAI');
}

async function checkAiPositions(){
    if(Object.keys(playerPositions).length != 0 && Object.keys(aiPositions).length == 0){
        randomAiPositions()
        console.log(playerPositions);
        console.log(aiPositions);
        startTimer()
    }
}

let counterTime = 15;

async function startTimer(){
    counterTime = 15;
    timer = setInterval(() => {
        counterTime--;
        document.getElementById('timerCount').innerHTML = counterTime+' s'
        if(counterTime == 0){
            stopTimer();
            randomClickField()
            displayShips()
        }
    }, 1000)
}

async function stopTimer(){
    clearInterval(timer);
    currentPlayer = "ai"
}

const selectedShipPlayer = ref({})
const selectedShipAI = ref({})

let selectedIdPlayer = ""

function selectShip(e){
    if(!setWaitingText.value){

        shipsCurrentPlayer.value.forEach((ship) => {
            if(ship.id == e.target.attributes['id'].value){
                selectedShipPlayer.value = ship;
            }
        });

        document.querySelectorAll('.ship').forEach((ship) => {
            ship.remove()
        })

        document.querySelector('#textChoose').innerHTML = "";
        console.log(selectedShipPlayer.value)
    }
}

function hasWon(){
    let playerDestroyed = 0, aiDestroyed = 0;

    shipsCurrentPlayer.value.forEach(ship => {
        if(ship.hp == 0){
            playerDestroyed++;
        }
    });

    shipsCurrentAI.forEach(ship => {
        if(ship.hp == 0){
            aiDestroyed++;
        }
    });

    if(aiDestroyed == shipsCurrentAI.length && shipsCurrentPlayer.value.length != 0){
        return 1;
    }

    if(playerDestroyed == shipsCurrentPlayer.value.length && shipsCurrentPlayer.value.length != 0){
        return 2;
    }

    return 0;
}

let missFields = []
let firstMove = true

let boardFieldsId, evaluations, targets, simulationMoves, score, fieldsToMove
let nextMoves, bestNextMoves

async function aiMove(){
    if(setWaitingText.value){
        let randomShipNumber = Math.round(Math.random() * shipsCurrentAI.length)

        //console.log("Random ship ai: "+ randomShipNumber)

        selectedShipAI.value = shipsCurrentAI[randomShipNumber]

        console.log(selectedShipAI.value)

        boardFieldsId = []

        evaluations = {}

        targets = {}

        for(let k = 0; k < boardFields.length; k++){
            //console.log(boardFields[k])
            boardFieldsId.push(boardFields[k].attributes['id'].value);
            //console.log(boardFieldsId[k])
        }

        let check = false
        let randomField, randomNumber 
        
        if(firstMove){
            randomNumber = Math.round(Math.random() * boardFields.length);

            randomField = boardFields[randomNumber].attributes['id'].value;

            firstMove = false

            boardFields[randomNumber].style.borderColor = "red";
        }else{
            simulationMoves = {} 
            score = 100
            
            if(missFields.length != 0){
                fieldsToMove = boardFieldsId.filter(function(el) {
                    return !missFields.includes(el);
                });
                
                //console.log("Ships player: "+currentFleet.shipsData)

                for(let t = 0; t < shipsCurrentPlayer.value.length; t++){
                    Object.keys(playerPositions).forEach(position => {
                        if(position == shipsCurrentPlayer.value[t].id){
                            for(let r = 0; r < fieldsToMove.length; r++){
                                targets[playerPositions[position]] = shipsCurrentPlayer.value[t];
                            }
                        }
                    })
                } 
                
                let shipHp, countMoves

                Object.keys(targets).forEach(target => {
                    
                    countMoves = 0

                    shipHp = targets[target].hp

                    while(shipHp > 0){
                        shipHp = shipHp - selectedShipAI.value.damage_canons;
                        countMoves++;
                    }

                    console.log(targets[target].hp)
                    
                    simulationMoves[target] = countMoves

                    for(let i = 0; i < countMoves; i++){
                        score--;
                    }
                    
                    evaluations[target] = score

                    score = 100
                    
                });

                nextMoves = []

                Object.keys(evaluations).forEach(evaluation => {
                    if(evaluations[evaluation] < 100){
                        nextMoves.push(evaluations[evaluation])
                    }
                })

                let bestMove = Math.max(...nextMoves)

                bestNextMoves = []

                Object.keys(evaluations).forEach(evaluation => {
                    if(evaluations[evaluation] == bestMove){
                        bestMove = evaluation
                        bestNextMoves.push(evaluation)
                    }
                });

                if(bestNextMoves.length > 1){
                    let randomTarget = Math.round(Math.random() * bestNextMoves.length);
                    randomField = bestNextMoves[randomTarget]
                }else{
                    randomField = bestMove
                }

                console.log("Najlepszy ruch: "+randomField)
                console.log("Board fields id: "+boardFieldsId)
                console.log("Miss fields: "+missFields)
                console.log("Fields to move: ")
                console.log(fieldsToMove)
                console.log("Targets: ")
                console.log(targets)
                console.log("Simulations: ")
                console.log(simulationMoves)
                console.log("Evaluations: ")
                console.log(evaluations)
                console.log("Next moves: ")
                console.log(nextMoves)
                console.log("Best next moves: ")
                console.log(bestNextMoves)
            }
        }

        let aiNextMove

        for(let i = 0; i < boardFields.length; i++){
            if(boardFields[i].attributes['id'].value == randomField){
                aiNextMove = boardFields[i]
            }
        }

        //console.log(shipsData.value)

        Object.keys(playerPositions).forEach((key) => {
            let shipId = 0;
            
            if(aiNextMove.attributes['id'].value === playerPositions[key]){
                check = true
                shipId = key;
            }
            
            if(check){
                for(let i = 0; i < shipsCurrentPlayer.value.length; i++){
                    if(shipsCurrentPlayer.value[i].id == shipId){
                        shipsCurrentPlayer.value[i].hp = shipsCurrentPlayer.value[i].hp - selectedShipAI.value.damage_canons;
                        
                        if(shipsCurrentPlayer.value[i].hp < 0){
                            shipsCurrentPlayer.value[i].hp = 0;
                            
                            let txt = "Ship sunk.";

                            emit('showInfoModal', txt);
                        }
                        
                        selectedIdPlayer = aiNextMove.attributes['id'].value;

                    }
                }

                for(let i = 0; i < boardFields.length; i++){
                    boardFields[i].style.borderColor = "white";
                }

                aiNextMove.style.borderColor = "red";
                
                aiNextMove.innerHTML = 'X';

                let txt = "AI ship hit.";

                emit('showInfoModal', txt);

                //console.log("heheh")
            }else{
                aiNextMove.innerHTML = 'O';

                aiNextMove.style.borderColor = "red";

                selectedIdPlayer = aiNextMove.attributes['id'].value;

                let txt = "AI missed.";

                emit('showInfoModal', txt);

                missFields.push(aiNextMove.attributes['id'].value)

                //let uniqueMissedFields = Array.from(new Set(missFields));

                //console.log(uniqueMissedFields)
            }
        });

        //setWaitingText.value = false
        console.log(setWaitingText.value);
        clearBoard()
    }

    rounds.value++;

    selectedShipAI.value = {}

    targets = {}

    bestNextMoves = []

    nextMoves = []

    evaluations = {}

    boardFieldsId = []

    fieldsToMove = []

    score = 100

    currentPlayer = "player"
    
}

async function randomClickField(){

    let randomNumber = Math.round(Math.random() * shipsCurrentPlayer.value.length);
    
    selectedShipPlayer.value = shipsCurrentPlayer.value[randomNumber];

    let randomNumberField = Math.round(Math.random() * (boardFields.length - 1));

    let randomPlayerField = boardFields[randomNumberField]

    if(!setWaitingText.value && Object.keys(selectedShipPlayer.value).length != 0){
        let check = false
        

        if(Object.keys(selectedShipPlayer.value).length != 0){
            if(!setWaitingText.value){

                Object.keys(aiPositions).forEach((key) => {
                    let shipId = 0;
                    if(randomPlayerField.attributes['id'].value === aiPositions[key]){
                        check = true
                        shipId = key;
                    }
                    
                    if(check){
                        for(let i = 0; i < shipsCurrentAI.length; i++){
                            if(shipsCurrentAI[i].id == shipId){
                                shipsCurrentAI[i].hp = shipsCurrentAI[i].hp - selectedShipPlayer.value.damage_canons;
                                
                                if(shipsCurrentAI[i].hp < 0){
                                    shipsCurrentAI[i].hp = 0;
                                }
                                
                                selectedIdPlayer = randomPlayerField.attributes['id'].value;

                                if(shipsCurrentAI[i].hp == 0){ 

                                    let txt = "Ship sunk.";

                                    emit('showInfoModal', txt);
                                }

                                //stopTimer()
                            }
                        }

                        randomPlayerField.innerHTML = 'X';

                        let txt = "Ship hit.";

                        emit('showInfoModal', txt);

                        healthBarsOpponent = document.querySelectorAll('.healthpointsai');

                        for(let i = 0; i < healthBarsOpponent.length; i++){
                            for (let j = 0; j < shipsCurrentAI.length; j++) {
                                if(healthBarsOpponent[i].attributes['id'].value == shipsCurrentAI[j].id){
                                            
                                    procentHealth = Math.floor(shipsCurrentAI[j].hp * 100 / shipsAI[j].hp);
                                
                                    healthBarsOpponent[i].style.width = procentHealth+'%';
                                }
                            }
                        }
                    }else{
                        randomPlayerField.innerHTML = 'O';

                        selectedIdPlayer = randomPlayerField.attributes['id'].value;

                        let txt = "You missed.";

                        emit('showInfoModal', txt);

                        missFields.push(selectedIdPlayer);

                        let uniqueMissedFields = Array.from(new Set(missFields));

                        console.log(uniqueMissedFields)
                        //stopTimer()
                    }
                });
            }

            //console.log("setWaitingMoving: "+setWaitingMoving.value)

            //moving.value = false

            //selectedShip.value = {}
        }

        healthBars = document.querySelectorAll('.healthpointsplayer');

        for(let i = 0; i < healthBars.length; i++){
            for (let j = 0; j < shipsCurrentPlayer.value.length; j++) {
                
                if(healthBars[i].attributes['id'].value == shipsCurrentPlayer.value[j].id){

                    procentHealth = Math.floor(shipsCurrentPlayer.value[j].hp * 100 / shipsPlayerHp[j]);
                    //console.log('Wartośc procentowa: '+procentHealth)
                    //console.log("Ships HP: " + gameData.value.ships_recipient_hp[healthBars[i].attributes['id'].value]);
                    //console.log("Ships HP2: " + ships.value[i].hp);

                    healthBars[i].style.width = procentHealth+'%';
                    
                }else{
                    //console.log('Wartośc procentowa: '+procentHealth)
                }
            }
        } 

        //displayShips()

        //currentPlayer = "ai"

        //setWaitingText.value = true

        document.querySelector('#textChoose').innerHTML = "";
        displayShips()
    }
}

async function clickField(field){
    if(!setWaitingText.value){
        let check = false

        if(Object.keys(selectedShipPlayer.value).length != 0){
            if(!setWaitingText.value){

                Object.keys(aiPositions).forEach((key) => {
                    let shipId = 0;
                    if(field.target.attributes['id'].value === aiPositions[key]){
                        check = true
                        shipId = key;
                    }
                    
                    if(check){
                        for(let i = 0; i < shipsCurrentAI.length; i++){
                            if(shipsCurrentAI[i].id == shipId){
                                shipsCurrentAI[i].hp = shipsCurrentAI[i].hp - selectedShipPlayer.value.damage_canons;
                                
                                if(shipsCurrentAI[i].hp < 0){
                                    shipsCurrentAI[i].hp = 0;
                                }
                                
                                selectedIdPlayer = field.target.attributes['id'].value;

                                if(shipsCurrentAI[i].hp == 0){ 

                                    let txt = "Ship sunk.";

                                    emit('showInfoModal', txt);
                                }

                                stopTimer()
                            }
                        }

                        field.target.innerHTML = 'X';

                        let txt = "Ship hit.";

                        emit('showInfoModal', txt);

                        healthBarsOpponent = document.querySelectorAll('.healthpointsai');

                        for(let i = 0; i < healthBarsOpponent.length; i++){
                            for (let j = 0; j < shipsCurrentAI.length; j++) {
                                if(healthBarsOpponent[i].attributes['id'].value == shipsCurrentAI[j].id){
                                            
                                    procentHealth = Math.floor(shipsCurrentAI[j].hp * 100 / shipsAI[j].hp);
                                
                                    healthBarsOpponent[i].style.width = procentHealth+'%';
                                }
                            }
                        }
                    }else{
                        field.target.innerHTML = 'O';

                        selectedIdPlayer = field.target.attributes['id'].value;

                        let txt = "You missed.";

                        emit('showInfoModal', txt);

                        let notSelfPosition = true

                        Object.keys(playerPositions).forEach(position =>{
                            if(playerPositions[position] == selectedIdPlayer){
                                notSelfPosition = false 
                            }
                        });
                   
                        if(notSelfPosition){
                            //console.log("qweqweqweqwe")
                            missFields.push(selectedIdPlayer);
                        }
                        
                        let uniqueMissedFields = Array.from(new Set(missFields));

                        console.log(uniqueMissedFields)
                        stopTimer()
                    }
                });
            }

            //console.log("setWaitingMoving: "+setWaitingMoving.value)

            //moving.value = false

            //selectedShip.value = {}
        }

        /*healthBars = document.getElementsByClassName('healthpointsplayer');

        for(let i = 0; i < healthBars.length; i++){
            for (let j = 0; j < shipsCurrentPlayer.length; j++) {
                
                if(healthBars[i].attributes['id'].value == shipsCurrentPlayer[j].id){

                    procentHealth = Math.floor(shipsCurrentPlayer[i].hp * 100 / shipsPlayerHp[j]);
                    //console.log('Wartośc procentowa: '+procentHealth)
                    //console.log("Ships HP: " + gameData.value.ships_sender_hp[healthBars[i].attributes['id'].value]);
                    //console.log("Ships HP2: " + ships.value[i].hp);
                    //console.log("GameData wartość: " + gameData.value.ships_sender_hp[shipsHealthsOpponent.value[j].id.toString()]);
                    //console.log("Klucz: " + shipsHealthsOpponent.value[j].id.toString());

                    healthBars[i].style.width = procentHealth+'%';
                    
                }else{
                    //console.log('Wartośc procentowa: '+procentHealth)
                }
            }
        }*/  

        //currentPlayer = "ai"

        //setWaitingText.value = true
        document.querySelector('#textChoose').innerHTML = "";
        displayShips()
    }

    selectedShipPlayer.value = {}
}

async function displayShips(){
    //if(document.getElementById('lastMove') != null)
        //document.getElementById('lastMove').innerHTML = "Last move: "+gameData.value.last_move;
    
    if(Object.keys(playerPositions).length != 0 &&
    Object.keys(aiPositions).length != 0){
        
        for(let i = 0; i < boardFields.length; i++){
            let j = 0;
            Object.keys(playerPositions).forEach((key) => {
                //console.log(key, gameData.value.recipient_positions[key]);
                    if(boardFields[i].attributes['id'].value == playerPositions[key]){
                        //console.log(gameData.value.ships_recipient[i])
                        if(shipsCurrentPlayer.value[j].hp != 0){
                            boardFields[i].innerHTML = '<img src="/ships/'+shipsCurrentPlayer.value[j].type+'.png" id="'+key+'"/>'; 
                        }
                    } 
                j++;
            });
        }
    }

    healthBars = document.querySelectorAll('.healthpointsplayer');

    for(let i = 0; i < healthBars.length; i++){
        for (let j = 0; j < shipsCurrentPlayer.value.length; j++) {
            
            if(healthBars[i].attributes['id'].value == shipsCurrentPlayer.value[j].id){

                procentHealth = Math.floor(shipsCurrentPlayer.value[j].hp * 100 / shipsPlayerHp[j]);
                //console.log('Wartośc procentowa: '+procentHealth)
                //console.log("Ships HP: " + gameData.value.ships_recipient_hp[healthBars[i].attributes['id'].value]);
                //console.log("Ships HP2: " + ships.value[i].hp);

                healthBars[i].style.width = procentHealth+'%';
                
            }else{
                //console.log('Wartośc procentowa: '+procentHealth)
            }
        }
    }   

    //moving.value = false
}

async function clearBoardPlayer(){
    let arrayFields = Array.from(document.getElementsByClassName('boardFieldAI'));
    
    //console.log(arrayFields)

    for (let i = 0; i < arrayFields.length; i++) {
        arrayFields[i].innerHTML = '';
    }
}

async function clearBoard(){
    let arrayFields = Array.from(document.getElementsByClassName('boardFieldAI'));
    
    //console.log(arrayFields)

    for (let i = 0; i < arrayFields.length; i++) {
        arrayFields[i].innerHTML = '';
    }

    //setWaitingText.value = false

    document.querySelector('#textChoose').innerHTML = "Choose your ship:";

    startTimer();
}

async function randomAiPositions(){
    shipsAI.forEach((ship) => {
        let randomNumber = Math.floor(Math.random() * (boardFields.length))
        aiPositions[ship.id] = boardFields[randomNumber].attributes['id'].value
        console.log(boardFields[randomNumber].attributes['id'].value)
    })
    console.log(aiPositions)

    if(Object.keys(playerPositions).length != 0 && Object.keys(aiPositions).length != 0){

        aiGameInterval = setInterval(() => {
            if(hasWon() == 0){
                if(currentPlayer == "player"){
                    setWaitingText.value = false
                    //startTimer()
                    clearBoardPlayer()
                }else if(currentPlayer == "ai"){
                    //stopTimer()
                    healthBars = document.querySelectorAll('.healthpointsplayer');

                    for(let i = 0; i < healthBars.length; i++){
                        for (let j = 0; j < shipsCurrentPlayer.value.length; j++) {
                            
                            if(healthBars[i].attributes['id'].value == shipsCurrentPlayer.value[j].id){

                                procentHealth = Math.floor((shipsCurrentPlayer.value[j].hp / shipsPlayerHp[j]) * 100);
                                //procentHealth = 50
                                console.log('Wartośc procentowa: '+procentHealth)
                                console.log("Ships HP: " + shipsCurrentPlayer.value[j].hp);
                                console.log("Ships HP2: " + shipsPlayerHp[j]);



                                healthBars[i].style.width = procentHealth+'%';
                                
                            }else{
                                //console.log('Wartośc procentowa: '+procentHealth)
                            }
                        }
                    }   

                    setWaitingText.value = true
                    aiMove()
                    displayShips()
                    //displayShips()
                }
            }else{
                stopInterval()
            }
        }, 4000)
            
    }
}

function stopInterval(){
    clearInterval(aiGameInterval)
}

async function exit(){
    userData.value.game_with_ai = false;
    stopInterval()
}

watch(
  () => bus.value.get('savePlayerPositions'),
  (playerPosition) => {
    playerPositions = playerPosition[0];
    checkAiPositions()
  }
);

</script>
<template>  
    <div v-show="hasWon() == 1" class="text-green-500 items-center grid justify-center bg-gray-600 h-96 text-2xl">Victory!</div>
    <div v-show="hasWon() == 2" class="text-red-500 items-center grid justify-center bg-gray-600 h-96 text-2xl">Lose.</div>
    <div v-show="hasWon() == 0" class="bg-gray-600 block p-2 justify-center">
        <div class="text-white items-center flex justify-center bg-gray-600 text-xl">Round {{  rounds }}</div>
        <div v-show="setWaitingText" class="text-white items-center flex justify-center bg-gray-600 text-xl mb-4">Your ships placing</div>
        <div v-show="!setWaitingText" class="text-white items-center flex justify-center bg-gray-600 text-xl">Opponent: AI</div>
        
        <div v-show="setWaitingText" v-for="ship in shipsCurrentPlayer" :key="ship.id" class="text-white block space-x-2 justify-center grid">
            <span>{{ ship.name }}</span>
            <div class="flex items-start mb-2">
                <span class="mr-1">{{ Math.round(ship.hp) }}</span> 
                <div class="mt-2 w-40 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-green-600 h-2.5 rounded-full healthpointsplayer" :id="ship.id"></div>
                </div>
            </div>
        </div>

        <div v-show="!setWaitingText" v-for="ship in shipsCurrentAI" :key="ship.id" class="text-white block space-x-2 justify-center grid">
            <span>{{ ship.name }}</span>
            <div class="flex items-start mb-2">
                <span class="mr-1">{{ Math.round(ship.hp) }}</span> 
                <div class="mt-2 w-40 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                    <div class="bg-rose-600 h-2.5 rounded-full healthpointsai" :id="ship.id"></div>
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
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I1" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J1" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">2</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I2" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J2" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">3</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I3" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J3" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">4</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I4" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J4" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">5</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A5" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J5" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">6</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I6" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J6" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">7</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I7" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J7" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">8</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I8" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J8" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">9</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I9" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J9" @click="clickField"></div>
        </div>
        <div class="inline-block flex justify-center">
            <div class="w-12 h-12 block text-white">10</div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="A10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="B10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="C10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="D10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="E10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="F10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="G10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="H10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="I10" @click="clickField"></div>
            <div class="w-12 h-12 block border-2 border-white boardFieldAI text-white" id="J10" @click="clickField"></div>
        </div>
        <div class="text-white mt-4" id="textChoose">Choose your ship:</div>
        
        <ul v-if="!setWaitingText" v-for="ship in shipsCurrentPlayer" :key="ship.id" id="listShips" class="list-disc text-white">
            <li :id="ship.id" @click="selectShip" class="ship hover:bg-orange-500 hover:cursor-pointer"><font-awesome-icon icon="fa-solid fa-sailboat" class="mr-2"/>{{ ship.name }} - damage: {{ ship.damage_canons }}</li>
        </ul>

        <div v-show="setWaitingText" class="text-white mt-4">Waiting for opponent...</div>
        <div class="text-white inline-flex" id="timerCount"></div>
    </div>
    <button @click="exit" type="button" class="mt-6 text-white">Exit battle</button>
</template>