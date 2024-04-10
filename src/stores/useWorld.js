import { defineStore } from "pinia";
import axios from "axios";
import WorldDataService from '../services/WorldDataService'

export const useWorldStore = defineStore("world", {
    state: () => ({
        worldData: {},
    }),
    getters: {

    },
    actions: {
        async setWorldData(worldData){
            this.worldData = worldData;
        },

        async storeWorldData(worldData){
            const world = {
                world_id: worldData.world_id,
                name: worldData.name,
                lvl: worldData.lvl,
                exp: worldData.exp,
                rank: worldData.rank,
                battlescore: worldData.battlescore
            };
            await this.setWorldData(world);
        },
    },
    persist: {
        enabled: true
    },
});
