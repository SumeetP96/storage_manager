export const LockDateMixin = {
  data() {
    return {
      record : {
        lockDate: '',
        lockDateRaw: '',
      },
      lockDateLoading: false,
      restrictDateEdit: true,
      transactionLockDialog: false
    }
  },

  methods: {
    changeTransactionLockDate() {
      this.settingMenu = false
      this.axios
        .get('/api/settings/get_lock_date')
        .then(response => {
          this.record.lockDateRaw = response.data.lockDateRaw
          this.record.lockDate = response.data.lockDate
          this.transactionLockDialog = true
        })
    },

    updateLockDate() {
      this.lockDateLoading = true
      this.axios
        .post('/api/settings/lock_date/update', this.record)
        .then(response => {
          this.transactionLockDialog = false
          this.$swal('Success!', 'Transactions locked successfully.', 'success')
          this.lockDateLoading = false
          this.record.lockDate = ''
          this.record.lockDateRaw = ''
          localStorage.removeItem('transaction_date')
          this.$router.go(0)
        })
        .catch(({response}) => {
          if (response.status == 422) {
            this.errors = response.data.errors
            this.lockDateLoading = false
          }
        })
    },

    changeDate() {
      this.restrictDateEdit = false
      document.getElementById('lockDateRaw').focus()
    },

  }
}
