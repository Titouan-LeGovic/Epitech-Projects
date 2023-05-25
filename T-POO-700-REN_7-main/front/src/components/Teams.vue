<template>
  <h1>Teams Manager</h1>
    <div class="wrapper">
      <div class="container">
        <div class="subContainer">
          <label>Team id</label>
          <input type="number" v-model="selectedId">
          <label>Team name</label>
          <input type="text" v-model="selectedName">
          <label>Team description</label>
          <input type="text" v-model="selectedDescription">
          <label>Team owner</label>
          <input type="text" v-model="selectedOwner">
        </div>
        <div class="subContainer">
          <button @click="getTeam(selectedId)">GET</button>
        </div>
        <div v-if="userRole=='manager'" class="container btns">
        <!-- <div class="container btns"> -->
            <button @click="createTeam(selectedName, selectedDescription, selectedOwner)">CREATE</button>
            <button @click="updateTeam(selectedName, selectedDescription, selectedOwner, selectedId)">UPDATE</button>
            <button @click="deleteTeam(selectedId)">DELETE</button>
        </div>
      </div>
        <div class="container">
            <div class="subContainer">
              <p v-if="getInfo.data">TEAM (id:{{ getInfo.data.id }})</p>
              <p v-if="getInfo.data">Name : {{ getInfo.data.name }}</p>
              <p v-if="getInfo.data">Description : {{ getInfo.data.description }}</p>
              <p v-if="getInfo.data">Owner : {{ getInfo.owner.data.username }} (with id: {{ getInfo.data.user }})</p>
            </div>
        </div>
    </div>
  
  </template>
  
  <script>
  import { processExpression } from "@vue/compiler-core";
  import axios from "axios";
  
  export default {
    //role: this.$store.role, 
    data() {
      return {
        // url: import.meta.env.VITE_API_URL,
        url: 'http://localhost:4000/api',
        getInfo: [],
      }
    },
  
    methods: {
    connect: function () {
      userRole = this.$store.getRole();
    },
      createTeam: function (name, description, owner) {
        const request = {
          "team":
              {
                "name": name,
                "description": description,
                "user": owner
              }
        }
        axios
            .post(this.url + '/teams/', request)
            .then(response => (this.response = response.data))
            .catch(error => console.log(error))
      },
      updateTeam: function (name, description, owner, id) {
        const request = {
          "team":
              {
                "name": name,
                "description": description,
                "user": owner
              }
        }
        axios
            .put(this.url + '/teams/' + id, request)
            .then(response => (this.response = response.data))
            .catch(error => console.log(error))
      },
      getTeam: function (id) {
        axios
            .get(this.url + '/teams/' + id)
            .then(response => {
              this.getInfo = response.data;
          console.log(this.getInfo.data.user);
          this.getOwner(this.getInfo.data.user);
            })
        .catch(error => console.log(error))
      },
      getOwner: function (idOwner) {
        axios
            .get(this.url + '/users/' + idOwner)
            .then(response => {
              this.getInfo.owner = response.data;
          console.log(this.getInfo.owner.data.username);
            })
            .catch(error => console.log(error))
      },
      deleteTeam: function (id) {
        axios
            .delete(this.url + '/teams/' + id)
            .then(response => (this.response = response.data))
            .catch(error => console.log(error))
      },
    }
  }
  </script>
  
  <style scoped>
  </style>