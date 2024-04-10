<script setup>
import RegisterCard from '../components/Auth/RegisterCard.vue'
import { useAuthStore } from "../stores/useAuth";
import { useRouter } from "vue-router";
import { storeToRefs } from "pinia";

const router = useRouter();
const userStore = useAuthStore();

const { userData } = storeToRefs(userStore);

if(userData.value.user_role_id == 4 && useAuthStore().loggedIn){
  router.push('/choose-world');
}else if(userData.value.user_role_id != 4 && useAuthStore().loggedIn){
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
    <RegisterCard/>
  </main>
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