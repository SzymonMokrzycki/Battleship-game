<script setup>
import { LockClosedIcon, EyeIcon, EyeSlashIcon } from "@heroicons/vue/20/solid";
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { ref, onBeforeUnmount, inject } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/useAuth";
import { useErrorStore } from "../../stores/useError";
import { storeToRefs } from "pinia";

const errr = ref(false);
const banError = ref(false);
let banTime = 0;
const router = useRouter();

const $loading = inject("$loading");

const error = useErrorStore();

const onSubmit = (credentials) => {
  const loader = $loading.show();
  const userStore = useAuthStore();
  const { userData } = storeToRefs(userStore);
  
  useAuthStore()
    .login(credentials)
    .then(() => {
      banTime = userData.value.user_banned_time;
      
      if(userData.value.user_is_banned == true){
        banError.value = true;
        this.logout();
      }

      if(userData.value.user_role_id == 4){
        router.push({ name: "chooseworld" });
        loader.hide();
        errr.value = false;
      }else{
        router.push({ name: "admin" });
        loader.hide();
        errr.value = false;
      }  
    })
    .catch((err) => {
      console.log(err);
      loader.hide();
      errr.value = true;
    });
};

const schema = yup.object({
  email: yup
    .string()
    .required("Email is a required field")
    .email("Email must be a valid email"),
  password: yup
    .string()
    .required("Password is a required field")
    .min(8, "Password must be at least 8 characters"),
});

function onInvalidSubmit({ values, errors, results }) {
  console.log(values); // current form values
  console.log(errors); // a map of field names and their first error message
  console.log(results); // a detailed map of field names and their validation results
}

onBeforeUnmount(() => error.$reset());

const showPassword = ref(false);
const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

</script>

<template>
<!-- component -->
<div class="flex justify-center min-h-screen">  
    <div class="container sm:mt-20 mt-10 my-auto max-w-md border-2 border-gray-200 p-3 bg-orange-500 rounded-xl">
      <!-- header -->  
      <div class="text-center my-6">
        <h1 class="text-3xl font-semibold text-white">Sign in</h1>
        <p class="text-white">Sign in to your account</p>
      </div>
      <!-- sign-in -->
      <div class="m-6">
        <Form class="mb-4"
        @submit="onSubmit"
        :validation-schema="schema"
        @invalid-submit="onInvalidSubmit"
        method="POST"
        >
          <div class="mb-6">
            <label for="email" class="block mb-2 text-sm text-gray-600 dark:text-white">Email Address</label>
            <Field required="" type="email" name="email" id="email" placeholder="Your email address" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
            <div class="mt-2 text-sm text-red-800">
              <ErrorMessage name="email" />
            </div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between mb-2">
              <label for="password" class="text-sm text-gray-600 dark:text-white">Password</label>
              <RouterLink to="/reset-password" class="text-sm text-white focus:outline-none focus:text-rose-500 hover:text-rose-500 dark:hover:text-rose-700">Forgot password?</RouterLink>
            </div>
            <div class="relative">
              <Field required="" :type="[showPassword ? 'text' : 'password']" name="password" id="password" placeholder="Your password" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500"/>
              <div
                class="absolute inset-y-0 right-0 flex items-center mr-3 cursor-pointer"
                >  
                <EyeIcon
                  v-if="showPassword === true"
                  class="h-5 w-5 text-orange-500 group-hover:text-orange-400"
                  aria-hidden="true"
                  @click="togglePasswordVisibility"
                />
                <EyeSlashIcon
                  v-else
                  @click="togglePasswordVisibility"
                  class="h-5 w-5 text-orange-500 group-hover:text-orange-400"
                  aria-hidden="true"
                />
              </div>
            </div>
            
            <div class="mt-2 text-sm text-red-800">
              <ErrorMessage name="password" />
            </div>
          </div>
          <div class="mb-6">
            <button type="submit" class="w-full px-3 py-4 text-white bg-rose-800 rounded-md hover:bg-rose-500 focus:outline-none duration-100 ease-in-out border-2 border-white">Sign in</button>
          </div>
          <div class="text-red-700" v-if="errr && !banError">Invalid email or password</div>
          <div class="text-red-700" v-if="banError">
            Sorry, you banned by administrator.<br/>
            You can login on your account after {{ banTime }} days.
          </div>
        </Form>
      </div>
    </div>
  </div>
</template>
