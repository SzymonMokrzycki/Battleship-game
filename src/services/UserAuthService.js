import api from "./api";

class UserAuthService{
    async login(user) {
        const response = await api.post('/login', {
            email: user.email,
            password: user.password
        });
        return response.data;
    }

    async logout() {
        const response = await api.post('/logout');
        return response.data;
    }

    async register(user) {
        return api.post('/register', {
            name: user.name,
            email: user.email,
            password: user.password,
        });
    }

    async userProfile() {
        const response = await api.get('/user');
        return response.data;
    }

    async sendPasswordResetEmail(email) {
        return api.post('/reset-password-request', {
            email: email.email
        });
    }

    async passwordResetUpdate(updatePasswordData, token) {
        const response = await api.post('/change-password', {
            email: updatePasswordData.email,
            token: token,
            password: updatePasswordData.password,
            password_confirmation: updatePasswordData.confirmpass
        });
        return response.data;
    }
}

export default new UserAuthService();