<script setup>
import { ref } from "vue";
import { storeToRefs } from "pinia";
import { useAuthStore } from "../stores/useAuth";
import { useRouter } from "vue-router";
import { useSkillsStore } from "../stores/useSkills";
import WorldDataService from '../services/WorldDataService';
import { useWorldStore } from "../stores/useWorld";

const userStore = useAuthStore();
const { userData } = storeToRefs(userStore);

const worldStore = useWorldStore();
const { worldData } = storeToRefs(worldStore);

const skillStore = useSkillsStore();
const { skillsData } = storeToRefs(skillStore);
const router = useRouter();

async function handleLogout() {
  useAuthStore()
    .logout()
    .then(() => router.push("/login"));
}

const userWorlds = ref([]);
const notUserWorlds = ref([]);


const setUserWorlds = () => {
  return WorldDataService.getUserLoginWorlds(userData.value.user_id)
    .then((res) => {
      userWorlds.value = res.data.data.worlds.data;
      if(userWorlds.value.length == 0){
        router.push('/choose-new-world');
      }
    })
    .catch((error) => {
      console.log(error.response.data);
    });
}; 

const setNotUserWorlds = () => {
  return WorldDataService.getUserNotLoginWorlds(userData.value.user_id)
    .then((res) => {
      notUserWorlds.value = res.data.data.data;
    })
    .catch((error) => {
      console.log(error.response.data);
    });
}; 

setUserWorlds();
setNotUserWorlds();

async function storeWorld(worldObject, user){
  WorldDataService.storeNewWorld(worldObject.id, user);
  const world = {
    world_id: worldObject.id,
    name: worldObject.name,
    lvl: 1,
    exp: 0,
    rank: "novice",
    battlescore: 100000,
  };

  worldStore.storeWorldData(world);

  const userWorldData = {
    user_id: userData.value.user_id,
    world_id: worldObject.id,
    lvl: worldData.value.lvl,
    rank: worldData.value.rank,
    exp: worldData.value.exp,
    battlescore: worldData.value.battlescore,
  };

  WorldDataService.storeUserWorldData(userWorldData)

  const skills = {
    skill_points: 3,
    shooting: 1,
    accuracy: 1,
    commanding: 1,
    shipbuilding: 1
  };

  skillStore.storeData(skills)
  console.log(skillsData.value.skill_points)

  const skillsDataUser = {
    user_id: userData.value.user_id,
    world_id: worldObject.id,
    skill_points: skillsData.value.skill_points,
    shooting: skillsData.value.shooting,
    accuracy: skillsData.value.accuracy,
    commanding: skillsData.value.commanding,
    shipbuilding: skillsData.value.shipbuilding,
  };

  WorldDataService.storeUserSkillsData(skillsDataUser)
}

async function continueGame(world, user_id){
  WorldDataService.getWorldData(world.id, user_id)
    .then((res) => {
      console.log(res.data.data)
      const dataWorld = {
        world_id: world.id,
        name: world.name,
        lvl: res.data.data.lvl,
        exp: res.data.data.exp,
        rank: res.data.data.rank,
        battlescore: res.data.data.battlescore,
      };
      worldStore.storeWorldData(dataWorld);
    })
    .catch((error) => {
      console.log(error.response.data);
    });

  WorldDataService.getSkillsData(world.id, user_id)
    .then((res) => {
      console.log(res.data.data)
      const skills = {
        skill_points: res.data.data.skill_points,
        shooting: res.data.data.shooting,
        accuracy: res.data.data.accuracy,
        commanding: res.data.data.commanding,
        shipbuilding: res.data.data.shipbuilding,
      };
      skillStore.storeData(skills);
    })
    .catch((error) => {
      //console.log(error.response.data);
    });
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

      <img alt="Battleship logo" class="logo" src="../assets/images/ship.png"/>

    </div>
  </header>
  <main>
    <div>
      
      <h1 class="text-white text-2xl">Hello {{ userData.user_name }} !</h1>

      <button @click="handleLogout" class="bn632-hover bn25 mt-10 w-1/4">Logout</button>

      <h2 class="text-white mt-10 mb-2 text-2xl">Your played ocean regions:</h2>
      <table id="tableone" class="border-separate table-auto border-spacing-2 rounded text-lg mb-10 border-2 border-orange-500 m-auto text-black">
        <thead>
            <tr class="border-2 border-orange-500">
            <th>Name</th>
            <th>Players</th>
            <th>Age (in days)</th>
            <th>Status</th>
            <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="world in userWorlds" class="border-2 border-orange-500">
            <td>{{ world.name }}</td>
            <td>{{ world.players }}</td>
            <td>{{ world.age }}</td>
            <td>{{ world.status }}</td>
            <td><RouterLink to="/game" v-if="world.status != 'close'" @click="continueGame(world, userData.user_id)" class="bn3637 bn37">Continue</RouterLink></td>
            </tr>
        </tbody>
      </table>

      <h2 class="text-white mb-2 text-2xl">Play in other ocean region:</h2>
      <table id="tabletwo" class="border-separate table-auto border-spacing-2 rounded text-lg border-2 border-orange-500 m-auto text-black">
        <thead>
            <tr class="border-2 border-orange-500">
            <th>Name</th>
            <th>Players</th>
            <th>Age (in days)</th>
            <th>Status</th>
            <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="world in notUserWorlds" class="border-2 border-orange-500">
            <td>{{ world.name }}</td>
            <td>{{ world.players }}</td>
            <td>{{ world.age }}</td>
            <td>{{ world.status }}</td>
            <td><RouterLink to="/game" v-if="world.status != 'close'" @click="storeWorld(world, userData.user_id)" class="bn3637 bn37">Play now!</RouterLink></td>
            </tr>
        </tbody>
      </table>
    </div>
  </main>
</template>

<style>
#tableone{
    background-image: url('../assets/images/oceanbg.jpg');
    background-repeat: no-repeat;
    background-size: cover;
}

#tabletwo{
    background-image: url('../assets/images/oceanbg.jpg');
    background-repeat: no-repeat;
    background-size: cover;
}

.bn3637 {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.7rem 2rem;
  font-size: 18px;
  text-align: center;
  text-decoration: none;
  color: #fff;
  backface-visibility: hidden;
  border: 0.3rem solid transparent;
  border-radius: 3rem;
}

.bn37 {
  border-color: transparent;
  background-color: #fff;
  color: #000;
  transition: transform 0.2s cubic-bezier(0.235, 0, 0.05, 0.95);
}
  
.bn37:hover {
  transform: perspective(1px) scale3d(1.044, 1.044, 1) translateZ(0) !important;
}
</style>