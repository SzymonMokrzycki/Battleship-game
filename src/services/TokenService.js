class TokenService{

    getLocalRefreshToken() {
        const user = JSON.parse(localStorage.getItem("user"));
        return user?.refreshToken;
    }

    getLocalAccessToken() {
        const user = JSON.parse(localStorage.getItem("user"));
        return user?.access_token;
    }

    updateLocalAccessToken(token) {
        let user = JSON.parse(localStorage.getItem("user"));
        user.access_token = token;
        localStorage.setItem("user", JSON.stringify(user));
    }

    setUser(user) {
        localStorage.setItem("user", JSON.stringify(user));
    }

    removeUserAccessToken() {
        localStorage.removeItem("token");
    }
}

export default new TokenService();