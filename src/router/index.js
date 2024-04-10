import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import AboutView from '../views/AboutView.vue'
import GameView from '../views/MainView.vue'
import ChooseWorldView from '../views/ChooseWorldView.vue'
import ChooseNewWorldView from '../views/ChooseNewWorld.vue'
import AdminView from '../views/AdminView.vue'
import MobileAppView from '../views/MobileAppView.vue'
import ForumView from '../views/ForumView.vue'
import ResetPasswordView from '../views/ResetPasswordView.vue'
import ConfirmResetPasswordView from '../views/ConfirmResetPassword.vue'
import TopicForumView from '../views/TopicForumView.vue'
import SetNewPasswordView from '../views/SetNewPasswordView.vue'
import { useAuthStore } from "../stores/useAuth";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: to => {
        // the function receives the target route as the argument
        // we return a redirect path/location here.
        return { path: '/login' }
      },
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/game',
      name: 'game',
      component: GameView
    },
    {
      path: '/about',
      name: 'about',
      component: AboutView
    },
    {
      path: '/choose-world',
      name: 'chooseworld',
      component: ChooseWorldView
    },
    {
      path: '/admin',
      name: 'admin',
      component: AdminView
    },
    {
      path: '/mobile-app',
      name: 'mobileapp',
      component: MobileAppView
    },
    {
      path: '/forum',
      name: 'forum',
      component: ForumView
    },
    {
      path: '/reset-password',
      name: 'resetpassword',
      component: ResetPasswordView
    },
    {
      path: '/set-new-password/:token',
      name: 'setnewpassword',
      component: SetNewPasswordView,
      params: true,
      beforeEnter: (to, from) => {
        return true;
      },
    },
    {
      path: '/confirm-reset',
      name: 'confirmreset',
      component: ConfirmResetPasswordView
    },
    {
      path: '/topic-forum/:id',
      name: 'topicforum',
      component: TopicForumView,
      params: true,
    },
    {
      path: '/choose-new-world',
      name: 'choosenewworld',
      component: ChooseNewWorldView
    }
  ]
})

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/forum', '/topic-forum', '/about', '/mobile-app', '/register', '/reset-password', '/set-new-password/' + to.params.token, '/confirm-reset', '/topic-forum/' + to.params.id];
  const authRequired = !publicPages.includes(to.path);
  if (authRequired && !useAuthStore().loggedIn) {
      next('/login');
  } else {
      next();
  }
});

export default router
