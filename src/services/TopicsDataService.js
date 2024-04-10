import api from "./api";

class TopicsDataService {
    async getAll(page){
        return api.get('/forum/all-topics?page='+page);
    }

    async getDetails(id){
        return api.get(`/forum/all-topics/${id}`);
    }

    async addTopic(topicData){
        return api.post('/forum/all-topics', {
            name:topicData.name,
            user_id: topicData.user_id
        });
    }
}

export default new TopicsDataService();