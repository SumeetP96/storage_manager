<template>
  <div>
    <v-app-bar dense app>

      <v-btn v-if="$route.name != 'home'" tabindex="-1" icon
        v-shortkey.once="['alt', 'q']" @shortkey="$router.push({ name: backRoute })"
        @click="$router.push({ name: backRoute })">
          <v-icon>mdi-arrow-left</v-icon>
      </v-btn>

      <v-btn v-if="$route.name != 'home'" tabindex="-1"
        v-shortkey.once="['home']" @shortkey="$router.push({ name: 'home' })"
        icon @click="$router.push({ name: 'home' })">
          <v-icon>mdi-home</v-icon>
      </v-btn>

      <div v-if="$route.name != 'home'"
        class="hidden-xs-only grey--text text--lighten-1 ml-3 mr-5 font-weight-thin"
        style="font-size: 1.5rem">
        |
      </div>

      <v-app-bar-title class="ml-2 ml-sm-0">Storage Manager</v-app-bar-title>

      <v-spacer></v-spacer>

      <v-btn text class="mr-1" tabindex="-1" :color="$vuetify.theme.dark ? 'primary' : 'indigo'"
        v-shortkey.once="['alt', 'b']" @shortkey="openBackupDialog()"
        @click="openBackupDialog()">
        <v-icon class="text-h6 mr-2">mdi-cloud-upload</v-icon> backup
      </v-btn>

      <v-btn text v-if="theme == 'dark'" @click="toggleApplicationTheme('light')" tabindex="-1"
        v-shortkey.once="['alt', 'h']" @shortkey="toggleApplicationTheme('light')">
          <v-icon class="text-h6 mr-2">mdi-weather-sunny</v-icon> light
      </v-btn>

      <v-btn text v-else @click="toggleApplicationTheme('dark')" tabindex="-1"
        v-shortkey.once="['alt', 'h']" @shortkey="toggleApplicationTheme('dark')">
          <v-icon class="text-h6 mr-1">mdi-weather-night</v-icon> dark
      </v-btn>
    </v-app-bar>

    <v-dialog v-model="backupDialog" max-width="600"
      @click:outside="restrictPathEdit = true; errors.path = []"
      @keydown.esc="restrictPathEdit = true; errors.path = []">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>Backup Data</div>
          <v-btn icon
            @click="backupDialog = false; restrictPathEdit = true; errors.path = []">
              <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text class="py-5">
          <div class="subtitle-1 pb-2" :class="$vuetify.theme.dark ? 'white--text' : 'black--text'">
            You data will be backed up in this location
          </div>

          <v-text-field
            v-model="backupPath"
            id="backupPath"
            outlined
            dense
            :filled="restrictPathEdit"
            :readonly="restrictPathEdit"
            :error-messages="errors.path"
            placeholder="Path e.x. C:\Users\username\Desktop">
          </v-text-field>

          <div class="d-flex align-center justify-center mt-5">
            <v-btn dark color="error" class="mr-2"
              @click="changePath()">
                change path
            </v-btn>
            <v-btn dark color="indigo" class="ml-2"
              :loading="backupLoading" @click="backupData()">
                <v-icon class="mr-3 text-h6">mdi-cloud-upload</v-icon>
                run backup
            </v-btn>
          </div>
        </v-card-text>

      </v-card>
    </v-dialog>

  </div>
</template>

<script>
import { CommonMixin } from '../mixins/CommonMixin'
import { AppBarMixin } from '../mixins/appbar/AppBarMixin'

export default {
  props: ['backRoute'],

  mixins: [CommonMixin, AppBarMixin],

  mounted() {
    this.setApplicationTheme()
    this.fetchTransactionDate()
  }
}
</script>
