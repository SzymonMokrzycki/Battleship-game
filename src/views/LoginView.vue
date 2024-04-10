<script setup>
import LoginCard from '../components/Auth/LoginCard.vue'
import { RouterLink } from 'vue-router'
import { useAuthStore } from "../stores/useAuth";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";

const router = useRouter();
const userStore = useAuthStore();

const { userData } = storeToRefs(userStore);

if(userData.value.user_is_banned == true){
  router.push('/login');
  useAuthStore().logout();
}

if(userData.value.user_role_id == 4 && useAuthStore().loggedIn && userData.value.user_is_banned != true){
  router.push('/choose-world');
}else if(userData.value.user_role_id != 4 && useAuthStore().loggedIn && userData.value.user_is_banned != true){
  router.push('/admin');
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
    <h1 class="text-white text-3xl text-center mt-20">Dont you have an account?</h1>
    <RouterLink to="/register"><button class="bn632-hover bn25 mt-10 w-1/4">PLAY NOW!</button></RouterLink>

    <LoginCard/>
  </main>
</template>

<style>
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

.bn632-hover {
  width: 160px;
  font-size: 16px;
  font-weight: 600;
  color: #fff;
  cursor: pointer;
  margin: 20px;
  height: 55px;
  text-align:center;
  border: none;
  background-size: 300% 100%;
  border-radius: 50px;
  -moz-transition: all .4s ease-in-out;
  -o-transition: all .4s ease-in-out;
  -webkit-transition: all .4s ease-in-out;
  transition: all .4s ease-in-out;
}

.bn632-hover:hover {
  background-position: 100% 0;
  -moz-transition: all .4s ease-in-out;
  -o-transition: all .4s ease-in-out;
  -webkit-transition: all .4s ease-in-out;
  transition: all .4s ease-in-out;
}

.bn632-hover:focus {
  outline: none;
}

.bn632-hover.bn25 {
  background-image: linear-gradient(
    to right,
    #29323c,
    #485563,
    #2b5876,
    #4e4376
  );
  box-shadow: 0 4px 15px 0 rgba(45, 54, 65, 0.75);
}
</style>
