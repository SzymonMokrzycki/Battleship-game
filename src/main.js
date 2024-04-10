import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPersist from 'pinia-plugin-persist'
const pinia = createPinia();
pinia.use(piniaPersist);
import App from './App.vue'
import router from './router'

import './assets/main.css'
import './assets/style.css'
import './registerServiceWorker'

import { library } from '@fortawesome/fontawesome-svg-core'

/* import font awesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

/* import specific icons */
import { faCopyright, faUserSecret, faSailboat, faList, faClockRotateLeft, 
    faEnvelope, faBriefcase, faUsers, faStar, faChartSimple, faWater, faMountainSun,
    faGlobe, faComments, faGear, faThumbsUp, faPlus, faBan, faTrash, faPenToSquare, faCheck, faInfo, faInfoCircle,
    faMedal, faSquare} 
from '@fortawesome/free-solid-svg-icons'
import axios from 'axios'
import setupInterceptors from './services/setupInterceptors';
import {LoadingPlugin} from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import VueAxios from 'vue-axios'
import SmartTable from 'vuejs-smart-table'
import VueTailwindDatepicker from 'vue-tailwind-datepicker'

setupInterceptors();

/* add icons to the library */
library.add(faUserSecret, faCopyright, faSailboat, faList, faClockRotateLeft, 
    faEnvelope, faBriefcase, faUsers, faStar, faChartSimple, faWater, faMountainSun, faGlobe,
    faComments, faGear, faThumbsUp, faPlus, faBan, faTrash, faPenToSquare, faCheck, faInfoCircle,
    faMedal, faSquare)

const app = createApp(App)

app.use(pinia).use(LoadingPlugin, {
    canCancel: false, // default false
    color: "gray",
    loader: "spinner",
    width: 64,
    height: 64,
    backgroundColor: "#ffffff",
    opacity: 0.5,
    zIndex: 999,
})

app.use(router)
app.use(VueAxios, axios).use(SmartTable)
app.component('font-awesome-icon', FontAwesomeIcon)
app.use(VueTailwindDatepicker)
app.mount('#app')
