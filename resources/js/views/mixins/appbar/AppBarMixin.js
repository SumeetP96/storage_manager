import { UserMixin } from "./UserMixin";
import { ThemeMixin } from "./ThemeMIxin";
import { BackupMixin } from "./BackupMixin";

export const AppBarMixin = {
  mixins: [
    UserMixin,
    ThemeMixin,
    BackupMixin,
 ],

  data() {
    return {
      settingsMenu: false,
      keyboardShortcutDialog: false
    }
  },

  methods: {
    toggleSettingMenu() {
      this.settingsMenu = !this.settingsMenu
      if (this.settingsMenu) document.getElementById('settingsButton').focus()
      else document.getElementById('settingsButton').blur()
    }
  }
}
