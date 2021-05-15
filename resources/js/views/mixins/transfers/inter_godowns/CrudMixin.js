export const CrudMixin = {
  methods: {
    createDialogRecord(payload) {
      this.dialogRecord.is_account = false

      this.dialogCreateButton = true
      this.showRecordLoading = true

      this.dialogErrors = {}

      this.$swal({
        title: 'Are you sure you want to create this record?',
        text: "Create record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, create it!',
        focusCancel: true,
      }).then((result) => {
        if (result.isConfirmed) {
          this.axios
          .post(`/api/${payload.apiRoute}/store`, this.dialogRecord)

          .then(response => {
            if (response.data.success) {

              let id = response.data.id

              this.record.to_godown_id = ''
              this.accountDetails = {}
              this.axios.get('/api/godowns/autocomplete/0')
                .then(response => {
                  this.godowns = response.data.records
                  this.record.to_godown_id = id
                  this.axios.get(`/api/godowns/${id}/details`)
                    .then(response => this.godownDetails = response.data)
                })

              this.closeDialog(payload.dialog)
            }

            if (!response.data.success) {
              this.$swal({
                icon: 'error',
                title: 'Error!',
                text: response.data.message ? response.data.message : 'Unknown error occured!'
              })
            }

            this.dialogCreateButton = false
            this.showRecordLoading = false
          })

          .catch(({response}) => {
            if (response.status == 422) {
              this.dialogErrors = response.data.errors

              this.dialogCreateButton = false
              this.showRecordLoading = false
            }
          })
        }
      })

      this.dialogCreateButton = false
      this.showRecordLoading = false
    },


    updateDialogRecord(id, payload) {
      this.dialogUpdateButton = true
      this.showRecordLoading = true

      this.dialogErrors = {}

      this.$swal({
        title: 'Are you sure you want to update this record?',
        text: "Update record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update it!',
        focusCancel: true,
      }).then((result) => {
        if (result.isConfirmed) {
          this.axios
          .post(`/api/${payload.apiRoute}/${id}/update`, this.dialogRecord)

          .then(response => {
            if (response.data.success) {

              this.record.to_godown_id = ''
              this.accountDetails = {}
              this.axios.get('/api/godowns/autocomplete/0')
                .then(response => {
                  this.godowns = response.data.records
                  this.record.to_godown_id = id
                  this.axios.get(`/api/godowns/${id}/details`)
                    .then(response => this.godownDetails = response.data)
                })

              this.closeDialog(payload.dialog)
            }

            if (!response.data.success) {
              this.$swal({
                icon: 'error',
                title: 'Error!',
                text: response.data.message ? response.data.message : 'Unknown error occured!'
              })
            }

            this.dialogUpdateButton = false
            this.showRecordLoading = false
          })

          .catch(({response}) => {
            if (response.status == 422) {
              this.dialogErrors = response.data.errors

              this.dialogUpdateButton = false
              this.showRecordLoading = false
            }
          })
        }
      })

      this.dialogUpdateButton = false
      this.showRecordLoading = false
    },
  }
}
