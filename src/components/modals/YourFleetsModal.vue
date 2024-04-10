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
import { useFleetStore } from "../../stores/useFleet";
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import FleetShipsModal from './FleetShipsModal.vue';

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const skillStore = useSkillsStore();
const { skillsData } = storeToRefs(skillStore);

const fleetStore = useFleetStore();
const { currentFleet } = storeToRefs(fleetStore);

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

watch(
  () => bus.value.get('showYourFleetsModal'),
  () => {
    if(!userData.value.user_is_in_battle){
      openModal();
      getFleets();
    }
  }
);

const fleets = ref([])

const getFleets = async () => {
    return GameDataService.getUserFleets(userData.value.user_id)
    .then((res) => {
        fleets.value = res.data.data;
    })
    .catch((error) => {
        console.log(error.response.data);
    });
};

const onSubmit = async (request) => {
  const fleetData = {
    name: request.name,
    number_of_ships: 10,
    user_id: userData.value.user_id
  };
  console.log(fleetData)
  GameDataService.addFleet(fleetData);
  getFleets();
};

const schema = yup.object({
  name: yup
    .string()
    .required("Name is a required field"),
});

function onInvalidSubmit({ values, errors, results }) {
  console.log(values); // current form values
  console.log(errors); // a map of field names and their first error message
  console.log(results); // a detailed map of field names and their validation results
}

async function chooseFleet(fleet_id, name, ships){
  if(ships.length >= 10){
    useFleetStore().setFleetData(fleet_id, name, ships);
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
                class="w-full max-w-2xl bg-orange-500 transform overflow-hidden rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  Your fleets
                  <button
                    type="button"
                    class="text-gray-600 bg-transparent rounded-lg text-sm p-1.5 ml-auto inline-flex items-center hover:bg-gray-600 hover:text-white"
                    @click="closeModal"
                  >
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                  </button>
                </DialogTitle>
                <div class="mt-2 mb-8 grid">
                  <table>
                    <th v-if="fleets.length != 0">Name:</th>
                    <th v-if="fleets.length != 0">Number of ships:</th>
                    <tr v-for="fleet in fleets" :key="fleet.id" class="hover:bg-white cursor-pointer" @click="chooseFleet(fleet.id, fleet.name, fleet.ships)">
                      <td>{{ fleet.name }}</td>
                      <td>{{ fleet.number_of_ships}}</td>
                      <td v-if="useFleetStore().name == fleet.name" class="text-green-700 font-bold"><font-awesome-icon :icon="['fas', 'check']" />Choosed</td>
                      <td class="text-green-700 font-bold" v-if="fleet.information == 'Fleet complete.'">{{ fleet.information }}</td>
                      <td class="text-red-700 font-bold" v-if="fleet.information == 'Fleet incomplete.'">{{ fleet.information }}</td>
                      <td><button @click="emit('showFleetShipsModal', [fleet.id])" type="button" class="block m-auto text-base bg-gray-600 w-24 lg:h-10 text-white rounded hover:bg-gray-400" title="Preview">
                        <span class="text-sm lg:text-base">Preview</span>
                      </button></td>
                    </tr>
                  </table>
                </div>
  
                <div class="mt-4">
                  <Form class="mb-4 grid"
                  @submit="onSubmit"
                  :validation-schema="schema"
                  @invalid-submit="onInvalidSubmit"
                  method="POST"
                  >
                  <Field type="text" name="name" placeholder="Name" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500"/>
                  <div class="mt-2 text-sm text-red-800">
                    <ErrorMessage name="name" />
                  </div>
                  <button type="submit" class="block m-auto text-base bg-gray-600 w-24 lg:h-10 text-white rounded hover:bg-gray-400" title="Add fleet">
                    <span class="text-sm lg:text-base">Add fleet</span>
                  </button>
                  </Form>
                </div>
              </DialogPanel>
            </TransitionChild>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>
    <FleetShipsModal/>
  </template>