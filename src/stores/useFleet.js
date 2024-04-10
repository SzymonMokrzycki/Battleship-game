import { defineStore } from "pinia";

export const useFleetStore = defineStore("fleet", {
    state: () => ({
        id: 0, 
        name: "",
        shipsData: [],
      }),
      getters: {
    
      },
      actions: {
        async setFleetData(id, name, shipsData) {
            this.id = id;
            this.name = name;
            this.shipsData = shipsData;
        }
    },
    persist: {
      enabled: true
    },
});