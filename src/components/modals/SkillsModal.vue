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
import { useAuthStore } from "../../stores/useAuth";
import { useWorldStore } from "../../stores/useWorld";
import GameDataService from '../../services/GameDataService';

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const skillStore = useSkillsStore();
const { skillsData } = storeToRefs(skillStore);

const { bus } = useEventsBus();

const isOpen = ref(false)

const confirmOk = ref(false);

function closeModal() {
  isOpen.value = false
  confirmOk.value = false
}

function openModal() {
  isOpen.value = true
}

watch(
  () => bus.value.get('showSkillsModal'),
  () => {
    if(!userData.value.user_is_in_battle){
      openModal();
    }
  }
);

function increaseShooting(){
  if(skillsData.value.skill_points > 0){
    skillsData.value.shooting++;
    skillsData.value.skill_points--;
    confirmSkills()
  }
}

function increaseAccuracy(){
  if(skillsData.value.skill_points > 0){
    skillsData.value.accuracy++;
    skillsData.value.skill_points--;
    confirmSkills()
  }
}

function increaseCommanding(){
  if(skillsData.value.skill_points > 0){
    skillsData.value.commanding++;
    skillsData.value.skill_points--;
    confirmSkills()
  }
}

function increaseShipbuilding(){
  if(skillsData.value.skill_points > 0){
    skillsData.value.shipbuilding++;
    skillsData.value.skill_points--;
    confirmSkills()
  }
}

const fleets = ref([])

const getFleets = async () => {
    return await GameDataService.getUserFleets(userData.value.user_id)
    .then((res) => {
        fleets.value = res.data.data;
        for(let i = 0; i < fleets.value.length; i++){
          for(let j = 0; j < fleets.value[i].ships.length; j++){
            fleets.value[i].ships[j].hp = fleets.value[i].ships[j].hp + (skillsData.value.shipbuilding * 10)
            fleets.value[i].ships[j].strong_crew = fleets.value[i].ships[j].strong_crew + (skillsData.value.commanding * 10)
            
            fleets.value[i].ships[j].damage_canons = fleets.value[i].ships[j].damage_canons + (skillsData.value.shooting * 10) + (skillsData.value.commanding * 10) + (skillsData.value.accuracy * 10)
            console.log(fleets.value[i].ships[j].damage_canons)
            
            GameDataService.updateShip(fleets.value[i].ships[j].id, {
                armament: fleets.value[i].ships[j].armament,
                hp: fleets.value[i].ships[j].hp,
                strongcrew: fleets.value[i].ships[j].strong_crew,
                number_canons: fleets.value[i].ships[j].number_of_canons,
                damage_canons: fleets.value[i].ships[j].damage_canons
            })
          }
        }
    })
    .catch((error) => {
        console.log(error.response.data);
    });
};

async function confirmSkills(){
    getFleets()
    
    const skill = {
      world_id: worldData.value.world_id,
      skill_points: skillsData.value.skill_points,
      shooting: skillsData.value.shooting,
      accuracy: skillsData.value.accuracy,
      commanding: skillsData.value.commanding,
      shipbuilding: skillsData.value.shipbuilding
    };

    console.log(skill)

    WorldDataService.updateSkillsData(skill, userData.value.user_id)
    .then((res) => {
      console.log(res.data)
      confirmOk.value = true;
      
    })
    .catch((error) => {
      console.log(error.response.data);
    });
  
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
                  Your skills
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
                  <p class="text-base text-center">
                    Skills points: {{ skillsData.skill_points }}
                  </p>

                  <table class="mt-8 table-auto m-auto">
                    <tr class="flex">
                      <td>Shooting: {{ skillsData.shooting }} <button type="button" @click="increaseShooting()" class="hover:text-white"><font-awesome-icon :icon="['fas', 'plus']" /></button></td>
                      
                      <td class="ml-4">Shipbuilding: {{ skillsData.shipbuilding }} <button type="button" @click="increaseShipbuilding()" class="hover:text-white"><font-awesome-icon :icon="['fas', 'plus']" /></button></td>
                    </tr>
                    <tr class="flex">
                      <td>Accuracy: {{ skillsData.accuracy }} <button type="button" @click="increaseAccuracy()" class="hover:text-white"><font-awesome-icon :icon="['fas', 'plus']" /></button></td>
                      
                      <td class="ml-4">Commanding: {{ skillsData.commanding }} <button type="button" @click="increaseCommanding()" class="hover:text-white"><font-awesome-icon :icon="['fas', 'plus']" /></button></td>
                    </tr>
                  </table>
                  
                </div>
  
                <div class="mt-4">
                  
                </div>
                <div v-if="confirmOk" class="text-green-700">Skills saved.</div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
  </template>