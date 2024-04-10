<script setup>
import { ref, computed, watch } from 'vue'
import {
  Combobox,
  ComboboxInput,
  ComboboxButton,
  ComboboxOptions,
  ComboboxOption,
  TransitionRoot,
} from '@headlessui/vue'
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid'
import {
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'
import useEventsBus from "@/composables/eventBus";
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import AdminDataService from '../../services/AdminDataService';
import UserDataService from '../../services/UserDataService';
import { storeToRefs } from "pinia";
import { useAuthStore } from "../../stores/useAuth";

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const { emit, bus } = useEventsBus();

const isOpen = ref(false)

function closeModal() {
  isOpen.value = false
}

function openModal() {
  isOpen.value = true
}

let user = 0;

watch(
  () => bus.value.get('showEditWorldModal'),
  (user_id) => {
    openModal();
    user = user_id[0].user_id;
  }
);

const onSubmit = async (request) => {
  const worldData = {
    status: request.selected.name
  };
  console.log(worldData)
  AdminDataService.updateWorld(worldData, user)
  .then(() => {
        emit('refreshWorldsTable');
    })
    .catch((error) => {
        console.log(error)
    });
  closeModal();
  
};

function onInvalidSubmit({ values, errors, results }) {
  console.log(values); // current form values
  console.log(errors); // a map of field names and their first error message
  console.log(results); // a detailed map of field names and their validation results
}

const statuses = [
  { id: 1, name: 'open' },
  { id: 2, name: 'close' },
]

let selected = ref(statuses[0])
let query = ref('')

let filteredStatuses = computed(() =>
  query.value === ''
    ? statuses
    : statuses.filter((person) =>
        person.name
          .toLowerCase()
          .replace(/\s+/g, '')
          .includes(query.value.toLowerCase().replace(/\s+/g, ''))
      )
)
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
                class="w-full max-w-md bg-orange-500 transform rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  Edit world status
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
                  <Form class="mb-4"
                  @submit="onSubmit"
                  @invalid-submit="onInvalidSubmit"
                  method="PUT"
                  >
                      <label for="selected" class="block mb-2 text-sm text-gray-600 dark:text-white">Status:</label>
                        <div class=" top-16 w-72">
                        <Field name="selected" v-model="selected"><Combobox v-model="selected">
                        <div class="relative mt-1">
                            <div
                            class="relative w-full cursor-default overflow-hidden rounded-lg bg-white text-left shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75 focus-visible:ring-offset-2 focus-visible:ring-offset-teal-300 sm:text-sm"
                            >
                            <ComboboxInput
                                class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 focus:ring-0"
                                :displayValue="(status) => status.name"
                                @change="query = $event.target.value"
                            />
                            <ComboboxButton
                                class="absolute inset-y-0 right-0 flex items-center pr-2"
                            >
                                <ChevronUpDownIcon
                                class="h-5 w-5 text-gray-400"
                                aria-hidden="true"
                                />
                            </ComboboxButton>
                            </div>
                            <TransitionRoot
                            leave="transition ease-in duration-100"
                            leaveFrom="opacity-100"
                            leaveTo="opacity-0"
                            @after-leave="query = ''"
                            >
                            <ComboboxOptions
                                class="absolute mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                            >
                                <div
                                v-if="filteredStatuses.length === 0 && query !== ''"
                                class="relative cursor-default select-none py-2 px-4 text-gray-700"
                                >
                                Nothing found.
                                </div>

                                <ComboboxOption
                                v-for="status in filteredStatuses"
                                as="template"
                                :key="status.id"
                                :value="status"
                                v-slot="{ selected, active }"
                                >
                                <li
                                    class="relative cursor-default select-none py-2 pl-10 pr-4"
                                    :class="{
                                    'bg-teal-600 text-white': active,
                                    'text-gray-900': !active,
                                    }"
                                >
                                    <span
                                    class="block truncate"
                                    :class="{ 'font-medium': selected, 'font-normal': !selected }"
                                    >
                                    {{ status.name }}
                                    </span>
                                    <span
                                    v-if="selected"
                                    class="absolute inset-y-0 left-0 flex items-center pl-3"
                                    :class="{ 'text-white': active, 'text-teal-600': !active }"
                                    >
                                    <CheckIcon class="h-5 w-5" aria-hidden="true" />
                                    </span>
                                </li>
                                </ComboboxOption>
                            </ComboboxOptions>
                            </TransitionRoot>
                        </div>
                        </Combobox>
                        </Field>
                        <div class="mt-2 mb-6">
                            <button type="submit" class="w-full px-3 py-4 text-white bg-rose-800 rounded-md hover:bg-rose-500 focus:outline-none duration-100 ease-in-out border-2 border-white">Send</button>
                        </div>
                </div>
            </Form>
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