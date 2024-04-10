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
  () => bus.value.get('showEditPostModal'),
  (user_id) => {
    openModal();
    user = user_id[0].user_id;
  }
);

const onSubmit = async (request) => {
  const postData = {
    message: request.message,
    user_id: userData.value.user_id
  };
  console.log(postData)
  AdminDataService.updatePost(postData, user)
  .then(() => {
        emit('refreshPostsTable');
    })
    .catch((error) => {
        console.log(error)
    });
  closeModal();
  
};

const schema = yup.object({
  message: yup
    .string()
    .required("Message is a required field"),
});

function onInvalidSubmit({ values, errors, results }) {
  console.log(values); // current form values
  console.log(errors); // a map of field names and their first error message
  console.log(results); // a detailed map of field names and their validation results
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
                class="w-full max-w-md bg-orange-500 transform rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  Edit post
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
                  :validation-schema="schema"
                  @invalid-submit="onInvalidSubmit"
                  method="PUT"
                  >
                    <div class="mb-6">
                      <label for="message" class="block mb-2 text-sm text-gray-600 dark:text-white">Message post:</label>
                      <Field v-slot="{ field }" name="message"><textarea v-bind="field" id="message" rows="6" placeholder="Edit post" 
                      class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none dark:text-white dark:placeholder-gray-400 dark:bg-gray-800" /></Field>                    
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