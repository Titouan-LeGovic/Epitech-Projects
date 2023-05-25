<template>
  <div class="container">
    <div class="wrapper">
      <div class="grid styleBox">
        <h2>GET USER WITH ID</h2>
        <input type="number" v-model="getselectedId">
        <button @click="getUserByID(getselectedId)">VALIDATE</button>
        <p v-if="getInfo.data">Username : {{ getInfo.data.username }}</p>
        <p v-if="getInfo.data">Email : {{ getInfo.data.email }}</p>
      </div>
      <!-- <div v-if="role=='admin'" class="grid2 styleBox"> -->
      <div class="grid2 styleBox">
        <h2>CREATE USER WITH ID</h2>
        <input type="text" v-model="createUsername">
        <input type="text" v-model="createEmail">
        <button @click="createUser(createUsername, createEmail)">VALIDATE</button>
      </div>
      <!-- <div v-if="role=='admin'" class="grid3 styleBox"> -->
      <div class="grid3 styleBox">
        <h2>UPDATE USER WITH ID</h2>
        <input type="number" v-model="updateselectedId">
        <input type="text" v-model="updateUsername">
        <input type="text" v-model="updateEmail">
        <button @click="updateUser(updateUsername, updateEmail, getselectedId)">VALIDATE</button>
      </div>
      <!-- <div v-if="role=='admin'" class="grid4 styleBox"> -->
      <div class="grid4 styleBox">
        <h2>DELETE USER WITH ID</h2>
        <input type="number" v-model="deleteselectedId">
        <button @click="deleteUser()">VALIDATE</button>
      </div>
    </div>
  </div>

</template>

<script>
import { processExpression } from "@vue/compiler-core";
import axios from "axios";
import store from "../store/index";

export default {
  //role: this.$store.role, 
  data() {
    return {
      url: import.meta.env.VITE_API_URL,
      getInfo: [],
      // for request get with id
      getselectedId: '',

      //for create a user
      createUsername: '',
      createEmail: '',

      //for update user
      updateselectedId: '',
      updateUsername: '',
      updateEmail: '',

      //for delete
      deleteselectedId: '',
    }
  },

  methods: {
    createUser: function (username, email) {
      const request = {
        "user":
            {
              "username": username,
              "email": email
            }
      }
      axios
          .post(this.url + '/users/', request)
          .then(response => (this.response = response.data))
          .catch(error => console.log(error))
    },
    updateUser: function (username, email, id) {
      const request = {
        "user":
            {
              "username": username,
              "email": email
            }
      }
      axios
          .put(this.url + '/users/' + this.updateselectedId, request)
          .then(response => (this.response = response.data))
          .catch(error => console.log(error))
    },
    getUser: function (username, email) {
      axios
          .get(this.url + '/users?email=' + email + '&username=' + username)
          .then(response => (this.response = response.data))
          .catch(error => console.log(error))
    },
    getUserByID: function (id) {
      axios
          .get(this.url + '/users/' + id)
          .then(response => {
            this.getInfo = response.data;
          })
          .catch(error => console.log(error))
    },
    deleteUser: function () {
      axios
          .delete(this.url + '/users/' + this.deleteselectedId)
          .then(response => (this.response = response.data))
          .catch(error => console.log(error))
    },
  }
}
</script>

<style scoped>

.container {
  margin-bottom: 5%;
}

.wrapper {
  display: grid;
  grid-auto-flow: column;
  grid-template-rows: 1fr;
  grid-template-columns: 1fr 1fr 1fr 1fr;

  height: 100%;

}

.grid {

  background-color: #1fb1e2;
  grid-row-start: 1;
  grid-column-start: 1;


}

.grid2 {

  background-color: #6DBA86;
  grid-row-start: 1;
  grid-column-start: 2;

}

.grid3 {

  background-color: #FDC9DB;
  grid-row-start: 1;
  grid-column-start: 3;
}

.grid4 {

  background-color: #C77DE7;
  grid-row-start: 1;
  grid-column-start: 4;
}

.styleBox {
  width: 300px;
  padding: 50px;
}

</style>