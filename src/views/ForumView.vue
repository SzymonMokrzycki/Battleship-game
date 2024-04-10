<script setup>
import { ref, onBeforeUnmount } from "vue";
import TopicsDataService from '../services/TopicsDataService';
import { useRoute } from "vue-router";
import { Form, Field } from "vee-validate";
import { useAuthStore } from "../stores/useAuth";
import { storeToRefs } from "pinia";
import { useErrorStore } from "../stores/useError";
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogPanel,
  DialogTitle,
} from '@headlessui/vue'
import { TailwindPagination } from "laravel-vue-pagination";

const error = useErrorStore();
const isOpen = ref(false)

function closeModal() {
  isOpen.value = false
}
function openModal() {
  isOpen.value = true
}

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const route = useRoute();

const topics = ref([]);

const onSubmit = async (topicData) => {
  const topic = {
    name: topicData.name,
    user_id: userData.value.user_id,
  };
  console.log(topic)
  if(useAuthStore().loggedIn){
      TopicsDataService.addTopic(topic)
        .then((res) => {
            console.log(res);
            setTopics();
        })
        .catch((error) => {
        const message =
            (error.response && error.response.data && error.response.data.message) ||
            error.message ||
            error.toString();
        });
    }
};

function errorLogin(){
    if(!useAuthStore().loggedIn){
        openModal();
    }
}

const pagination = ref({});

const setTopics = (page = 1) => {
  return TopicsDataService.getAll(page)
    .then((res) => {
      topics.value = res.data.data.data;
      pagination.value = res.data.data.pagination;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
}; 

setTopics();

function onInvalidSubmit({ values, errors, results }) {
  console.log(values); // current form values
  console.log(errors); // a map of field names and their first error message
  console.log(results); // a detailed map of field names and their validation results
}

onBeforeUnmount(() => error.$reset());

</script>

<template>
  <header>
    <div>
      <nav class="text-white sticky top-0 shadow-xl">
        <RouterLink to="/forum">Game forum</RouterLink>
        <RouterLink to="/about">About game</RouterLink>
        <RouterLink to="/mobile-app">Mobile version</RouterLink>
      </nav>

      <RouterLink to="/"><img alt="Battleship logo" class="logo" src="../assets/images/ship.png"/></RouterLink>

    </div>
  </header>
  <main>
    <Form class="mb-6 flex justify-center"
        @submit="onSubmit"
        @invalid-submit="onInvalidSubmit"
        method="POST">
            <div class="py-2 px-4 mb-4 bg-white rounded-lg rounded-t-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <label for="name" class="sr-only">Your topic</label>
                <Field type="text" name="name" id="name" placeholder="Topic name..." 
                class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none dark:text-white dark:placeholder-gray-400 dark:bg-gray-800" />
            </div>
            <button @click="errorLogin" type="submit"
                class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">
                Add topic
            </button>
        </Form>
    
    <h2 class="text-white mb-2 text-2xl">Game forum topics</h2>
    <table id="tabletwo" class="border-separate table-auto border-spacing-2 rounded text-lg border-2 border-orange-500 m-auto text-black">
      <thead>
        <th>Name</th>
        <th>Author</th>
        <th>Date</th>
      </thead>
      <tbody>
          <tr v-for="topic in topics" class="border-2 border-orange-500">
          <td><RouterLink class="hover:text-white" :to="`/topic-forum/${topic.id}`">{{ topic.name }}</RouterLink></td>
          <td>{{ topic.created_by }}</td>
          <td>{{ topic.created_at }}</td>
          </tr>
      </tbody>
    </table>
    <TailwindPagination 
    class="justify-center text-white bg-transparent"
    :data="pagination"
    @pagination-change-page="setTopics"
    :limit="1"
    />
  </main>
  <TransitionRoot appear :show="isOpen" as="template">
    <Dialog as="div" @close="closeModal" class="relative z-10">
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

      <div class="fixed inset-0 overflow-y-auto">
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
              class="w-full max-w-md transform overflow-hidden rounded-2xl bg-orange-500 p-6 text-left align-middle shadow-xl transition-all"
            >
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-red-700"
              >
                Error
              </DialogTitle>
              <div class="mt-2">
                <p class="text-sm text-red-700">
                    You are not logged in.
                </p>
              </div>

              <div class="mt-4">
                <button
                  type="button"
                  class="inline-flex justify-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                  @click="closeModal"
                >
                  Close
                </button>
                <RouterLink to="/login"
                  type="button"
                  class="ml-2 inline-flex justify-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                  
                >
                  Sign in
                </RouterLink>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<style scoped>
header {
  line-height: 1.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
  max-width: 100%;
  height: auto;
}

nav {
  width: 100%;
  font-size: 12px;
  text-align: center;
  margin-top: 0;
  background-color:chocolate;
  position: sticky;
  margin-bottom: 1rem;
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

@media (min-width: 500px) {
  header {
    display:inline;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 auto 2rem;
    max-width: 40%;
    height: auto;
  }

  header .wrapper {
    display: inline;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: center;
    margin-left: auto;
    font-size: 1rem;

    padding: 1rem 0;
    margin-top: 0;

    background-color:chocolate;
    position: sticky;
  }
}
</style>