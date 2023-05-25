<script>

import User from "./components/User.vue";
import Connexion from "./components/Connexion.vue";

export default {
  name : "App",
  isConnected: true,
  components :{
    User, Connexion
  },
  methods: {
    connect: function () {
      isConnected = this.$store.isconnected();
    }
  }
}

</script>

<template>
  <header>
    <h1>GOTHPLAN MANAGER</h1>
    <nav v-if="isConnected">
      <!-- <router-link to="/">Home</router-link> -->
      <!-- <router-link v-if="!isConnected" to="/connexion">Login</router-link> -->
      <router-link to="/user">user</router-link>
      <router-link to="/clock">Clock</router-link>
      <router-link to="/workingtime">Working Time</router-link>
      <router-link to="/workingtimes">Working Times</router-link>
      <router-link to="/teams">Teams</router-link>
      <router-link to="/chartmanager">Chart Manager</router-link>
    </nav>
  </header>
  <router-view v-if="isConnected"></router-view>
  <Connexion v-if="!isConnected"></Connexion>


  <!--  ---- remplacer <router-view></router-view> par : ----
    <div v-show="isConnected">
    <router-view></router-view>
  </div>
  <div v-show="!isConnected">
    <Connexion></Connexion>
      </div> -->
</template>


<style scoped>

nav > a {
  margin-right: 20px;
}
nav > a:hover {
  color: purple;
  background-color: rgb(0, 220, 147);
}

header {
  color:black;
  position: fixed;
  top: 0;
  width: 100%;
  left:0;
  line-height: 1.5;
  background-color: rgba(176, 240, 255, 0.8);
  z-index: 5;
}


@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }
}
</style>
