<script setup>
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";
import { ref, onBeforeUnmount, inject } from "vue";
import { useRouter } from "vue-router";
import { useErrorStore } from "../../stores/useError";
import UserAuthService from "../../services/UserAuthService";

const $loading = inject("$loading");
const fullPage = ref(true);

const router = useRouter();
const error = useErrorStore();

const resetEmailOk = ref(false);
const resetEmailErr = ref(false);

const onSubmit = async (email) => {
  const loader = $loading.show();
  UserAuthService.sendPasswordResetEmail(email)
    .then((res) => {
      loader.hide();
      //router.push("/login");
      resetEmailOk.value = true;

      if(res == null){
        resetEmailOk.value = false;
        resetEmailErr.value = true;
      }
    })
    .catch((error) => {
      loader.hide();
      const message =
        (error.response && error.response.data && error.response.data.message) ||
        error.message ||
        error.toString();
        
        console.log(resetEmailErr)
    });
};

const schema = yup.object({
  email: yup
    .string()
    .required("Email is a required field")
    .email("Email must be a valid email"),
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
          <!-- header -->  
          <div class="text-center my-6">
            <h1 class="text-3xl font-semibold text-white">Reset password</h1>
          </div>
          <!-- sign-in -->
          <div class="m-6">
            <Form
            @submit="onSubmit"
            :validation-schema="schema"
            @invalid-submit="onInvalidSubmit"
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
                <button type="submit" class="w-full px-3 py-4 text-white bg-rose-800 rounded-md hover:bg-rose-500 focus:outline-none duration-100 ease-in-out border-2 border-white">Send email reset</button>
              </div>
              <div class="text-green-700" v-if="resetEmailOk">Reset email sent. Check your email post.</div>
              <div class="text-red-700" v-if="resetEmailErr">E-mail account does not exist</div>
            </Form>
          </div>
        </div>
      </div>
</template>