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
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import AdminDataService from '../../services/AdminDataService';

const dateValueFrom = ref([])
const dateValueTo = ref([])
const formatter = ref({
    date: 'DD MMM YYYY',
    month: 'MMM'
})

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
  () => bus.value.get('showBanUserModal'),
  (user_id) => {
    openModal();
    user = user_id[0].user_id;
  }
);

const onSubmit = async (banUserData) => {
  const bannedData = {
    banned_from: banUserData.banned_from[0],
    banned_to: banUserData.banned_to[0]
  };
  console.log(bannedData)
  AdminDataService.updateUser(bannedData, user)
  .then(() => {
        emit('refreshUsersTable');
    })
    .catch((error) => {
        console.log(error)
    });
  closeModal();
  
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
                class="w-full max-w-md bg-orange-500 transform rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  Ban user
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
                  method="PUT"
                  >
                    <div class="mb-6">
                      <label for="banned_from" class="block mb-2 text-sm text-gray-600 dark:text-white">Banned from:</label>
                      <Field name="banned_from" v-model="dateValueFrom"><vue-tailwind-datepicker :formatter="formatter" v-model="dateValueFrom" /></Field>
                    </div>
                    <div class="mb-6">
                      <label for="banned_to" class="block mb-2 text-sm text-gray-600 dark:text-white">Banned to:</label>
                      <Field name="banned_to" v-model="dateValueTo"><vue-tailwind-datepicker :formatter="formatter" v-model="dateValueTo" /></Field>                
                    </div>
                    <div class="mb-6">
                      <button type="submit" class="w-full px-3 py-4 text-white bg-rose-800 rounded-md hover:bg-rose-500 focus:outline-none duration-100 ease-in-out border-2 border-white">Send</button>
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