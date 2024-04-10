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
    <div class="text-white text-center">
      <h1 class="text-xl mb-6">Game with PWA (Progressive Web Application)</h1>
      <p class="mb-6">
        This game uses PWA technology. This means that it is adapted to be used on mobile devices.
        Provides responsiveness and limited offline work in the absence of an internet connection.
        The PWA application behaves like a mobile application but it is a regular web application.
        It is easy to use and install.
        To install it, just click the browser's button to install as an application on your device.
        After installation, a shortcut will be created on your device's desktop or home screen.
        Below is an example of installation in the Microsoft Edge browser.
        
      </p>
      <div class="flex justify-center">
        <img src="/pwainstalledgescreen.png" width="300" height="100" class="aboutImg mr-4 rounded-md border-2 border-white"/>
      </div>
    </div>
  </main>
</template>
  
<style>
@media (min-width: 500px) {
  .aboutImg {
    max-width: 40%;
    height: auto;
  }
}
</style>
  