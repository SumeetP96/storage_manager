export const LockDateMixin = {
  data() {
    return {
      lockDate: '',
      lockDateRaw: '',
      lockDateLoading: false,
      restrictDateEdit: true,
      transactionLockDialog: false
    }
  },

  methods: {
    changeTransactionLockDate() {
      this.settingMenu = false
      // this.axios
      //   .get('/api/settings/get_lock_date')
      //   .then(response => {
      //     this.lockDate = response.data.date
          this.transactionLockDialog = true
        // })
    },

    updateLockDate() {
      this.lockDateLoading = true
      // this.axios
      //   .get(`/api/settings/lock_date/update?date=${this.lockDate}`)
      //   .then(response => {
      //     this.transactionLockDialog = false
      //     this.$swal('Success!', 'Transactions locked successful.', 'success')
      //     this.lockDateLoading = false
      //     this.backupPath = ''
      //   })
      //   .catch(({response}) => {
      //     if (response.status == 422) {
      //       this.errors = response.data.errors
      //       this.lockDateLoading = false
      //     }
      //   })
    },

    changeDate() {
      this.restrictDateEdit = false
      document.getElementById('lockDateRaw').focus()
    },

  }
}
