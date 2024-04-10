<script setup>
import { ref } from 'vue'
import { storeToRefs } from "pinia";
import { useAuthStore } from "../stores/useAuth";
import { useRouter } from "vue-router";
import useEventsBus from "@/composables/eventBus";
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import SettingsModal from '@/components/modals/SettingsAccountModal.vue'
import UsersTable from '../components/tables/UsersTable.vue'
import TopicsTable from '../components/tables/TopicsTable.vue'
import PostsTable from '../components/tables/PostsTable.vue'
import WorldsTable from '../components/tables/WorldsTable.vue'
import IslandsTable from '../components/tables/IslandsTable.vue'

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);
const router = useRouter();
const { emit } = useEventsBus();

async function handleLogout() {
  useAuthStore()
    .logout()
    .then(() => router.push("/login"));
}

let content = '';

async function setTable(menuItem){
    if(menuItem == 'users'){
        content = 'users'
    }

    if(menuItem == 'topics'){
        content = 'topics'
    }

    if(menuItem == 'posts'){
        content = 'posts'
    }

    if(menuItem == 'worlds'){
        content = 'worlds'
    }

    if(menuItem == 'islands'){
        content = 'islands'
    }
}

</script>

<template>
<aside class="ml-[-100%] fixed z-10 top-0 pb-3 px-6 w-full flex flex-col justify-between h-screen border-r bg-orange-500 transition duration-300 md:w-4/12 lg:ml-0 lg:w-[25%] xl:w-[20%] 2xl:w-[15%]">
    <div>
        <div class="mt-2 text-center">
            <div class="text-rose-800">
                <img v-if="userData.user_avatar != null" :src="userData.user_avatar" alt="" class="w-10 h-10 m-auto rounded-full object-cover lg:w-28 lg:h-28 border-4">
                <img v-if="userData.user_avatar == null" src="empty.png" alt="" class="w-10 h-10 m-auto rounded-full object-cover lg:w-28 lg:h-28 border-4">            
            </div>
            <h5 class="hidden mt-2 text-xl font-semibold text-gray-600 lg:block">{{ userData.user_name }}</h5>
            <h5 class="hidden mt-1 text-xl font-semibold text-gray-600 lg:block">{{ userData.user_role }}</h5>
        </div>

        <ul class="space-y-1 tracking-wide mt-6 text-left">
            <li v-if="userData.user_role_id == 1" @click="setTable('users')">
                <a href="#" aria-label="dashboard" class="relative px-2 py-1 flex items-center space-x-2 rounded-xl text-white hover:bg-gradient-to-r from-red-700 to-red-500">
                    <font-awesome-icon icon="fa-solid fa-users" class="text-gray-600" />
                    <span class="-mr-1 font-medium">Users</span>
                </a>
            </li>
            <li v-if="userData.user_role_id == 1 || userData.user_role_id == 2" @click="setTable('topics')">
                <a href="#" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                    <font-awesome-icon icon="fa-solid fa-comments" class="text-gray-600" />
                    <span class="">Forum topics</span>
                </a>
            </li>
            <li v-if="userData.user_role_id == 1 || userData.user_role_id == 2" @click="setTable('posts')">
                <a href="#" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600 ">
                    <font-awesome-icon icon="fa-solid fa-comments" class="text-gray-600" />
                    <span class="">Forum posts</span>
                </a>
            </li>
            <li v-if="userData.user_role_id == 1 || userData.user_role_id == 3" @click="setTable('worlds')">
                <a href="#" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                    <font-awesome-icon icon="fa-solid fa-globe" class="text-gray-600" />
                    <span class="">Worlds</span>
                </a>
            </li>
            <li v-if="userData.user_role_id == 1 || userData.user_role_id == 3" @click="setTable('islands')">
                <a href="#" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                    <font-awesome-icon icon="fa-solid fa-mountain-sun" class="text-gray-600" />
                    <span class="">Islands</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="px-4 -mx-6 pt-2 flex justify-between text-white items-center border-t">
        <button @click="handleLogout" class="px-4 py-3 flex items-center space-x-4 rounded-md text-gray-600 group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span class="group-hover:text-gray-700">Logout</span>
        </button>
    </div>
</aside>
<main>
<div class="ml-auto mb-6 lg:w-[75%] xl:w-[80%] 2xl:w-[85%]">
    <div class="sticky z-10 top-0 h-16 border-b bg-amber-600 lg:py-2.5">
        <div class="px-6 flex items-center justify-between space-x-4 2xl:container">
            <div>
              <Menu as="div" class="inline-block text-left">
                <MenuButton class="w-12 h-16 -mr-2 border-r lg:hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 my-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </MenuButton>

                <transition
                    enter-active-class="transition duration-100 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-75 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <MenuItems
                    class="absolute lg:hidden left-0 mt-2 w-56 origin-top-left rounded-md bg-orange-500 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    >
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                            <div class="mt-2 text-center">
                                <div class="text-rose-800">
                                    <img v-if="userData.user_avatar != null" :src="userData.user_avatar" alt="" class="w-10 h-10 m-auto rounded-full object-cover lg:w-28 lg:h-28 border-4">
                                    <img v-if="userData.user_avatar == null" src="empty.png" alt="" class="w-10 h-10 m-auto rounded-full object-cover lg:w-28 lg:h-28 border-4">                                </div>
                                <h5 class="mt-2 text-xl font-semibold text-gray-600 lg:block">{{ userData.user_name }}</h5>
                                <h5 class="mt-1 text-xl font-semibold text-gray-600 lg:block">{{ userData.user_role }}</h5>
                            </div>
                        </MenuItem>
                        <MenuItem v-slot="{ active }" v-if="userData.user_role_id == 1" @click="setTable('users')">
                            <a href="#" aria-label="dashboard" class="relative px-2 py-1 flex items-center space-x-2 rounded-xl text-white hover:bg-gradient-to-r from-red-700 to-red-500">
                                <font-awesome-icon icon="fa-solid fa-users" class="text-gray-600" />
                                <span class="-mr-1 font-medium">Users</span>
                            </a>
                        </MenuItem>
                    </div>
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }" v-if="userData.user_role_id == 1 || userData.user_role_id == 2" @click="setTable('topics')">
                            <a href="#" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                                <font-awesome-icon icon="fa-solid fa-comments" class="text-gray-600" />
                                <span class="">Forum topics</span>
                            </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }" v-if="userData.user_role_id == 1 || userData.user_role_id == 2" @click="setTable('posts')">
                            <a href="#" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600 ">
                                <font-awesome-icon icon="fa-solid fa-comments" class="text-gray-600" />
                                <span class="">Forum posts</span>
                            </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }" v-if="userData.user_role_id == 1 || userData.user_role_id == 3" @click="setTable('worlds')">
                            <a href="#" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                                <font-awesome-icon icon="fa-solid fa-globe" class="text-gray-600" />
                                <span class="">Worlds</span>
                            </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }" v-if="userData.user_role_id == 1 || userData.user_role_id == 3" @click="setTable('islands')">
                            <a href="#" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                                <font-awesome-icon icon="fa-solid fa-mountain-sun" class="text-gray-600" />
                                <span class="">Islands</span>
                            </a>
                        </MenuItem>
                    </div>

                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                            <button @click="handleLogout" class="px-4 py-3 flex items-center space-x-4 rounded-md text-gray-600 group">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="group-hover:text-gray-700">Logout</span>
                            </button>
                        </MenuItem>
                    </div>
                    </MenuItems>
                </transition>
              </Menu>
            </div>
            <div></div>
            <div class="flex space-x-1 lg:space-x-4">
                <button @click="emit('showSettingsModal')" class="bg-gray-600 w-10 h-10 rounded hover:bg-gray-400" title="Account settings">
                    <font-awesome-icon icon="fa-solid fa-gear" class="text-white"/>
                </button>
            </div>
        </div>
    </div>
    
    <div class="2xl:container overflow-x-auto">
        <UsersTable v-if="content == 'users'"/>
        <TopicsTable v-if="content == 'topics'"/>
        <PostsTable v-if="content == 'posts'"/>
        <WorldsTable v-if="content == 'worlds'"/>
        <IslandsTable v-if="content == 'islands'"/>
    </div>
    <SettingsModal/>
</div>
</main>
</template>