import api from "./api";

class AdminDataService {
    async getUsers(page){
        return api.get(`/game/all-users?page=`+page);
    }

    async getTopics(page){
        return api.get(`/forum/all-topics?page=`+page);
    }

    async getPosts(page){
        return api.get(`/forum/all-posts?page=`+page);
    }

    async getWorlds(page){
        return api.get(`/game/all-worlds?page=`+page);
    }

    async getAllWorlds(){
        return api.get('/game/get-all-worlds');
    }

    async getIslands(page){
        return api.get(`/game/all-islands?page=`+page);
    }

    async updateUser(data, user_id){
        return api.put(`/game/all-users/${user_id}`, {
            banned_from: data.banned_from,
            banned_to: data.banned_to
        });
    }

    async updateTopic(request, topic_id){
        return api.put(`/forum/all-topics/${topic_id}`, {
            name: request.name,
            user_id: request.user_id
        });
    }

    async updatePost(request, post_id){
        return api.put(`/forum/all-posts/${post_id}`, {
            message: request.message,
            user_id: request.user_id
        });
    }

    async updateWorld(request, world_id){
        return api.put(`/game/all-worlds/${world_id}`, {
            status: request.status
        });
    }

    async deleteUser(user_id){
        return api.delete(`/game/all-users/${user_id}`);
    }

    async deleteTopic(topic_id){
        return api.delete(`/forum/all-topics/${topic_id}`);
    }

    async deletePost(post_id){
        return api.delete(`/forum/all-posts/${post_id}`);
    }

    async deleteIslands(island_id){
        return api.delete(`/game/all-islands/${island_id}`);
    }

    async addUser(userData){
        return api.post('/game/all-users', {
            name: userData.name,
            email: userData.email,
            avatar: userData.avatar,
            password: userData.password,
            role_id: userData.role_id
        });
    }

    async addWorld(worldData){
        return api.post('/game/all-worlds', {
            name: worldData.name,
            status: worldData.status,
        });
    }
}

export default new AdminDataService();