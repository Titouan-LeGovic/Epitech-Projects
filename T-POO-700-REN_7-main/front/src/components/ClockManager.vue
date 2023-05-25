<template>
  <h1>Clock Manager</h1>
  <div class="wrapper">
    <div class="container">
      <div class="subContainer">
        <label>Clock id</label>
        <input type="number" v-model="getselectedId" placeholder="clock Id">
        <label>Date Start</label>
        <input type="datetime-local" v-model="startDateTime" placeholder="Start Datetime">
        <label>Clock in</label>
        <input type="checkbox" v-model="clockIn" placeholder="Clock In">
        <button @click="createClocks(id_user, startDateTime, clockIn)">GET</button>
      </div>
      <div class="container btns">
        <button @click="clock(getInfo.data.status, getInfo.data.time, getInfo.data.user)">CLOCKIN</button>
        <button @click="refresh(getselectedId)">REFRESH</button>
      </div>
      <div class="subContainer">
        <p v-if="getInfo.data">Clock Id : {{getInfo.data.id}}</p>
        <p v-if="getInfo.data">User ID : {{getInfo.data.user}}</p>
        <p v-if="getInfo.data">Time : {{getInfo.data.time}}</p>
        <p v-if="getInfo.data">Clockin : {{getInfo.data.status}}</p>
    </div>
  </div>
</div>
  <!-- <div id="modifyClock">
    <h2>REFRESH</h2>
    <input type="datetime-local" v-model="updateStart" placeholder="Start Date">
    <input type="datetime-local" v-model="updateEndDate" placeholder="End Date">
    <button @click="updateWorkingTime(updateStart, updateEndDate, id_user, id_workingtime)">VALIDATE</button>
  </div>
  <div id="deleteClock">
    <h2>ACTIVE CLOCK</h2>
    <button @click="deleteWorkingTime(id_workingtime)">VALIDATE</button>
  </div> -->
</template>

<script>
import axios from "axios";
import moment from "moment";

export default {
  data() {
    return {
      // url: import.meta.env.VITE_API_URL,
      url: 'http://localhost:4000/api',
      moment,
      getInfo: [],
      name: "ClockManager",
      // id_user: this.$route.params.id,
      id_user: 1,
      id_clock: 1,
      startDateTime:'',
      clockIn:'',
      updateStartDateTime:'',
      updateClockIn:'',
      response: ''
    }
  },
  
  methods: {
    refresh: function (userId){
      axios
          .get(this.url + '/clocks/' + userId)
          .then(response => (this.getInfo = response.data))
          .catch(error => console.log(error))
    },
    createClocks: function (userId, time, clockin){
      //Add: if time == 0 time = null && clockin = false else clockin = true
      if(clockin!==true) clockin = false;
      const request = {
        "clock":
            {
              "status":clockin,
              "time":time
            }
      }

      axios
          .post(this.url + '/clocks/' + userId, request)
          .then(response => (this.getInfo = response.data))
          .catch(error => console.log(error))
    },

    clock: function(status, time, id) {
      if(status) status = false; else status = true;
      time = this.getInfo.data.time;
      alert(id + "/" + time + "/" + status);
      this.createClocks(id, time, status)
    }
  }
}
</script>

<style scoped>
</style>