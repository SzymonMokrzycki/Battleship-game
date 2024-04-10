import { defineStore } from "pinia";

export const useSkillsStore = defineStore("skills", {
    state: () => ({
        skillsData: {},
    }),
    getters: {

    },
    actions: {
        async setSkillData(skillsData){
            this.skillsData = skillsData;
        },

        async storeData(skillsData){
            const skilData = {
                skill_points: skillsData.skill_points,
                shooting: skillsData.shooting,
                accuracy: skillsData.accuracy,
                commanding: skillsData.commanding,
                shipbuilding: skillsData.shipbuilding
            };
            await this.setSkillData(skilData);
        },
    },
    persist: {
        enabled: true
    },
});