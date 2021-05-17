<template>
  <div>
    <v-app-bar dense app>

      <v-btn v-if="$route.name != 'home'"
        icon @click="$router.push({ name: backRoute })" tabindex="-1"
        class="d-none d-sm-flex">
          <v-icon>mdi-arrow-left</v-icon>
      </v-btn>

      <v-btn v-if="$route.name != 'home'"
        icon @click="$router.go(0)" tabindex="-1"
        class="d-none d-sm-flex">
          <v-icon>mdi-refresh</v-icon>
      </v-btn>

      <v-btn v-if="$route.name != 'home'"
        icon @click="$router.push({ name: 'home' })" tabindex="-1">
          <v-icon>mdi-home</v-icon>
      </v-btn>

      <div v-if="$route.name != 'home'"
        class="hidden-xs-only grey--text text--lighten-1 ml-4 mr-5 font-weight-thin" style="font-size: 1.5rem">|</div>

      <v-app-bar-title class="ml-2 ml-sm-0">Storage Manager</v-app-bar-title>

      <v-spacer></v-spacer>

      <v-btn text class="mr-1 hidden-xs-only" tabindex="-1" :color="$vuetify.theme.dark ? 'primary' : 'indigo'"
        @click="openBackupDialog()">
        <v-icon class="text-h6 mr-2">mdi-cloud-upload</v-icon> backup
      </v-btn>

      <!-- Mobile -->
      <v-btn icon class="hidden-sm-and-up" tabindex="-1" :color="$vuetify.theme.dark ? 'primary' : 'indigo'">
        <v-icon class="text-h6">mdi-backup-restore</v-icon>
      </v-btn>

      <div class="hidden-xs-only">
        <v-btn text v-if="theme == 'dark'" @click="toggleApplicationTheme('light')" tabindex="-1">
            <v-icon class="text-h6 mr-2">mdi-weather-sunny</v-icon> light
        </v-btn>

        <v-btn text v-else @click="toggleApplicationTheme('dark')" tabindex="-1">
            <v-icon class="text-h6 mr-1">mdi-weather-night</v-icon> dark
        </v-btn>
      </div>

      <!-- Mobile -->
      <div class="hidden-sm-and-up">
        <v-btn icon v-if="theme == 'dark'" @click="toggleApplicationTheme('light')" tabindex="-1">
          <v-icon class="text-h6">mdi-weather-sunny</v-icon>
        </v-btn>

        <v-btn icon v-else @click="toggleApplicationTheme('dark')" tabindex="-1">
          <v-icon class="text-h6">mdi-weather-night</v-icon>
        </v-btn>
      </div>
    </v-app-bar>

    <v-dialog v-model="backupDialog" max-width="600"
      @click:outside="restrictPathEdit = true" @keydown.esc="restrictPathEdit = true">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>Backup Data</div>
          <v-btn icon @click="backupDialog = false; restrictPathEdit = true"><v-icon>mdi-close</v-icon></v-btn>
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

export default {
  data() {
    return {
      backupPath: '',
      backupDialog: false,
      backupLoading: false,
      restrictPathEdit: true,
    }
  },

  mixins: [CommonMixin],

  props: ['backRoute'],

  mounted() {
    this.setApplicationTheme()
  },

  methods: {
    toggleApplicationTheme(theme) {
      if (theme == 'light') localStorage.setItem('dark_theme', false)
      else if (theme == 'dark') localStorage.setItem('dark_theme', true)
      this.theme = theme
      this.setApplicationTheme()
    },

    setApplicationTheme() {
      let theme = (localStorage.getItem('dark_theme') == 'true') ? 'dark' : 'light'
      if (theme == 'dark') {
        document.body.style.backgroundColor = '#121212';
        this.$vuetify.theme.dark = true
      } else {
        this.$vuetify.theme.dark = false
        document.body.style.backgroundColor = '#ECEFF1';
      }
      this.theme = theme
    },

    openBackupDialog() {
      this.axios
        .get('/api/backup/get_path')
        .then(response => {
          this.backupPath = response.data.path
          this.backupDialog = true
        })
    },

    backupData() {
      this.backupLoading = true
      this.axios
        .get(`/api/backup/run?path=${this.backupPath}`)
        .then(response => {
          console.log(response.data);
          this.backupDialog = false
          this.$swal('Complete!', 'Data backup successful.', 'success')
          this.backupLoading = false
          this.backupPath = ''
        })
        .catch(({response}) => {
          if (response.status == 422) {
            this.errors = response.data.errors
            this.backupLoading = false
          }
        })
    },

    changePath() {
      this.restrictPathEdit = false
      document.getElementById('backupPath').focus()
    }
  }
}
</script>
