<script setup>
import { ref, watch } from 'vue'
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'
import useEventsBus from "@/composables/eventBus";
import { storeToRefs } from "pinia";
import { useSkillsStore } from "../../stores/useSkills";
import WorldDataService from '../../services/WorldDataService';
import GameDataService from '../../services/GameDataService';
import { useAuthStore } from "../../stores/useAuth";
import { useWorldStore } from "../../stores/useWorld";
import { useFleetStore } from '../../stores/useFleet';

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const skillStore = useSkillsStore();
const { skillsData } = storeToRefs(skillStore);

const { bus, emit } = useEventsBus();

const isOpen = ref(false)

function closeModal() {
  if (counter == ships.value.length) {
    isOpen.value = false
  }
}

function openModal() {
  isOpen.value = true
}

const ships = ref([])

function getShips(){
  ships.value = useFleetStore().shipsData;
}

let arrayFields = []
let fieldId = ref("")

function selectField(e){
  fieldId.value = e.target.attributes['id'].value;
  arrayFields = document.querySelectorAll(".fields");

  arrayFields.forEach(field => {
    field.setAttribute('class', 'w-12 h-12 block border-2 border-white fields');
  });

  e.target.setAttribute("class",  "w-12 h-12 block border-2 border-red-600 fields");
}

let shipField = {}
let shipName = "", counter = 0

const game = ref([])

async function getGame(id){
  GameDataService.getGame(id)
  .then((res) => {
    game.value = res.data.data;
    console.log(game.value.recipient_positions)
    console.log(id)

    let check = 0;
    if (check == 0) {
      openModal();
      getShips();
      console.log("MODAL OTWARTY");
      console.log(check);
    }
    
  })
  .catch((error) => {
    //console.log(error)
  });
}

async function selectShip(e){
  if(fieldId.value != ""){
    
    counter++

    e.target.setAttribute('class', 'hover:bg-white hover:cursor-pointer selected');
    
    arrayFields.forEach(field => {
      field.setAttribute('class', 'w-12 h-12 block border-2 border-white fields');
    });

    shipField[e.target.attributes['id'].value] = fieldId.value;

    ships.value.forEach(ship => {
      if(ship.id == e.target.attributes['id'].value){
        shipName = ship.name
      }
    });

    arrayFields.forEach(field => {
      if(field.attributes['id'].value === fieldId.value){
        field.innerHTML = shipName
      }
    });

    e.target.remove()
    
  }
  
  console.log(shipField)
  fieldId.value = ""

  console.log("Ilosć statków na liście:"+counter)
  console.log("Ilośc statków:"+ships.value.length)

  if (counter == ships.value.length) {
    if(game.value.sender_id === userData.value.user_id){
      GameDataService.updateSenderPositions(userData.value.current_query_id, shipField)
      .then(() => {
        console.log("success")
      })
      .catch((error) => {
        console.log(error.response.data)
      })
    }

    if(game.value.recipient_id === userData.value.user_id){
      GameDataService.updateRecipientPositions(userData.value.current_query_id, shipField)
      .then(() => {
        console.log("success")
      })
      .catch((error) => {
        console.log(error.response.data)
      })
    }
    
    closeModal();
  }
}

watch(
  () => bus.value.get('showPlacingModal'),
  () => {
    getGame(userData.value.current_query_id);
  }
);

</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
      <Dialog as="div" @close="closeModal" class="relative z-10 text-gray-600">
        <TransitionChild
          as="template"
          enter="duration-300 ease-out"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="duration-200 ease-in"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-black bg-opacity-25" />
        </TransitionChild>
  
        <div class="fixed inset-0 overflow-y-auto ">
          <div
            class="flex min-h-full items-center justify-center p-4 text-center"
          >
            <TransitionChild
              as="template"
              enter="duration-300 ease-out"
              enter-from="opacity-0 scale-95"
              enter-to="opacity-100 scale-100"
              leave="duration-200 ease-in"
              leave-from="opacity-100 scale-100"
              leave-to="opacity-0 scale-95"
            >
              <DialogPanel
                class="w-full max-w-2xl bg-orange-500 transform overflow-hidden rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  Place your ships on board: 
                  <button
                    type="button"
                    class="text-gray-600 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:bg-gray-600 hover:text-white"
                    @click="closeModal"
                  >
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                  </button>
                </DialogTitle>
                <div class="mt-2">
                  <div class="block p-2 justify-center">
                    <div class="inline-block flex justify-center text-white">
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
                        <div class="w-12 h-12 block border-2 border-white fields" id="A1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I1" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J1" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">2</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I2" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J2" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">3</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I3" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J3" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">4</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I4" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J4" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">5</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A5" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J5" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">6</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I6" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J6" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">7</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I7" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J7" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">8</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I8" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J8" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">9</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I9" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J9" @click="selectField"></div>
                    </div>
                    <div class="inline-block flex justify-center">
                        <div class="w-12 h-12 block text-white">10</div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="A10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="B10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="C10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="D10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="E10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="F10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="G10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="H10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="I10" @click="selectField"></div>
                        <div class="w-12 h-12 block border-2 border-white fields" id="J10" @click="selectField"></div>
                    </div>
                  </div>
                  <ul v-for="ship in ships" :key="ship.id" id="listships">
                    <li :id="ship.id" @click="selectShip" class="hover:bg-white hover:cursor-pointer shipItem"><font-awesome-icon icon="fa-solid fa-sailboat" class="mr-2"/>{{ ship.name }} - {{ ship.type }}</li>
                  </ul>
                </div>
  
                <div class="mt-4">
                 
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </template>