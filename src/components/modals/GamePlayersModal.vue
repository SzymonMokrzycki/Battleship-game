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
import GameDataService from '../../services/GameDataService';
import { useWorldStore } from "../../stores/useWorld";
import { useAuthStore } from "../../stores/useAuth";
import { useFleetStore } from '../../stores/useFleet';
import WorldDataService from '../../services/WorldDataService';

const { bus, emit } = useEventsBus();

const isOpen = ref(false)

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);
const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const sended = ref("")

const fleetErr = ref(false)

async function submit(sender, user_id, user_name){
  if(useFleetStore().id == 0){
    fleetErr.value = true;
  }else{
    const queryData = {
      sender_id: userData.value.user_id,
      sender: sender,
      user_id: user_id,
      sender_fleet: useFleetStore().id
    };

    GameDataService.addQueryGame(queryData)
    .then((res) => {
      userData.value.current_query_id = res.data.data.id;
    })
    .catch((error) => {
        console.log(error.response.data);
    });

    sended.value = user_name;
    fleetErr.value = false;
  }
}

function closeModal() {
  isOpen.value = false
  emit('backFromSending', [userData.value.current_query_id]);
  fleetErr.value = false;
  sended.value = ""
}

function openModal() {
  isOpen.value = true
}

const users = ref([])
const rankData = ref({})

const getUsers = async () => {
    return GameDataService.getWorldUsers(worldData.value.world_id)
    .then((res) => {
        users.value = res.data.data;

        for(let i = 0; i < users.value.length; i++){
          WorldDataService.getWorldData(worldData.value.world_id, users.value[i].id)
          .then((res) => {
            users.value[i].rank = res.data.data.rank
          })
          .catch((error) => {
              console.log(error.response.data);
          });
        }
    })
    .catch((error) => {
        console.log(error.response.data);
    });
};

watch(
  () => bus.value.get('showPlayersModal'),
  () => {
    openModal();
    getUsers();
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
                class="w-full max-w-md bg-orange-500 transform overflow-hidden rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                Choose player to send invitation game:
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
                  <table v-if="users.length != 1" class="grid">
                    <tr v-for="user in users" :key="user.id" @click="submit(userData.user_name, user.id, user.name)">
                      <td class="hover:bg-white hover:cursor-pointer" v-if="user.id != userData.user_id && user.avatar != null"><img class="mr-2 w-6 h-6 rounded-full"
                          :src="user.avatar"
                          alt="user"></td>
                      <td class="hover:bg-white hover:cursor-pointer" v-if="user.id != userData.user_id && user.avatar == null"><img class="mr-2 w-6 h-6 rounded-full"
                          src="/empty.png"
                          alt="user"></td>
                      <td class="hover:bg-white hover:cursor-pointer" v-if="user.id != userData.user_id">{{ user.name }}</td>
                      <td v-if="user.id != userData.user_id">
                        - rank: <span v-if="user.rank == 'novice'"><font-awesome-icon icon="fa-solid fa-star" /></span>
                        <span v-if="user.rank == 'mariner'">
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                        </span>
                        <span v-if="user.rank == 'bosman'">
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                        </span>
                        <span v-if="user.rank == 'first officer'">
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                        </span>
                        <span v-if="user.rank == 'captain'">
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                        </span>
                        <span v-if="user.rank == 'admiral'">
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                        </span>
                        <span v-if="user.rank == 'admiral fleet'">
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                            <font-awesome-icon icon="fa-solid fa-star" />
                        </span>
                      </td>
                      <td v-if="sended == user.name" class="text-green-700">Sent.</td>
                    </tr>
                  </table>

                  <div v-if="users.length == 1" class="text-red-700">No players available.</div>
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
  </template>