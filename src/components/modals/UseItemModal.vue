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

const { bus, emit } = useEventsBus();

const isOpen = ref(false)

const confirmOk = ref(false);

function closeModal() {
  isOpen.value = false
  confirmOk.value = false
}

function openModal() {
  isOpen.value = true
}

const category = ref("")
const property = ref(0)
const itemId = ref(0)
const equipmentId = ref(0)
const numberItems = ref(0)

watch(
  () => bus.value.get('showUseItemModal'),
  (par) => {
    category.value = par[0][0]
    property.value = par[0][1]
    itemId.value = par[0][2]
    equipmentId.value = par[0][3]
    numberItems.value = par[0][4]
    //console.log(par[0][2])
    getFleets();
    openModal();
  }
);

const fleets = ref([]);

const getFleets = async () => {
  return GameDataService.getUserFleets(userData.value.user_id)
    .then((res) => {
        fleets.value = res.data.data;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

async function chooseFleet(fleet){
    for(let i = 0; i < fleet.ships.length; i++){
        if(category.value == 'strongcrew'){
            let percent = property.value / 100
            fleet.ships[i].strong_crew = fleet.ships[i].strong_crew + (fleet.ships[i].strong_crew * percent)
            fleet.ships[i].damage_canons = fleet.ships[i].damage_canons + fleet.ships[i].strong_crew
            
            GameDataService.updateShip(fleet.ships[i].id, {
              armament: fleet.ships[i].armament,
              hp: fleet.ships[i].hp,
              strongcrew: fleet.ships[i].strong_crew,
              number_canons: fleet.ships[i].number_of_canons,
              damage_canons: fleet.ships[i].damage_canons
            })
        }

        if(category.value == 'cannons'){
            let percent = property.value / 100
            fleet.ships[i].number_of_canons = fleet.ships[i].number_of_canons + (fleet.ships[i].number_of_canons * percent) 
            fleet.ships[i].damage_canons = fleet.ships[i].damage_canons + fleet.ships[i].number_of_canons
            
            GameDataService.updateShip(fleet.ships[i].id, {
              armament: fleet.ships[i].armament,
              hp: fleet.ships[i].hp,
              strongcrew: fleet.ships[i].strong_crew,
              number_canons: fleet.ships[i].number_of_canons,
              damage_canons: fleet.ships[i].damage_canons
            })
        }

        if(category.value == 'hp'){
            let percent = property.value / 100
            fleet.ships[i].hp = fleet.ships[i].hp + (fleet.ships[i].hp * percent)
            
            GameDataService.updateShip(fleet.ships[i].id, {
              armament: fleet.ships[i].armament,
              hp: fleet.ships[i].hp,
              strongcrew: fleet.ships[i].strong_crew,
              number_canons: fleet.ships[i].number_of_canons
            })
        }

        if(category.value == 'armament'){
            let percent = property.value / 100
            fleet.ships[i].armament = fleet.ships[i].armament + (fleet.ships[i].armament * percent)
            fleet.ships[i].damage_canons = fleet.ships[i].damage_canons + fleet.ships[i].armament
            
            GameDataService.updateShip(fleet.ships[i].id, {
              armament: fleet.ships[i].armament,
              hp: fleet.ships[i].hp,
              strongcrew: fleet.ships[i].strong_crew,
              number_canons: fleet.ships[i].number_of_canons,
              damage_canons: fleet.ships[i].damage_canons
            })
        }
    }

    GameDataService.deletItemEquipment(itemId.value, equipmentId.value)
    numberItems.value--
    GameDataService.updateEquipment(equipmentId.value, numberItems.value)
    closeModal()
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
                  <font-awesome-icon icon="fa-solid fa-mountain-sun" class="text-gray-600 mr-2" />
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
                    Choose your ship:
                  </h2>
                  
                  <table class="grid">
                    <tr v-for="fleet in fleets" :key="fleet.id" @click="chooseFleet(fleet)">
                      <td class="hover:bg-white hover:cursor-pointer">{{ fleet.name }}</td>
                    </tr>
                  </table>

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