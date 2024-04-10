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
import BuyModal from './BuyModal.vue';

import 'vue3-carousel/dist/carousel.css'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const skillStore = useSkillsStore();
const { skillsData } = storeToRefs(skillStore);

const { bus, emit } = useEventsBus();

const isOpen = ref(false)

const confirmOk = ref(false);

function closeModal() {
  isOpen.value = false
  confirmOk.value = false
  buyError.value = false
  buyOk.value = false
}

function openModal() {
  isOpen.value = true
}

let title = '';

watch(
  () => bus.value.get('showIslandModal'),
  (name) => {
    openModal();
    title = name[0].name
    buyError.value = false
  }
);

const ships = ref([]);
const items = ref([]);

const getShips = async () => {
  return GameDataService.getAllShips()
    .then((res) => {
      ships.value = res.data.data;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

getShips();

const getItems = async () => {
  return GameDataService.getAllItems()
    .then((res) => {
      items.value = res.data.data;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

getItems();

const disShips = ref(false)
const disItems = ref(false)

function setDisShips(){ 
  disShips.value = true
  disItems.value = false
}

function setDisItems(){ 
  disItems.value = true
  disShips.value = false
}

const equipmentId = ref(0)
const numberOfItems = ref(0)

const getEquipment = async () => {
  return await GameDataService.getEquipmentItems(userData.value.user_id)
  .then((res) => {
    equipmentId.value = res.data.data.id
    numberOfItems.value = res.data.data.number_items
    console.log(numberOfItems.value)
  })
  .catch((error) => {

  })
};

const item = ref({})

const getItem = async (item_id) => {
  return await GameDataService.getItem(item_id)
  .then((res) => {
    item.value = res.data.data
    console.log(item.value)
    console.log(equipmentId.value)
    if(worldData.value.battlescore - item.value.price > 0){
      worldData.value.battlescore = worldData.value.battlescore - item.value.price;
      GameDataService.buyItem(item_id, equipmentId.value);
      
      numberOfItems.value++
      
      GameDataService.updateEquipment(equipmentId.value, numberOfItems.value)
      
      buyError.value = false
      buyOk.value = true
    }else{
      buyError.value = true
      buyOk.value = false
    }
  })
  .catch((error) => {

  })
};

const buyError = ref(false)
const buyOk = ref(false)

async function buyItem(item_id){
  getEquipment()
  getItem(item_id)
}
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
                class="w-full max-w-md bg-orange-500 transform overflow-hidden rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  <font-awesome-icon icon="fa-solid fa-mountain-sun" class="text-gray-600 mr-2" /> {{ title }}
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
                  <h2 class="text-lg text-center mb-4">
                    Shop
                  </h2>
                  <div class="flex justify-center">
                    <button @click="setDisShips" type="button" class="block m-auto text-base bg-gray-600 w-24 lg:h-10 text-white rounded hover:bg-gray-400">Ships</button>
                    <button @click="setDisItems" type="button" class="block m-auto text-base bg-gray-600 w-24 lg:h-10 text-white rounded hover:bg-gray-400">Items</button>
                  </div>
                  
                  <carousel :items-to-show="3" v-if="disShips">
                    <slide v-for="ship in ships" :key="ship.id" class="mr-8 text-sm items-center">
                      <div>
                        <img :src="'/ships/'+ship.type+'.png'" class="w-32 h-46 left-0 right-0"/>
                        <span class="text-center font-bold">
                          {{ ship.name }} <br/>
                        </span>
                        <span class="text-center">
                          Type: {{ ship.type }} <br/>
                        </span>
                        <span class="text-center">
                          Canons: {{ ship.number_of_canons }} <br/>
                        </span>
                        <span class="text-center">
                          Damage: {{ ship.damage_canons }} <br/>
                        </span>
                        <span class="text-center">
                          Speed: {{ ship.speed }} <br/>
                        </span>
                        <span class="text-center">
                          Hp: {{ ship.hp }} <br/>
                        </span>
                        <span class="text-center font-bold text-red-800 text-base">
                          {{ ship.price }}<font-awesome-icon :icon="['fas', 'medal']" /><br/>
                        </span>
                        <button type="button" class="block m-auto text-base bg-gray-600 w-16 lg:h-10 text-white rounded hover:bg-gray-400" @click="emit('showBuyModal', [ship.id])">Buy</button>
                      </div>
                    </slide>

                    <template #addons>
                      <navigation />
                    </template>
                  </carousel>

                  <carousel :items-to-show="3" v-if="disItems">
                    <slide v-for="item in items" :key="item.id" class="mr-8 text-sm items-center">
                      <div>
                        <div class="w-32 h-46 left-0 right-0 font-bold text-white text-xl">+{{ item.property }}%</div>
                        <span class="text-center font-bold">
                          {{ item.name }} <br/>
                        </span>
                        <span class="text-center">
                          Category: {{ item.category }} <br/>
                        </span>
                        <span class="text-center font-bold text-red-800 text-base">
                          {{ item.price }}<font-awesome-icon :icon="['fas', 'medal']" /><br/>
                        </span>
                        <button type="button" class="block m-auto text-base bg-gray-600 w-16 lg:h-10 text-white rounded hover:bg-gray-400" @click="buyItem(item.id)">Buy</button>
                      </div>
                    </slide>

                    <template #addons>
                      <navigation />
                    </template>
                  </carousel>
                </div>
  
                <div class="mt-4">
                  <div v-if="buyOk" class="text-green-700 font-bold">You bought an item. It is now in your equipment.</div>
                  <div v-if="buyError" class="text-red-700 font-bold">You dont have enough battlescore to buy this item.</div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
    <BuyModal/>
  </template>