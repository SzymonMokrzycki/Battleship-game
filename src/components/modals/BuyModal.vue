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


const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const skillStore = useSkillsStore();
const { skillsData } = storeToRefs(skillStore);

const { bus } = useEventsBus();

const isOpen = ref(false)

const shipId = ref(0)

function closeModal() {
  isOpen.value = false
  buyError.value = false 
}

function openModal() {
  isOpen.value = true
}

watch(
  () => bus.value.get('showBuyModal'),
  (ship_id) => {
    openModal();
    getFleets();
    shipId.value = ship_id;
    getShip(shipId.value);
  }
);

const fleets = ref([])
const ship = ref({})

const getFleets = async () => {
    return GameDataService.getUserFleets(userData.value.user_id)
    .then((res) => {
        fleets.value = res.data.data;
    })
    .catch((error) => {
        console.log(error.response.data);
    });
};

const getShip = async (id) => {
  return GameDataService.getShip(id)
  .then((res) => {
      ship.value = res.data.data;
  })
  .catch((error) => {
      console.log(error.response.data);
  });
};

const buyError = ref(false)

async function buyShip(fleet_id){
  if(worldData.value.battlescore - ship.value.price > 0){
    worldData.value.battlescore = worldData.value.battlescore - ship.value.price;
    GameDataService.buyShip(shipId.value, fleet_id);
    closeModal(); 
    buyError.value = false 
  }else{
    buyError.value = true
  }
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
                  Choose fleet
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
                  <table>
                    <th v-if="fleets.length != 0">Name:</th>
                    <tr v-for="fleet in fleets" :key="fleet.id" class="hover:bg-white cursor-pointer" @click="buyShip(fleet.id)">
                      <td>{{ fleet.name }}</td>
                    </tr>
                  </table>
                </div>
  
                <div class="mt-4">
                  <div v-if="buyError" class="text-red-700 font-bold">You dont have enough battlescore to buy this ship.</div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </template>