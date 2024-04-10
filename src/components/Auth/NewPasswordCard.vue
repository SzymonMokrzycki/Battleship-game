<script setup>
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { ref, onBeforeUnmount, inject } from "vue";
import { useRouter } from "vue-router";
import { useErrorStore } from "../../stores/useError";
import UserAuthService from "../../services/UserAuthService";
import { useRoute } from "vue-router";

const route = useRoute();

const $loading = inject("$loading");

const router = useRouter();
const error = useErrorStore();

const onSubmit = async (resetPasswordData) => {
  const loader = $loading.show();
  const token = route.params.token;

  UserAuthService.passwordResetUpdate(resetPasswordData, token)
    .then((res) => {
      loader.hide();
      router.push("/confirm-reset");
    })
    .catch((error) => {
      const message =
        (error.response && error.response.data && error.response.data.message) ||
        error.message ||
        error.toString();
        loader.hide();
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
    .matches(
      /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/,
      "Password must contain 8 characters, one uppercase, one lowercase, one number and one special case character"
    ),
  confirmpass: yup
    .string()
    .oneOf([yup.ref("password"), null], "Passwords must match"),
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
        <div class="container sm:mt-20 mt-10 my-auto max-w-md border-2 border-gray-200 p-3 bg-orange-500 rounded-xl">
            
          <div class="text-center my-6">
            <h1 class="text-3xl font-semibold text-white">Set new password</h1>
          </div>
        
          <div class="m-6">
            <Form 
            @submit="onSubmit"
            :validation-schema="schema"
            @invalid-submit="onInvalidSubmit"
            class="mb-4"
            method="POST"
            >
              <div class="mb-6">
                <label for="email" class="block mb-2 text-sm text-gray-600 dark:text-white">Email Address</label>
                <Field type="email" name="email" id="email" placeholder="Your email address" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
                <div class="text-sm text-red-800">
                  <ErrorMessage name="email" />
                </div>
              </div>  
              <div class="mb-6">
                <div class="flex justify-between mb-2">
                  <label for="password" class="text-sm text-gray-600 dark:text-white">New password</label>
                </div>
                <Field type="password" name="password" id="password" placeholder="New password" class="w-full px-3 py-2 placeholder-gray-300 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-indigo-100 focus:border-indigo-300 dark:bg-gray-700 dark:text-white dark:placeholder-gray-500 dark:border-gray-600 dark:focus:ring-gray-900 dark:focus:border-gray-500" />
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
                <button type="submit" class="w-full px-3 py-4 text-white bg-rose-800 rounded-md hover:bg-rose-500 focus:outline-none duration-100 ease-in-out border-2 border-white">Set password</button>
              </div>
            </Form>
          </div>
        </div>
      </div>
    </template>