import api from "./api";

class PostsDataService {
    async getAll(){
        return api.get('/forum/all-posts');
    }

    async getTopicPosts(topic_id, page){
        return api.get(`/forum/get-topic-posts/${topic_id}?page=`+page);
    }

    async addNewPost(postData){
        return api.post('/forum/all-posts', {
            message: postData.message,
            likes: postData.likes,
            user_id: postData.user_id,
            topic_id: postData.topic_id
        });
    }

    async updateLikesPost(updateData){
        return api.patch(`/forum/all-posts/${updateData.id}`, {
            likes: updateData.likes
        });
    }
}

export default new PostsDataService();