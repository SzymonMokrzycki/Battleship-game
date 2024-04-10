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

import 'vue3-carousel/dist/carousel.css'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'

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

const ships = ref([])

const fleetId = ref(0);
const fleetName = ref("");

watch(
  () => bus.value.get('showFleetShipsModal'),
  (fleet_id) => {
    fleetId.value = fleet_id;
    openModal();
    getFleetShips(fleetId.value);
  }
);

const getFleetShips = async (fleet_id) => {
    return GameDataService.getFleetShips(fleet_id)
    .then((res) => {
        ships.value = res.data.data.ships;
        fleetName.value = res.data.data.name;
    })
    .catch((error) => {
        console.log(error.response.data);
    });
};
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
                  Fleet {{ fleetName }} ships:
                  <button
                    type="button"
                    class="text-gray-600 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:bg-gray-600 hover:text-white"
                    @click="closeModal"
                  >
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                  </button>
                </DialogTitle>
                <div class="mt-2 mb-8">
                  
                  <carousel :items-to-show="3">
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
                          Hp: {{ ship.hp }} <br/>
                        </span>
                      </div>
                    </slide>

                    <template #addons>
                      <navigation />
                    </template>
                  </carousel>
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