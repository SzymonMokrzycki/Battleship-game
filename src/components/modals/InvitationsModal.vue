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
import GameDataService from '../../services/GameDataService';
import { useAuthStore } from "../../stores/useAuth";
import { useWorldStore } from "../../stores/useWorld";
import PlacingShipsModal from './PlacingShipsModal.vue';
import { useFleetStore } from "../../stores/useFleet";

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const skillStore = useSkillsStore();
const { skillsData } = storeToRefs(skillStore);

const { bus, emit } = useEventsBus();

const isOpen = ref(false)

const fleetErr = ref(false);

function closeModal() {
  isOpen.value = false
  fleetErr.value = false;
}

function openModal() {
  isOpen.value = true
}

watch(
  () => bus.value.get('showInvitationsModal'),
  () => {
    if(!userData.value.user_is_in_battle){
      getQueries();
      openModal();
    }
  }
);

const queries = ref([])

const getQueries = async () => {
    return GameDataService.getUserQueries(userData.value.user_id)
    .then((res) => {
      queries.value = res.data.data;
    })
    .catch((error) => {
        console.log(error.response.data);
    });
};

async function acceptQuery(id, sender_fleet){
  if(useFleetStore().id != 0){
    userData.value.current_query_id = id;
    GameDataService.acceptQuery(id);
    getQueries();
    emit('showPlacingModal');
    closeModal();
    userData.value.user_is_in_battle = true;
    GameDataService.addNewGame(id, sender_fleet, useFleetStore().id);
    fleetErr.value = false;
  }else{
    fleetErr.value = true;
  }
}

async function rejectQuery(id){
  GameDataService.rejectQuery(id);
  getQueries();
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
              class="w-full max-w-lg bg-orange-500 transform overflow-hidden rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
            >
              <DialogTitle
                as="h3"
                class="text-lg flex font-medium leading-6 justify-between text-gray-600"
              >
                Game invitations
                <button
                  type="button"
                  class="text-gray-600 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:bg-gray-600 hover:text-white"
                  @click="closeModal"
                >
                  <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                  <span class="sr-only">Close modal</span>
                </button>
              </DialogTitle>
              <div class="mt-2 grid">
                <table>
                  <thead>                      
                    <th>From:</th>
                    <th>Status</th>
                    <th>Date</th>
                  </thead>
                  <tbody>
                    <tr v-for="query in queries" :key="query.id">
                      <td>{{ query.sender }}</td>
                      <td>{{ query.status }}</td>
                      <td>{{ query.date }}</td>
                      <td v-if="!userData.game_with_ai && !userData.user_is_in_battle"><button type="button" @click="acceptQuery(query.id, query.sender_fleet)" class="bg-green-700 hover:bg-green-900 text-white font-bold py-2 px-4 rounded" v-if="query.status == 'waiting'">Accept</button></td>
                      <td v-if="!userData.game_with_ai && !userData.user_is_in_battle"><button type="button" @click="rejectQuery(query.id)" class="bg-red-700 hover:bg-red-900 text-white font-bold py-2 px-4 rounded" v-if="query.status == 'waiting'">Reject</button></td>
                    </tr>
                  </tbody>
                </table>
                <div v-if="fleetErr" class="text-red-700">You must choose fleet.</div>
              </div>

              <div class="mt-4">
              
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
  <PlacingShipsModal/>
</template>