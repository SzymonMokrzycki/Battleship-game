import api from "./api";

class WorldDataService {
    async getAll(){
        return api.get('/game/get-all-worlds');
    }

    async getUserLoginWorlds(id){
        return api.get(`/game/user-played-worlds/${id}`);
    }

    async getUserNotLoginWorlds(id){
        return api.get(`/game/user-not-played-worlds/${id}`);
    }

    async storeNewWorld(world_id, user_id){
        return api.post('/game/store-new-world', {
            world_id: world_id,
            user_id: user_id
        });
    }

    async storeUserWorldData(worldData){
        return api.post('/game/store-user-data',{
            user_id: worldData.user_id,
            world_id: worldData.world_id,
            lvl: worldData.lvl,
            rank: worldData.rank,
            exp: worldData.exp,
            battlescore: worldData.battlescore,
        });
    }

    async storeUserSkillsData(skillsDataUser){
        return api.post('/game/store-user-skills',{
            user_id: skillsDataUser.user_id,
            world_id: skillsDataUser.world_id,
            skill_points: skillsDataUser.skill_points,
            shooting: skillsDataUser.shooting,
            accuracy: skillsDataUser.accuracy,
            commanding: skillsDataUser.commanding,
            shipbuilding: skillsDataUser.shipbuilding
        });
    }

    async getWorldData(world_id, user_id){
        return api.get(`/game/get-user-data/${world_id}/${user_id}`);
    }

    async getSkillsData(world_id, user_id){
        return api.get(`/game/get-user-skills/${world_id}/${user_id}`);
    }

    async updateWorldData(worldData, user_id){
        return api.put(`/game/store-user-data/${user_id}`, {
            world_id: worldData.world_id,
            lvl: worldData.lvl,
            rank: worldData.rank,
            exp: worldData.exp,
            battlescore: worldData.battlescore,
        });
    }

    async updateSkillsData(skillsData, user_id){
        return api.put(`/game/store-user-skills/${user_id}`, {
            world_id: skillsData.world_id,
            skill_points: skillsData.skill_points,
            sailing: skillsData.sailing,
            shooting: skillsData.shooting,
            accuracy: skillsData.accuracy,
            commanding: skillsData.commanding,
            shipbuilding: skillsData.shipbuilding
        });
    }
}

export default new WorldDataService();