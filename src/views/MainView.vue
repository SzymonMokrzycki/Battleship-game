<script setup>  
import { ref, watch } from "vue";
import { storeToRefs } from "pinia";
import { useAuthStore } from "../stores/useAuth";
import { useWorldStore } from "../stores/useWorld";
import { useSkillsStore } from "../stores/useSkills";
import { useRouter } from "vue-router";
import { Menu, MenuButton, MenuItems, MenuItem } from '@headlessui/vue'
import Caribbean from '../components/maps/Caribbean.vue'
import Bahamas from '../components/maps/Bahamas.vue'
import Falklands from '../components/maps/Falklands.vue'
import Indonesia from '../components/maps/Indonesia.vue'
import Philipines from '../components/maps/Philipines.vue'
import Vanatu from '../components/maps/Vanatu.vue'
import useEventsBus from "@/composables/eventBus";
import skillsModal from '../components/modals/SkillsModal.vue'
import settingsModal from '../components/modals/SettingsAccountModal.vue'
import fleetsModal from '../components/modals/YourFleetsModal.vue'
import historyModal from '../components/modals/HistoryBattleModal.vue'
import invitationsModal from '../components/modals/InvitationsModal.vue'
import equipmentModal from '../components/modals/EquipmentModal.vue'
import gameModal from '../components/modals/GameModal.vue'
import GamePlayersModal from "../components/modals/GamePlayersModal.vue";
import Board from '../components/Board.vue'
import BoardGameAI from '../components/BoardGameAI.vue'
import WorldDataService from "../services/WorldDataService";
import GameDataService from "../services/GameDataService";
import InfoGameModal from "../components/modals/InfoGameModal.vue";
import PlacingShipsModalAI from '../components/modals/PlacingShipsModalAI.vue';

const { emit, bus } = useEventsBus();
const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);
const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);
const skillsStore = useSkillsStore();
const { skillsData } = storeToRefs(skillsStore);
const router = useRouter();

const querId = ref(0)

async function handleLogout() {
if(!userData.value.user_is_in_battle){
    await WorldDataService.updateWorldData({
        world_id: worldData.value.world_id,
        lvl: worldData.value.lvl,
        rank: worldData.value.rank,
        exp: worldData.value.exp,
        battlescore: worldData.value.battlescore
    }, userData.value.user_id)

    await WorldDataService.updateSkillsData({
        world_id: worldData.value.world_id,
        skill_points: skillsData.value.skill_points,
        shooting: skillsData.value.shooting,
        accuracy: skillsData.value.accuracy,
        commanding: skillsData.value.commanding,
        shipbuilding: skillsData.value.shipbuilding
    }, userData.value.user_id)

    useAuthStore()
        .logout()
        .then(() => router.push("/login"));

    clearInterval(expInterval);
}
 
}

const queryData = ref({})

function enterBattle(){
    if(userData.value.current_query_id != 0)
        checkQueryStatus(userData.value.current_query_id);
    else
        emit('showOceanModal');
}

function exitBattle(){
    userData.value.user_is_in_battle = false;
    userData.value.current_query_id = 0
}

const checkQueryStatus = async (query_id) => {
  GameDataService.getQueryStatus(query_id)
  .then((res) => {
      queryData.value = res.data.data;
      console.log(queryData.value.sender_id)
      if(queryData.value.status === 'accepted' && userData.value.user_id === queryData.value.sender_id){
        userData.value.user_is_in_battle = true;
      }
  })
  .catch((error) => {
      
  }); 
};

watch(
  () => bus.value.get('exit'),
  () => {
    exitBattle();
  }
);

watch(
  () => bus.value.get('backFromSending'),
  (query_id) => {
    querId.value = query_id;
  }
);

async function backToChoose(){
    if(!userData.value.user_is_in_battle){
        await WorldDataService.updateWorldData({
            world_id: worldData.value.world_id,
            lvl: worldData.value.lvl,
            rank: worldData.value.rank,
            exp: worldData.value.exp,
            battlescore: worldData.value.battlescore
        }, userData.value.user_id)

        await WorldDataService.updateSkillsData({
            world_id: worldData.value.world_id,
            skill_points: skillsData.value.skill_points,
            shooting: skillsData.value.shooting,
            accuracy: skillsData.value.accuracy,
            commanding: skillsData.value.commanding,
            shipbuilding: skillsData.value.shipbuilding
        }, userData.value.user_id)

        router.push('/login');
        clearInterval(expInterval);
    }
}

let percentExp, barExp

function setExp(){
    if(worldData.value.exp >= 100){
        worldData.value.exp = 0
        worldData.value.lvl++;
        skillsData.value.skill_points = 3
        emit('confirmedSkills')
    }

    if(worldData.value.lvl >= 1 && worldData.value.lvl <= 4){
        worldData.value.rank = 'novice'
    }else if(worldData.value.lvl >= 5 && worldData.value.lvl <= 9){
        worldData.value.rank = 'mariner'
    }else if(worldData.value.lvl >= 10 && worldData.value.lvl <= 14){
        worldData.value.rank = 'bosman'
    }else if(worldData.value.lvl >= 15 && worldData.value.lvl <= 19){
        worldData.value.rank = 'first officer'
    }else if(worldData.value.lvl >= 20 && worldData.value.lvl <= 29){
        worldData.value.rank = 'captain'
    }else if(worldData.value.lvl >= 30 && worldData.value.lvl <= 54){
        worldData.value.rank = 'admiral'
    }else if(worldData.value.lvl >= 55){
        worldData.value.rank = 'admiral fleet'
    }
    
    percentExp = Math.round((worldData.value.exp / 100) * 100);
    barExp = document.getElementById('expbar');

    barExp.style.width = percentExp + '%'
}

const expInterval = setInterval(() => {
    setExp()
}, 100);

watch(
  () => bus.value.get('stopInterval'),
  () => {
    clearInterval(expInterval)
  }
);

</script>

<template>
<aside class="ml-[-100%] fixed z-10 top-0 pb-3 px-6 w-full flex flex-col justify-between h-screen border-r bg-orange-500 transition duration-300 md:w-4/12 lg:ml-0 lg:w-[25%] xl:w-[20%] 2xl:w-[15%]">
    <div>
        <div class="mt-2 text-center">
            <div class="text-rose-800">
                <img v-if="userData.user_avatar != null" :src="userData.user_avatar" alt="" class="w-10 h-10 m-auto rounded-full object-cover lg:w-28 lg:h-28 border-4">
                <img v-if="userData.user_avatar == null" src="empty.png" alt="" class="w-10 h-10 m-auto rounded-full object-cover lg:w-28 lg:h-28 border-4">
                <span class="hidden m-auto text-white mb-4 w-20 lg:block text-2xl font-bold bg-stone-600">{{ worldData.lvl }}</span>
                
                <span>Exp</span>
                <div class="flex items-start mb-2">
                    <span class="mr-1">{{ worldData.exp }}/100</span> 
                    <div @load="setExp" class="mt-2 w-40 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div id="expbar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
               
            </div>
            <h5 class="hidden mt-2 text-xl font-semibold text-gray-600 lg:block">{{ userData.user_name }}</h5>
            <h5 class="hidden mt-1 text-xl font-semibold text-gray-600 lg:block">Rank: {{ worldData.rank }}</h5>
            <span v-if="worldData.rank == 'novice'"><font-awesome-icon icon="fa-solid fa-star" /></span>
            <span v-if="worldData.rank == 'mariner'">
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
            </span>
            <span v-if="worldData.rank == 'bosman'">
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
            </span>
            <span v-if="worldData.rank == 'first officer'">
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
            </span>
            <span v-if="worldData.rank == 'captain'">
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
            </span>
            <span v-if="worldData.rank == 'admiral'">
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
            </span>
            <span v-if="worldData.rank == 'admiral fleet'">
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
                <font-awesome-icon icon="fa-solid fa-star" />
            </span>
        </div>

        <ul class="space-y-1 tracking-wide mt-6 text-left">
            <li>
                <a href="#" @click="emit('showYourFleetsModal')" aria-label="dashboard" class="relative px-2 py-1 flex items-center space-x-2 rounded-xl text-white hover:bg-gradient-to-r from-red-700 to-red-500">
                    <font-awesome-icon icon="fa-solid fa-list" class="text-gray-600" />
                    <span class="-mr-1 font-medium">Your fleets</span>
                </a>
            </li>
            <li>
                <a href="#" @click="emit('showHistoryBattleModal')" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600 ">
                    <font-awesome-icon icon="fa-solid fa-clock-rotate-left" class="text-gray-600" />
                    <span class="">Battles history</span>
                </a>
            </li>
            <li>
                <a href="#" @click="emit('showInvitationsModal')" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                    <font-awesome-icon icon="fa-solid fa-envelope" class="text-gray-600" />
                    <span class="">Game invitations</span>
                </a>
            </li>
            <li>
                <a href="#" @click="emit('showEquipmentModal')" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                    <font-awesome-icon icon="fa-solid fa-briefcase" class="text-gray-600" />
                    <span class="">Equipment</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="px-4 -mx-6 pt-2 grid justify-center text-white items-center border-t">
        <button @click="backToChoose" class="px-4 py-3 flex items-center space-x-4 rounded-md text-gray-600 group">
            <!--<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>-->
            <span class="group-hover:text-gray-700"><font-awesome-icon :icon="['fas', 'globe']" /> Switch world</span>
        </button>
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
                <h5 class="lg:text-2xl text-gray-600 hidden text-xs font-medium lg:block">{{ worldData.name }}</h5>

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
                            <div class="text-center">
                                <img v-if="userData.user_avatar != null" :src="userData.user_avatar" alt="" class="w-16 h-16 m-auto rounded-full object-cover lg:w-28 lg:h-28 border-4">
                                <img v-if="userData.user_avatar == null" src="empty.png" alt="" class="w-16 h-16 m-auto rounded-full object-cover lg:w-28 lg:h-28 border-4">
                                <span class="m-auto text-center text-white mb-4 w-6 block text-base font-bold bg-stone-600">1</span>
                                
                                <span class="text-rose-800">Exp</span>
                                <div class="flex items-start mb-2">
                                    <span class="mr-1 text-rose-800">{{ worldData.exp }}/100</span> 
                                    <div class="mt-2 w-40 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                                    </div>
                                </div>
                                <h5 class="mt-2 text-center text-xl font-semibold text-gray-600 lg:block">{{ userData.user_name }}</h5>
                                <h5 class="mt-1 text-center text-xl font-semibold text-gray-600 lg:block">Rank: {{ worldData.rank }}</h5>
                                <span v-if="worldData.rank == 'novice'"><font-awesome-icon icon="fa-solid fa-star" /></span>
                                <span v-if="worldData.rank == 'mariner'">
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                </span>
                                <span v-if="worldData.rank == 'bosman'">
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                </span>
                                <span v-if="worldData.rank == 'first officer'">
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                </span>
                                <span v-if="worldData.rank == 'captain'">
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                </span>
                                <span v-if="worldData.rank == 'admiral'">
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                </span>
                                <span v-if="worldData.rank == 'admiral fleet'">
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                    <font-awesome-icon icon="fa-solid fa-star" />
                                </span>
                            </div>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <a href="#" @click="emit('showYourFleetsModal')" aria-label="dashboard" class="relative px-2 py-1 flex items-center space-x-2 rounded-xl text-white hover:bg-gradient-to-r from-red-700 to-red-500">
                                <font-awesome-icon icon="fa-solid fa-list" class="text-gray-600" />
                                <span class="-mr-1 font-medium">Your fleets</span>
                            </a>
                        </MenuItem>
                    </div>
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                            <a href="#" @click="emit('showHistoryBattleModal')" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600 ">
                                <font-awesome-icon icon="fa-solid fa-clock-rotate-left" class="text-gray-600" />
                                <span class="">Battles history</span>
                            </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <a href="#" @click="emit('showInvitationsModal')" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                                <font-awesome-icon icon="fa-solid fa-envelope" class="text-gray-600" />
                                <span class="">Game invitations</span>
                            </a>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <a href="#" @click="emit('showEquipmentModal')" class="px-2 py-1 flex items-center text-white space-x-4 rounded-xl hover:bg-gradient-to-r from-red-700 to-red-500 rounded-md text-gray-600">
                                <font-awesome-icon icon="fa-solid fa-briefcase" class="text-gray-600" />
                                <span class="">Equipment</span>
                            </a>
                        </MenuItem>
                    </div>

                    <div class="px-1 py-1">
                        <MenuItem>
                            <button @click="backToChoose" class="px-4 py-3 flex items-center space-x-4 rounded-md text-gray-600 group">
                                <!--<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>-->
                                <span class="group-hover:text-gray-700"><font-awesome-icon :icon="['fas', 'globe']" /> Switch world</span>
                            </button>
                        </MenuItem>
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
            <h5 class="lg:text-2xl text-gray-600 text-base lg:hidden font-medium lg:block">{{ worldData.name }}</h5>
            <div class="flex space-x-1 lg:space-x-4">
              <span class="text-rose-800 font-bold text-xs lg:text-base">Battle score: {{ worldData.battlescore }} <font-awesome-icon :icon="['fas', 'medal']" /></span>
              <button @click="enterBattle" v-if="!userData.user_is_in_battle && !userData.game_with_ai" class="text-white text-center bg-rose-800 h-8 lg:h-10 rounded hover:bg-white hover:text-rose-800" title="Play">
                Start battle!
              </button>
              <button @click="emit('showSkillsModal')" class="bg-gray-600 w-8 h-8 lg:w-10 lg:h-10 rounded hover:bg-gray-400" title="Your skills">
                <font-awesome-icon icon="fa-solid fa-chart-simple" class="text-xs lg:text-base text-white"/>
              </button>
              <button @click="emit('showSettingsModal')" class="bg-gray-600 w-8 h-8 lg:w-10 lg:h-10 rounded hover:bg-gray-400" title="Account settings">
                <font-awesome-icon icon="fa-solid fa-gear" class="text-xs lg:text-base text-white"/>
              </button>
            </div>
        </div>
    </div>
    
    <div class="2xl:container overflow-x-auto h-screen" id="ocean" v-if="!userData.user_is_in_battle && !userData.game_with_ai">
        <Caribbean v-if="worldData.name == 'Caribbean'"/>
        <Bahamas v-if="worldData.name == 'Bahamas'"/>
        <Falklands v-if="worldData.name == 'Falklands'"/>
        <Indonesia v-if="worldData.name == 'Indonesia'"/>
        <Philipines v-if="worldData.name == 'Philipines'"/>
        <Vanatu v-if="worldData.name == 'Vanatu'"/>
    </div>
    <div class="2xl:container h-screen" v-if="userData.user_is_in_battle">
        <Board/>
    </div>
    <div class="2xl:container h-screen" v-if="userData.game_with_ai">
        <BoardGameAI/>
    </div>
    <skillsModal/>
    <settingsModal/>
    <fleetsModal/>
    <historyModal/>
    <gameModal/>
    <invitationsModal/>
    <equipmentModal/>
    <GamePlayersModal/>
    <InfoGameModal/>
    <PlacingShipsModalAI/>
</div>
</main>
</template>

<style>
#ocean{
    background-image: url('../assets/images/oceanisland.jpg');
    background-attachment: scroll;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}

</style>