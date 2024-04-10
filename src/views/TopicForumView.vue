<script setup>
import { ref, onBeforeUnmount } from "vue";
import TopicsDataService from "../services/TopicsDataService"
import PostsDataService from "../services/PostsDataService"
import UserDataService from "../services/UserDataService"
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

const topic = route.params.id;

const posts = ref([]);

const topic_name = ref(""); 

const onSubmit = async (postData) => {
  const post = {
    message: postData.comment,
    likes: 0,
    user_id: userData.value.user_id,
    topic_id: topic
  };
  console.log(post)
  if(useAuthStore().loggedIn){
      PostsDataService.addNewPost(post)
        .then((res) => {
            console.log(res);
            setPosts();
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

const pagination = ref({})

const setNameTopic = async () => {
    return TopicsDataService.getDetails(topic)
    .then((res) => {
       topic_name.value = res.data.data.name;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

const setPosts = async (page = 1) => {
  return PostsDataService.getTopicPosts(topic, page)
    .then((res) => {
      posts.value = res.data.data.data;
      pagination.value = res.data.data.pagination;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
};

setPosts();
setNameTopic();

function onInvalidSubmit({ values, errors, results }) {
  console.log(values); // current form values
  console.log(errors); // a map of field names and their first error message
  console.log(results); // a detailed map of field names and their validation results
}

onBeforeUnmount(() => error.$reset());

const setLikes = async (likes, id) => {
    const like = likes + 1;
    const data = {
        likes: like,
        id: id
    };
    if(useAuthStore().loggedIn){
     PostsDataService.updateLikesPost(data)
        .then((res) => {
            console.log(res);
            setPosts();
        })
        .catch((error) => {
        const message =
            (error.response && error.response.data && error.response.data.message) ||
            error.message ||
            error.toString();
            console.log(message);
        });
    }
    else{
        errorLogin();
    }
}

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
  <section class="py-8 lg:py-16">
    <div class="max-w-2xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg lg:text-2xl font-bold text-white">{{ topic_name }} ({{ posts.length }})</h2>
        </div>
        <Form class="mb-6"
        @submit="onSubmit"
        @invalid-submit="onInvalidSubmit"
        method="POST">
            <div class="py-2 px-4 mb-4 bg-white rounded-lg rounded-t-lg border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                <label for="comment" class="sr-only">Your comment</label>
                <Field v-slot="{ field }" name="comment"><textarea v-bind="field" id="comment" rows="6" placeholder="Write a post..." 
                class="px-0 w-full text-sm text-gray-900 border-0 focus:ring-0 focus:outline-none dark:text-white dark:placeholder-gray-400 dark:bg-gray-800" /></Field>
                
            </div>
            <button @click="errorLogin" type="submit"
                class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-primary-700 rounded-lg focus:ring-4 focus:ring-primary-200 dark:focus:ring-primary-900 hover:bg-primary-800">
                Add post
            </button>
        </Form>

        <article v-for="post in posts" class="p-6 mb-6 text-base rounded-lg bg-orange-600">
            <footer class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <p v-if="post.avatar != null" class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white"><img
                            class="mr-2 w-6 h-6 rounded-full"
                            :src="post.avatar"
                            alt="{{ post.author }}">{{ post.author }}</p>
                    <p v-if="post.avatar == null" class="inline-flex items-center mr-3 text-sm text-gray-900 dark:text-white"><img
                            class="mr-2 w-6 h-6 rounded-full"
                            src="/empty.png"
                            alt="{{ post.author }}">{{ post.author }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400"><time pubdate datetime="{{ post.created_at }}"
                            title="{{ post.created_at }}">{{ post.created_at }}</time></p>
                </div>
                <button type="button" @click="setLikes(post.likes, post.id)"
                    class="flex items-center text-sm text-white hover:underline">
                    Like post
                </button>
            </footer>
            <p class="text-gray-500 dark:text-gray-400">{{ post.message }}</p>
            <div class="flex items-center mt-4 space-x-1">
                <font-awesome-icon icon="fa-solid fa-thumbs-up" class="text-white"/>
                <p class="text-white">{{ post.likes }}</p>
            </div>
        </article>
    </div>
  </section>
  <TailwindPagination 
    class="justify-center text-white bg-transparent"
    :data="pagination"
    @pagination-change-page="setPosts"
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