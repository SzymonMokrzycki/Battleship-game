<script setup>
import ResetCard from '../components/Auth/ResetCard.vue'
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
      <ResetCard/>
    </main>
    
  </template>
  
  <style>
  @media (min-width: 500px) {
    .about {
      min-height: 100vh;
      display: flex;
      align-items: center;
    }
  }
  </style>
  