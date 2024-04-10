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
import { TailwindPagination } from "laravel-vue-pagination";

let result = ref("")

let filters = {
    result: { value: result, keys: ["result"] },
};

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

const dataContent = ref([])
const pagination = ref({})

let percentVictories = 0, countLose = 0, countWin = 0

const getBattles = async (page = 1) => {
  return GameDataService.getHistoryBattles(userData.value.user_id, page)
    .then((res) => {
      dataContent.value = res.data.data.data;
      pagination.value = res.data.data.pagination;
      
      if(dataContent.value.length != 0){
        for(let i = 0; i < dataContent.value.length; i++){
          if(dataContent.value[i].result == 'Win.'){
            countWin++;
          }

          if(dataContent.value[i].result == 'Lose.'){
            countLose++;
          }
        }

        percentVictories = Math.round((countWin / dataContent.value.length) * 100);
      }

    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

watch(
  () => bus.value.get('showHistoryBattleModal'),
  () => {
    if(!userData.value.user_is_in_battle){
      getBattles();
      openModal();
    }
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
                class="w-full max-w-lg bg-orange-500 transform overflow-hidden rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  Battles history
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
                  <label for="result" class="block text-sm font-medium text-white"
                  >Filter by result:</label>
                  <div class="mb-4">
                    <input
                        type="text"
                        name="result"
                        id="result"
                        v-model="result"
                        class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500"                        
                        placeholder="Result"
                    />
                  </div>
                  <VTable id="tableone" 
                  class="border-separate table-auto border-spacing-2 rounded text-base mb-10 border-2 border-orange-500 m-auto text-black text-center"
                  sortHeaderClass="flex items-center justify-between w-full"
                  :data="dataContent" 
                  :filters="filters">
                    <template #head>
                        <tr class="border-2 border-orange-500">
                          <VTh 
                          scope="col"
                          sortKey="result">Result</VTh>
                          <VTh 
                          scope="col"
                          sortKey="lost_ships">Number lost ships</VTh>
                          <VTh 
                          scope="col"
                          sortKey="exp">Experience</VTh>
                          <VTh 
                          scope="col"
                          sortKey="battle_points">Battle score</VTh>
                          <VTh 
                          scope="col"
                          sortKey="date">Date</VTh>
                        </tr>
                    </template>
                    <template #body="{rows}">
                        <tr v-for="battle in rows" :key="battle.id" class="border-2 border-orange-500">
                          <td v-if="battle.result == 'Win.'" class="text-green-800">{{ battle.result }}</td>
                          <td v-if="battle.result != 'Win.'" class="text-red-700">{{ battle.result }}</td>
                          <td>{{ battle.lost_ships }}</td>
                          <td>{{ battle.exp }}</td>
                          <td>{{ battle.battle_points }}</td>
                          <td>{{ battle.date }}</td>
                        </tr>
                    </template>
                  </VTable>
                  <TailwindPagination 
                  class="justify-center text-white bg-transparent"
                  :data="pagination"
                  @pagination-change-page="getBattles"
                  :limit="1"
                  />
                  <span> Percent of victories: {{ percentVictories }} % <br/>W: {{ countWin }}  L: {{ countLose }}</span>
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