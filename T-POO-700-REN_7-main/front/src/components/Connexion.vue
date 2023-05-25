<template>
  <div class="container">
    <div v-show="login === true" ><LogIn @registerEvent="registerActive" /></div>
    <div v-show="login === false" ><Register @loginEvent="loginActive" /></div>
  </div>
</template>


<script>
import LogIn from "./LogIn.vue";
import Register from "./Register.vue";
// import store from '../store.js';
// import { useStore } from '@/stores'


export default {
  
  // setup() {
  //   const user = useStore()

  //   user.setUser()
  // },

  components :{
    LogIn, Register
  },

  data() {
    //user: store.state.user
    return {
      login: true,
      name: "connexion",
       // user: {username:"", email:"", token:"", role:""}
    }
  },
  methods: {

    registerActive() {
        this.login = false;
        //console.log(this.$store.user.username)
    },

    loginActive() {
        this.login = true;
        //console.log(this.$store.user.username)
    },

    // setUser(user) {
    //     this.$store.commit(this.user)
    //     console.log(this.$store.user.username)
    // }
  }
}
</script>


<style scoped>
</style>