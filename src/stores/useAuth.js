import { defineStore } from "pinia";
import axios from "axios";
import { useWorldStore } from "./useWorld";
import { useSkillsStore } from "./useSkills";
import { useFleetStore } from "./useFleet";
import UserAuthService from "../services/UserAuthService";
import TokenService from "../services/TokenService";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        loggedIn: localStorage.getItem("token") ? true : false,
        userData: {},
      }),
      getters: {
    
      },
      actions: {
        async setUserData(userData) {
          this.userData = userData;
        },
        
        async login(credentials) {
          const response = await UserAuthService.login(credentials);
          if (response) {
            const token = `Bearer ${response.authorisation.token}`;
            localStorage.setItem("token", token);
            axios.defaults.headers.common["Authorization"] = token;
            const userData = await this.fetchUser();
            const userDataForStoring = {
                user_id: userData.user.id,
                user_name: userData.user.name,
                user_role_id: userData.user.role_id,
                user_role: userData.user.role,
                user_avatar: userData.user.avatar,
                user_banned_time: userData.user.banned_time,
                user_is_banned: userData.user.isBanned,
                user_is_in_battle: false,
                game_with_ai: false,
                current_query_id: 0
            };
            await this.setUserData(userDataForStoring);
            this.loggedIn = true;
        }
    },

    async logout() {
        const response = await UserAuthService.logout();
        if (response) {
          TokenService.removeUserAccessToken();
          this.$reset();
          useWorldStore().$reset();
          useSkillsStore().$reset();
          useFleetStore().$reset();
          this.loggedIn = false;
        }
    },

    async fetchUser() {
        return await UserAuthService.userProfile();
      },
    },
    persist: {
      enabled: true
    },
});