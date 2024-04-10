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
import UserDataService from '../../services/UserDataService';
import { useAuthStore } from "../../stores/useAuth";
import { useWorldStore } from "../../stores/useWorld";
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { useRouter } from "vue-router";

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const skillStore = useSkillsStore();
const { skillsData } = storeToRefs(skillStore);

const { bus, emit } = useEventsBus();

const router = useRouter();

const isOpen = ref(false)

const confirmOk = ref(false);

function closeModal() {
  isOpen.value = false
  confirmOk.value = false

  document.getElementById('errorName').innerHTML = ""
  document.getElementById('errorEmail').innerHTML = ""
}

function openModal() {
  isOpen.value = true
}

async function handleLogout() {
  useAuthStore()
    .logout()
    .then(() => router.push("/login"));
    emit('stopInterval')
}

const onSubmit = (user) => {
  let data = {};

  if(user.name == undefined && user.email != undefined){
    data = {
      email: user.email,
      avatar: user.avatar
    }
  }

  if(user.email == undefined && user.name != undefined){
    data = {
      name: user.name,
      avatar: user.avatar
    }
  }

  if(user.email == undefined && user.name == undefined){
    data = {
      avatar: user.avatar
    }
  }

  if(user.email != undefined && user.name != undefined){
    data = {
      name: user.name,
      email: user.email,
      avatar: user.avatar
    }
  }
  //console.log(data)

  UserDataService.updateUser(data, userData.value.user_id)
  .then((res) => {
    console.log(data)
    if(res.data.success){
      handleLogout()
    }else{
      if(res.data.data.name != undefined){
        document.getElementById('errorName').innerHTML = res.data.data.name[0]
      }

      if(res.data.data.email != undefined){
        document.getElementById('errorEmail').innerHTML = res.data.data.email[0]
      }
    }
    //console.log(res.data.data.email[0])
  })
  .catch((err) => {
    console.log(err);
  });
};

const onSubmitPassword = (pass) => {
  const dataPass = {
    password: pass.password
  };

  UserDataService.updateUserPassword(dataPass, userData.value.user_id)
  .then(() => {
    handleLogout()
  })
  .catch((err) => {
    console.log(err);
  });
};

watch(
  () => bus.value.get('showSettingsModal'),
  () => {
    if(!userData.value.user_is_in_battle){
      openModal();
    }
  }
);

const schema = yup.object({
  name: yup
    .string()
    .min(3)
    .max(8),
  email: yup
    .string()
    .email("Email must be a valid email"),
  password: yup    
    .string()
    .matches(
      /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/,
      "Password must contain 8 characters, one uppercase, one lowercase, one number and one special case character"),
  confirmpass: yup
    .string()
    .oneOf([yup.ref("password"), null], "Passwords must match"),
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
                class="w-full max-w-md bg-orange-500 transform overflow-hidden rounded-2xl p-4 text-left align-middle shadow-xl transition-all"
              >
                <DialogTitle
                  as="h3"
                  class="text-lg flex font-medium leading-6 justify-between text-gray-600"
                >
                  Account settings
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
                  <h3>Change account data:</h3>
                  <Form class="mb-4"
                  @submit="onSubmit"
                  :validation-schema="schema"
                  @invalid-submit="onInvalidSubmit"
                  method="POST"
                  >
                    <div class="mb-6">
                      <label for="name" class="block mb-2 text-sm text-gray-600 dark:text-white">Change username</label>
                      <Field type="text" name="name" id="name" placeholder="New user name" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
                      <div class="mt-2 text-sm text-red-800">
                        <ErrorMessage name="name" />
                      </div>
                      <div class="mt-2 text-sm text-red-800" id="errorName">  
                      </div>
                    </div>
                    <div class="mb-6">
                      <label for="email" class="block mb-2 text-sm text-gray-600 dark:text-white">Change Email Address</label>
                      <Field required="" type="email" name="email" id="email" placeholder="New email address" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
                      <div class="mt-2 text-sm text-red-800">
                        <ErrorMessage name="email" />
                      </div>
                      <div class="mt-2 text-sm text-red-800" id="errorEmail">
                      </div>
                    </div>
                    <div class="mb-6">
                      <label for="avatar" class="block mb-2 text-sm text-gray-600 dark:text-white">Set avatar</label>
                      <Field type="file" name="avatar" id="avatar" accept="image/png, image/jpg" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
                    </div>
                    <div class="mb-6">
                      <button type="submit" class="w-full px-3 py-4 text-white bg-rose-800 rounded-md hover:bg-rose-500 focus:outline-none duration-100 ease-in-out border-2 border-white">Send</button>
                    </div>
                    <!--<div class="text-red-700" v-if="errr"></div>-->
                  </Form>
                  <h3>Change password:</h3>
                  <Form class="mb-4"
                  @submit="onSubmitPassword"
                  :validation-schema="schema"
                  @invalid-submit="onInvalidSubmit"
                  method="PATCH"
                  >
                    <div class="mb-6">
                      <div class="flex justify-between mb-2">
                        <label for="password" class="text-sm text-gray-600 dark:text-white">Change Password</label>
                      </div>
                      <Field required="" type="password" name="password" id="password" placeholder="New password" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
                      <div class="mt-2 text-sm text-red-800">
                        <ErrorMessage name="password" />
                      </div>
                    </div>
                    <div class="mb-6">
                      <div class="flex justify-between mb-2">
                        <label for="confirmpass" class="text-sm text-gray-600 dark:text-white">Confirm Password</label>
                      </div>
                      <Field required="" type="password" name="confirmpass" id="confirmpass" placeholder="Confirm password" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
                      <div class="mt-2 text-sm text-red-800">
                        <ErrorMessage name="confirmpass" />
                      </div>
                    </div>
                    <div class="mb-6">
                      <button type="submit" class="w-full px-3 py-4 text-white bg-rose-800 rounded-md hover:bg-rose-500 focus:outline-none duration-100 ease-in-out border-2 border-white">Send</button>
                    </div>
                    <!--<div class="text-red-700" v-if="errr"></div>-->
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