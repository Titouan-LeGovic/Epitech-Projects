import { createApp } from 'vue'
import App from './App.vue'

import './assets/main.css'
import router from "./router";
import store from "./store/index.js"

createApp(App).use(store).use(router).mount('#app')
