export const BackupMixin = {
  data() {
    return {
      backupPath: '',
      backupDialog: false,
      backupLoading: false,
      restrictPathEdit: true,
    }
  },

  methods: {
    openBackupDialog() {
      this.settingMenu = false
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
