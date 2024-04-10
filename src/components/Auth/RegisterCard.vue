<script setup>
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { ref, onBeforeUnmount, inject } from "vue";
import UserAuthService from "../../services/UserAuthService";
import { useErrorStore } from "../../stores/useError";

const $loading = inject("$loading");

const error = useErrorStore();

const registerOk = ref(false);

const onSubmit = async (newUserData) => {
  const loader = $loading.show();
  console.log(newUserData);
  console.log(newUserData.confirmpass);
  console.log(newUserData);

  UserAuthService.register(newUserData)
  .then((res) => {
    console.log(res.data)
    if(res.data.status == 'success'){
      loader.hide();
      registerOk.value = true;

      document.getElementById('errorName').innerHTML = ''
      document.getElementById('errorEmail').innerHTML = ''
    }else{
      registerOk.value = false;

      loader.hide();

      if(res.data.data.name != undefined){
        document.getElementById('errorName').innerHTML = res.data.data.name[0]
      }

      if(res.data.data.email != undefined){
        document.getElementById('errorEmail').innerHTML = res.data.data.email[0]
      }
    }
    
  })
  .catch((error) => {
    loader.hide();
    registerOk.value = false;
  });

  //emit("passNewOwnerId", { id: registeredOwnerId.value, new_owner_data: newUserData });
};

const schema = yup.object({
  name: yup
    .string()
    .required("Username is a required field")
    .min(2, "Userame must be at least 2 characters"),
  email: yup
    .string()
    .required("Email is a required field")
    .email("Email must be a valid email"),
  password: yup
    .string()
    .required("Password is a required field")
    .matches(
      /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/,
      "Password must contain 8 characters, one uppercase, one lowercase, one number and one special case character"
    ),
  confirmpass: yup
    .string()
    .required("Password confirmation is a required field")
    .oneOf([yup.ref("password")], "Your passwords do not match."),  
  regulations: yup
    .string()
    .required("Accept regulations is required")
});

function onInvalidSubmit({ values, errors, results }) {
  console.log(values); // current form values
  console.log(errors); // a map of field names and their first error message
  console.log(results); // a detailed map of field names and their validation results
}

onBeforeUnmount(() => error.$reset());
</script>

<template>
  <!-- component -->
  <div class="flex justify-center min-h-screen">  
    <div class="container sm:mt-10 mt-10 my-auto max-w-md border-2 border-gray-200 p-3 bg-orange-500 rounded-xl">
      <!-- header -->  
      <div class="text-center my-6">
        <h1 class="text-3xl font-semibold text-white">Sign up</h1>
        <p class="text-white">Create an account</p>
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
            <div class="flex justify-between mb-2">
                <label for="name" class="block mb-2 text-sm text-gray-600 dark:text-white">Username</label>
            </div>
            <Field type="text" name="name" id="name" placeholder="Your username" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" required="" />
            <div class="text-sm text-red-800">
              <ErrorMessage name="name" />
            </div>
            <div class="mt-2 text-sm text-red-800" id="errorName">  
            </div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between mb-2">
                <label for="email" class="block mb-2 text-sm text-gray-600 dark:text-white">Email Address</label>
            </div>
            <Field type="email" name="email" id="email" placeholder="Your email address" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
            <div class="text-sm text-red-800">
              <ErrorMessage name="email" />
            </div>
            <div class="mt-2 text-sm text-red-800" id="errorEmail">
            </div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between mb-2">
              <label for="password" class="text-sm text-gray-600 dark:text-white">Password</label>
            </div>
            <Field type="password" name="password" id="password" placeholder="Your password" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
            <div class="text-sm text-red-800">
              <ErrorMessage name="password" />
            </div>
          </div>
          <div class="mb-6">
            <div class="flex justify-between mb-2">
              <label for="confirmpass" class="text-sm text-gray-600 dark:text-white">Confirm password</label>
            </div>
            <Field type="password" name="confirmpass" id="confirmpass" placeholder="Confirm password" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
            <div class="text-sm text-red-800">
              <ErrorMessage name="confirmpass" />
            </div>
          </div>
          <div class="mb-6">
            <div class="flex justify-center mb-2">
                <Field value="true" type="checkbox" name="regulations" id="regulations" class="mr-2" />
                <label for="regulations" class="text-sm text-gray-600 dark:text-white">Accept regulations and privacy policy.</label>
            </div>
            <div class="text-sm text-red-800">
              <ErrorMessage name="regulations" />
            </div>
          </div>
          <div class="mb-6">
            <button @click="handleRegister" type="submit" class="w-full px-3 py-4 text-white bg-rose-800 rounded-md hover:bg-rose-500 focus:outline-none duration-100 ease-in-out border-2 border-white">Sign up</button>
          </div>
          <div class="text-green-800" v-if="registerOk">Succesfully registred. <a href="/login" class="text-white">Sign in</a></div>
        </Form>
      </div>
    </div>
  </div>
</template>