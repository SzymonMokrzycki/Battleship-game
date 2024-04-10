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
      <h1 class="mb-6 text-xl">Are you interesting of ships and you like competition? 
        <RouterLink to="/register" class="underline hover:text-yellow-300">Join the game</RouterLink> and start the sea adventure!</h1>
      <p class="mb-6">
        The game "Battleship: The Battle of survivor" is a presentation of the cult ship game
        known by entire generations of people in a modern form. It's a web application but also
        for mobile devices to play ships using modern application development technologies.
        Through this game, players can connect with each other and play naval battle of ships
        on a 10x10 board like a piece of paper. The important point is that the players
        they also have the option to play a battle against an artificial intelligence instead of a real player.
        This makes the game more attractive and gives players more freedom in developing tactics
        before facing a real player. The game also introduces an element of competition.
        In the game, players can select and purchase ships of various types from the extensive store inventory.
        They can also upgrade them to make them stronger and destroy enemy ships even more effectively.
        Create an account, choose the ocean region you want to play in and become the best fleet admiral in the ocean.
      </p>
      <div class="flex justify-center mb-6">
        <img src="/chooseworldscreen.png" width="500" height="200" class="aboutImg mr-4 rounded-md border-2 border-white"/>
        <img src="/mainviewscreen.png" width="500" height="200" class="aboutImg rounded-md border-2 border-white"/>
      </div>
      <h1 class="mb-6 text-xl">Create powerful and undefeated fleet</h1> 
      <p class="mb-6">
        Your task will be to climb up the career ladder of a fleet commander and improve your skills to dominate others.
        For this purpose, it will be necessary to create such a powerful fleet that will survive any clash at sea.
        You will be able to buy upgrades for your ships and create and buy new fleets with the special points you collect after each battle.
        Win as many battles as possible to level up and surpass rivals in your region.
        There are 8 geographic ocean regions to choose from where you can start the game.
        If you decide that you don't want to play any region anymore, you can choose another one.
        As the game develops, there may be more of them in the future.
        Simultaneously with reaching a higher level, you will be able to advance to a higher rank, which will also be visible to other players.
        In the game you have access to upgrade equipment that is connected to all the worlds you play in.
      </p>
      <div class="flex justify-center mb-6">
        <img src="/choosefleetviewscreen.png" width="500" height="200" class="aboutImg mr-2 rounded-md border-2 border-white"/>
        <img src="/buyshipscreen.png" width="500" height="200" class="aboutImg mr-2 rounded-md border-2 border-white"/>
        <img src="/gameviewscreen.png" width="500" height="200" class="aboutImg mr-2 rounded-md border-2 border-white"/>
      </div>
      <h1 class="mb-6 text-xl">Training with computer </h1> 
      <p class="mb-6">
        If you are a new player and you are not sure if you are ready to face a real opponent
        you can test your skills in battle with computer. It won't affect your stats.
        The accumulated experience will help you choose the right tactics in battle and eliminate mistakes to tip the scales of victory in your favor.
        Developing the right tactics for each battle will help you reach the top faster.
      </p>
      <div class="flex justify-center mb-6">
        <img src="/gameaiscreen.png" width="500" height="200" class="aboutImg mr-4 rounded-md border-2 border-white"/>
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
