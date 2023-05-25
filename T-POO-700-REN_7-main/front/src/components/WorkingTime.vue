<template>
  <h1>Workingtime Manager</h1>
  <div class="wrapper">
    <div class="container">
      <div class="subContainer">
        <label>Working-Time id</label>
        <input type="number" v-model="selectedId" placeholder="workingtime ID">
      </div>
      <div class="subContainer">
        <label>StartDate</label>
        <input type="datetime-local" v-model="selectedStart" placeholder="Start Date">
      </div>
      <div class="subContainer">
        <label>EndDate</label>
        <input type="datetime-local" v-model="selectedEndDate" placeholder="End Date">
      </div>
      <div class="subContainer">
        <button @click="getWorkingTime(selectedId, id_user)">GET</button>
      </div>
      <div v-if="userRole=='manager'" class="container btns">
      <!-- <div class="container btns"> -->
        <button @click="createWorkingTime(selectedStart, selectedEndDate, id_user)">CREATE</button>
        <button @click="updateWorkingTime(selectedStart, selectedEndDate, id_user, selectedId)">UPDATE</button>
        <button @click="deleteWorkingTime(selectedId)">DELETE</button>
      </div>
    </div>
    <div class="container">
        <p v-if="getInfo.data">Clock ID : {{getInfo.data.id}}</p>
        <p v-if="getInfo.data">User ID : {{getInfo.data.user}}</p>
        <p v-if="getInfo.data">Start : {{getInfo.data.start}}</p>
        <p v-if="getInfo.data">endDate : {{getInfo.data.endDate}}</p>
    </div>
  </div>
</template>

<script>
import { isSet } from "@vue/shared";
import axios from "axios";

export default {
  //role: this.$store.role,
  data() {
    return {
      // url: import.meta.env.VITE_API_URL,
      url: 'http://localhost:4000/api',
      getInfo: [],
      name: "Workingtimes",
      id_user: 1,
      id_workingtime: 1,
      createStart:'',
      createEndDate:'',
      updateStart:'',
      updateEndDate:'',
      // id_user: this.$route.params.id,
      response: '',
    }
  },
  methods: {
    connect: function () {
      userRole = this.$store.getRole();
    },
    getWorkingTime: function (id, userId){

      axios
          .get(this.url + '/workingtimes/' + userId + '/' + id) //Add parameters
          .then(response => (this.response = response.data))
          .catch(error => console.log(error))
    },

    createWorkingTime: function (start, endDate, userId){
      const request = {
        "workingtime":
            {
              "start":start,
              "endDate":endDate
            }
      }

      axios
          .post(this.url + '/workingtimes/' + userId, request) //Add parameters
          .then(response => (this.response = response.data))
          .catch(error => console.log(error))
    },

    updateWorkingTime: function (start, endDate, userId, workingTimeId){
      if(!isSet(userId)) userId = this.id_user;
      const request = {
        "workingtime":
            {
              "start":start,
              "endDate":endDate
            }
          }
      axios
          .put(this.url + '/workingtimes/' + workingTimeId, request) //Add parameters
          .then(response => (this.response = response.data))
          .catch(error => console.log(error))
    },

    deleteWorkingTime: function (workingTimeId){
      axios
          .delete(this.url + '/workingtimes/' + workingTimeId) //Add parameters
          .then(response => (this.response = response.data))
          .catch(error => console.log(error))
    },
  },
}
</script>

<style scoped>

</style>