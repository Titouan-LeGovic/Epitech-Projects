<template>
  <h1>Register</h1>
  <div class="wrapper">
    <div class="subContainer">
        <input type="text" v-model="createUsername" placeholder="username">
        <input type="text" v-model="createEmail" placeholder="email adress">
        <input type="text" v-model="createPassword" placeholder="password">
        <button @click="createUser(createEmail, createPassword, username)">VALIDATE</button>
        <button class="cnxBtn" @click="emitLoginEvent">Login</button>
    </div>
  </div>

</template>


<script>
import axios from "axios";
import { mapMutations } from 'vuex';
export default {
  data() {
    return {
        // url: import.meta.env.VITE_API_URL,
        url: 'http://localhost:4000/api',
        getInfo: [],
        name: "register",
        response: '',
        user: {username:"", email:"", password:"", role:""}
    }
  },
  methods: {

    emitLoginEvent() {
        this.$emit('loginEvent')
    },
    
    createUser: function (email, password) {
      const request = {
        "user":
            {
              "email": email,
              "password": password,
              "username": username,
              "role": "user"
            }
      }
      axios
        .post(this.url+'/register/', request)
        .then(response => (this.response = response.data))
        .catch(error => console.log(error))

        this.user.email = response.user.email;
        this.user.password = response.user.password;
        this.user.username = response.user.username;
        this.user.role = response.user.role;
        setUser(this.user);
      },

      //...mapMutations('setUser'),

      setUser(user) {
        console.log(user)
        this.$store.commit('setUser', this.user)
        console.log(this.$store.user)
      }
  }
}
</script>


<style scoped>
</style>