<template>
  <h1>Workingtimes Manager</h1>
  <div class="wrapper">
    <div class="container">
      <div class="subContainer">
        <label>Working-Time id</label>
        <input type="number" v-model="selectedId" placeholder="Workingtime ID">
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
      <button @click="getWorkingTimes(selectedId, userId, selectedStart, selectedEndDate)">Search</button>
    </div>
  </div>
    <div v-if="userRole=='manager'" class="container">
      <p v-if="getInfo.data">Workingtime ID : {{ getInfo.data.id }}</p>
      <p v-if="getInfo.data">Start : {{ getInfo.data.start }}</p>
      <p v-if="getInfo.data">EndDate : {{ getInfo.data.endDate }}</p>
      <!-- <div v-for="item in getInfo.data">
        <p v-if="getInfo.data[item]">Workingtime ID : {{ getInfo.data[item].id }}</p>
        <p v-if="getInfo.data[item]">Start : {{ getInfo.data[item].start }}</p>
        <p v-if="getInfo.data[item]">EndDate : {{ getInfo.data.data[item].endDate }}</p>
      </div> -->
      </div>
  </div>
</template>

<script>
import Moment from 'moment';
import 'moment/locale/fr';
Moment.locale('fr');
import axios from "axios";

export default {
  // url: import.meta.env.VITE_API_URL,
  url: 'http://localhost:4000/api',
  userId: '',
  name: "WorkingTime",
  response: '',
  workingTimes: [],
  //role: this.$store.role,
  data() {
    return {
      getInfo: [],
      getData: [],
      userId: 1,
      start: '',
      endDate: '',
    }
  },
  methods: {
    connect: function () {
      userRole = this.$store.getRole();
    },
    getWorkingTimes: function (id, userId, start, endDate) {
      if(userId == '') userId = this.id_user;

      // start="2022-10-28 13:51:00";
      // endDate="2022-10-28 13:51:00";

      if((start != '' && endDate != '') && typeof id === 'undefined') {

        alert(this.url + '/workingtimes/' + userId + "?start=" + start + "&endDate=" + endDate)

        axios
          .get(this.url + '/workingtimes/' + userId + "?start=" + start + "&endDate=" + endDate)
          .then(response => {
            console.log(response.data)
            this.getData = response.data
            this.getInfo = response
          })
          .catch(error => console.log(error))
      }
      else if(id != '')
      {
      axios
          .get(this.url + '/workingtimes/' + userId + '/' + id)
          .then(response => {
            console.log(response.data)
            this.getInfo = response.data
          })
          .catch(error => console.log(error))
      }
    },
  }
}

</script>

<!-- <style scoped>
.btns {
  display: flex;
  margin-left: auto;
  margin-right: 10px;
  width: 2.5em;
}
.container {
  display: block;
  margin-top: auto;
  margin-bottom: auto;
}
.container .btns {
  display: flex;
  margin-left: auto;
  margin-right: 10px;
  width: 5em;
}
.subContainer {
  display: grid;
  margin-top: 2em;
  margin-bottom: 2em;
}
.wrapper {
  display: block;
  margin-top: 2em;
  margin-bottom: 2em;
  width: 80%;
}
#createWT {
  font-size: xx-large;
  font-weight: bolder;
  width: 1.5em;
  height: 1.5em;
  text-align: center;
  justify-content: center;
}
button {
  margin-left: auto;
  margin-right: auto;
  width: fit-content;
  height: fit-content;
}
input {
  width: fit-content;
  height: fit-content;
} 
</style> -->