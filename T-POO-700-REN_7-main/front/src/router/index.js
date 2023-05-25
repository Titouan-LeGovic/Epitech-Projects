import {createRouter, createWebHistory} from "vue-router";
import ClockManager from "../components/ClockManager.vue";
import WorkingTime from "../components/WorkingTime.vue";
import WorkingTimes from "../components/WorkingTimes.vue";
import ChartManager from "../components/ChartManager.vue";
import Teams from "../components/Teams.vue";
import Connexion from "../components/Connexion.vue";
import Login from "../components/Login.vue";
import User from "../components/User.vue";

const routes = [
    {
        path : '/clock',
        component : ClockManager,
        name : "clock"
    },
    {
        path : '/workingTime',
        component : WorkingTime,
        name : "workingtime"
    },
    {
        path : '/workingTimes',
        component : WorkingTimes,
        name : "workingtimes"
    },
    {
        path : '/teams',
        component : Teams,
        name : "teams",
    },
    {
        path : '/chartManager',
        component : ChartManager,
        name : "chartmanager"
    },
    {
        path : '/connexion',
        component : Connexion,
        name : "connexion",
    },
    {
        path : '/login',
        component : Login,
        name : "login",
    },
    {
        path : '/user',
        component : User,
        name : "user",
    }
];

const router = createRouter({
    history : createWebHistory(),
    routes,
})

// router.beforeEach((to, from, next) => {
//     let isAuthenticated = false;
//     if (to.name !== 'Login' && !isAuthenticated) next({ name: 'clock' })
//     else next()
// })


export default router;