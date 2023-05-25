import { createStore } from 'vuex'

export default createStore({
    state () {
        return {
            token: "null",
            email: "null",
            username: "null",
            role: "none",
        }
    },
    mutations: {
        set (newValue) {
            this.token = newValue
        },
        isconnected () {
            if(this.token != null && this.email != null && this.username != null) return true; else return false;
        },
        getRole() {
            return this.role;
        }
    }
})