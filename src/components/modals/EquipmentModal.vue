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
import UseItemModal from './UseItemModal.vue';

import 'vue3-carousel/dist/carousel.css'
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel'
import GameDataService from '../../services/GameDataService'

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

const items = ref([]);
const numberItems = ref(0)
const equipmentId = ref(0)

const getItems = async (equipment) => {
  return GameDataService.getEquipmentItems(equipment)
    .then((res) => {
      numberItems.value = res.data.data.number_items
      items.value = res.data.data.items;
      equipmentId.value = res.data.data.id;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};


watch(
  () => bus.value.get('showEquipmentModal'),
  () => {
    if(!userData.value.user_is_in_battle){
      getItems(userData.value.user_id);
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
                class="w-full max-w-md bg-orange-500 transform overflow-hidden rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  Equipment
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
                  <carousel :items-to-show="3" v-if="items.length != 0">
                    <slide v-for="item in items" :key="item.id" class="mr-8 text-sm items-center">
                      <div>
                        <div class="w-32 h-46 left-0 right-0 font-bold text-white text-xl">+{{ item.property }}%</div>
                        <span class="text-center font-bold">
                          {{ item.name }} <br/>
                        </span>
                        <span class="text-center">
                          Category: {{ item.category }} <br/>
                        </span>
                        <button type="button" class="block m-auto text-base bg-gray-600 w-16 lg:h-10 text-white rounded hover:bg-gray-400" @click="emit('showUseItemModal', [item.category, item.property, item.id, equipmentId, numberItems])">Use</button>
                      </div>
                    </slide>

                    <template #addons>
                      <navigation />
                    </template>
                  </carousel>

                  <div v-if="items.length == 0" class="text-center">Equipment is empty.</div>

                </div>
  
                <div class="mt-4">
                  <div v-if="items.length != 0">Items in equipment: {{ numberItems }}</div>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
    <UseItemModal/>
  </template>