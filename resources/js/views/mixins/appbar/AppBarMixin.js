import { UserMixin } from "./UserMixin";
import { ThemeMixin } from "./ThemeMIxin";
import { BackupMixin } from "./BackupMixin";
import { LockDateMixin } from "./LockDateMixin";

export const AppBarMixin = {
  mixins: [
    UserMixin,
    ThemeMixin,
    BackupMixin,
    LockDateMixin,
 ],

  data() {
    return {
      company: {},
      companyErrors: [],
      companyDialog: false,
      companyUpdateButton: false,

      settingsMenu: false,
      keyboardShortcutDialog: false
    }
  },

  methods: {
    toggleSettingMenu() {
      this.settingsMenu = !this.settingsMenu
      if (this.settingsMenu) document.getElementById('settingsButton').focus()
      else document.getElementById('settingsButton').blur()
    },

    openCompanyDialog() {
      this.axios.get('/api/company')
        .then(response => {
          this.company = response.data
          this.companyDialog = true
        })
    },

    closeCompanyDialog() {
      this.company = {}
      this.companyErrors = []
      this.companyDialog = false
    },

    updateCompany() {
      this.axios.post('/api/company/update', this.company)
        .then(response => {
          this.companyDialog = false
          this.$swal('Success!', 'Company updated successfully.', 'success')
          this.companyUpdateButton = false
          this.closeCompanyDialog()
        })
        .catch(({response}) => {
          if (response.status == 422) {
            this.companyErrors = response.data.errors
            this.companyUpdateButton = false
          }
        })
    }
  }
}
