import api from "./api";

class UserDataService{
    async updateUser(user, user_id){
        return api.put(`/game/update-user/${user_id}`, {
            name: user.name,
            email: user.email,
            avatar: user.avatar
        })
    }

    async updateUserPassword(user, user_id){
        return api.patch(`/game/all-users/${user_id}`, {
            password: user.password
        })
    }
}

export default new UserDataService();