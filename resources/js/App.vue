<template>
  <v-app>
    <v-main>
      <transition name="fade">
        <router-view class="content"></router-view>
      </transition>
    </v-main>
  </v-app>
</template>
