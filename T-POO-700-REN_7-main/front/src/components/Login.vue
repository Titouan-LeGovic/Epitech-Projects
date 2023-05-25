<template>
  <h1>Login</h1>
  <div class="wrapper">
    <div class="subContainer">
        <input type="text" v-model="createEmail" placeholder="email adress">
        <input type="text" v-model="createPassword" placeholder="password">
        <button @click="login(createEmail, createPassword)">VALIDATE</button>
        <button class="cnxBtn" @click="emitRegisterEvent">Register</button>
    </div>
  </div>
</template>


<script>
import axios from "axios";
import jwt from "jwt-decode";


export default {
  data() {
    return {
      // url: import.meta.env.VITE_API_URL,
      url: 'http://localhost:4000/api',
      getInfo: [],
      name: "log-in",
      response: '',
    }
  },
  methods: {

    emitRegisterEvent() {
      this.$emit('registerEvent');
    },

    login: async function (email, password) {
      const request =

          {
            "email": email,
            "password": password

          }
      await axios
          .post(this.url + '/login/', request)
          .then((response) => { this.response = jwt(response.data, {complete: true});})
          .catch(error => console.log(error))



      // this.$store.email = this.response.email;
      // this.$store.username = this.response.username;
      // this.$store.role = this.response.role;

      // console.log(this.$store)
    }
  }
}
</script>


<style scoped>
</style>