<template>
  <div>
    <v-app-bar dense app>

      <div v-if="disableBack">
        <v-btn v-if="$route.name != 'home'" tabindex="-1" icon @click="$router.push({ name: backRoute })">
          <v-icon>mdi-arrow-left</v-icon>
        </v-btn>

        <v-btn v-if="$route.name != 'home'" tabindex="-1" icon @click="$router.push({ name: 'home' })">
          <v-icon>mdi-home</v-icon>
        </v-btn>
      </div>

      <div v-else>
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
      </div>

      <div v-if="$route.name != 'home'"
        class="hidden-xs-only grey--text text--lighten-1 ml-3 mr-5 font-weight-thin"
        style="font-size: 1.5rem">
        |
      </div>

      <v-app-bar-title class="ml-2 ml-sm-0">Storage Manager</v-app-bar-title>

      <v-spacer></v-spacer>

      <v-btn text v-if="theme == 'dark'" @click="toggleApplicationTheme('light')" tabindex="-1"
        v-shortkey.once="['alt', 'h']" @shortkey="toggleApplicationTheme('light')" color="amber lighten-3">
          <v-icon class="text-h6 mr-2">mdi-weather-sunny</v-icon> light
      </v-btn>

      <v-btn text v-else @click="toggleApplicationTheme('dark')" tabindex="-1"
        v-shortkey.once="['alt', 'h']" @shortkey="toggleApplicationTheme('dark')">
          <v-icon class="text-h6 mr-2">mdi-weather-night</v-icon> dark
      </v-btn>

      <!-- Settings Menu -->
      <v-menu offset-y :value="settingsMenu">
        <template v-slot:activator="{ on, attrs }">
          <v-btn text v-bind="attrs" v-on="on" id="settingsButton"
            :color="$vuetify.theme.dark ? 'primary lighten-2' : 'indigo'" tabindex="-1"
            v-shortkey.once="['alt', 't']" @shortkey="toggleSettingMenu()" @click="toggleSettingMenu()">
              <v-icon class="mr-2 text-h6">mdi-cog-outline</v-icon> Settings
          </v-btn>
        </template>

        <v-list dense>

          <v-list-item link v-shortkey.once="['alt', 'k']"
            @shortkey="keyboardShortcutDialog = true; toggleSettingMenu()"
            @click="keyboardShortcutDialog = true; toggleSettingMenu()">
            <v-list-item-icon><v-icon>mdi-keyboard</v-icon></v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title class="pr-5">Keyboard shortcuts</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-list-item link v-shortkey.once="['alt', 'b']"
            @shortkey="openBackupDialog(); toggleSettingMenu()">
            <v-list-item-icon><v-icon>mdi-cloud-upload</v-icon></v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Backup data</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-list-item link @click="changePassword(); toggleSettingMenu()">
            <v-list-item-icon><v-icon>mdi-form-textbox-password</v-icon></v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title>Change password</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

          <v-list-item link v-shortkey.once="['alt', 'o']"
            @shortkey="logout(); toggleSettingMenu()"
            @click="logout(); toggleSettingMenu()">
            <v-list-item-icon><v-icon class="error--text">mdi-power</v-icon></v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title class="error--text">Logout</v-list-item-title>
            </v-list-item-content>
          </v-list-item>

        </v-list>
      </v-menu> <!-- / Settings Menu End -->

    </v-app-bar>

    <!-- Backup Dialog -->
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
    </v-dialog> <!-- / Backup Dialog End -->

    <!-- Keyboard Shortct Dialog -->
    <v-dialog v-model="keyboardShortcutDialog" max-width="1000">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>Keyboard Shortcuts</div>
          <v-btn icon @click="keyboardShortcutDialog = false">
              <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text class="pt-5 pb-10">
          <v-row>
            <!-- Left side -->
            <v-col cols="6" class="pr-6">
              <div class="pb-2 subtitle-2">Settings shortcuts</div>
              <table class="keyboard-shortcut-table">
                <tr class="subtitle-1">
                  <td class="font-weight-bold" style="width: 60%">Toggle theme</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + H</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold" style="width: 60%">Toggle settings</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + S</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">View shortcuts</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + T + K</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Backup data</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + T + B</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Logout</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + T + O</td>
                </tr>
              </table>

              <div class="mt-8 pb-2 subtitle-2">Navigation shortcuts</div>
              <table class="keyboard-shortcut-table">
                <tr class="subtitle-1">
                  <td class="font-weight-bold" style="width: 60%">Goto Home</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Home</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Goto previous page</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + Q</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Goto next page</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + Left</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Reload page</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Ctrl + R</td>
                </tr>
              </table>
            </v-col> <!-- / Left side end -->

            <!-- Right Side -->
            <v-col cols="6" class="pl-6">
              <div class="pb-2 subtitle-2">Table / Form shortcuts</div>
              <table class="keyboard-shortcut-table">
                <tr class="subtitle-1">
                  <td class="font-weight-bold" style="width: 60%">New record</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + N</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold" style="width: 60%">Refresh table</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + R</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Focus search</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + S</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Load more</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + L</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">(Form) Save / Update</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + S</td>
                </tr>
              </table>

              <div class="mt-8 pb-2 subtitle-2">Dashboard shortcuts</div>
              <table class="keyboard-shortcut-table">
                <tr class="subtitle-1">
                  <td class="font-weight-bold" style="width: 60%">Purchase</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + P</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Sales</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + S</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Inter godown</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + I</td>
                </tr>

                <tr class="subtitle-1">
                  <td class="font-weight-bold">Reports</td>
                  <td :class="$vuetify.theme.dark ? '' : 'black--text'">Alt + R</td>
                </tr>
              </table>
            </v-col> <!-- / Right Side end -->
          </v-row>
        </v-card-text>

      </v-card>
    </v-dialog> <!-- / Backup Dialog End -->

  </div>
</template>

<script>
import { CommonMixin } from '../mixins/CommonMixin'
import { AppBarMixin } from '../mixins/appbar/AppBarMixin'

export default {
  props: ['backRoute', 'disableBack'],

  mixins: [CommonMixin, AppBarMixin],

  mounted() {
    this.setApplicationTheme()
  },
}
</script>
