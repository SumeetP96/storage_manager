export const CrudMixin = {
  methods: {
    createDialogRecord(payload) {
      this.dialogRecord.is_account = payload.hasOwnProperty('isAccount') ? payload.isAccount : ''

      this.dialogCreateButton = true
      this.showRecordLoading = true

      this.dialogErrors = {}

      const currentIndex = this.currentIndex

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

              const id = response.data.id

              if (payload.apiRoute == 'products') {
                this.inputProducts[currentIndex].id = id

                this.axios.get('/api/products/autocomplete')
                  .then(response => {
                    this.products = response.data.records
                    this.fetchProductDetails(currentIndex)
                  })
              }

              if (payload.apiRoute == 'agents') {
                this.record.agent_id = ''
                this.axios.get('/api/agents/autocomplete')
                  .then(response => {
                    this.agents = response.data.records
                    this.record.agent_id = id
                  })
              }

              if (payload.apiRoute == 'godowns') {

                if (payload.isAccount) {
                  this.accountDetails = {}
                  this.record.from_godown_id = ''
                  this.axios.get('/api/godowns/autocomplete/1')
                    .then(response => {
                      this.accounts = response.data.records
                      this.record.from_godown_id = id
                      this.axios.get(`/api/godowns/${id}/details`)
                        .then(response => this.accountDetails = response.data)
                    })
                } else {
                  this.godownDetails = {}
                  this.record.to_godown_id = ''
                  this.axios.get('/api/godowns/autocomplete/0')
                    .then(response => {
                      this.godowns = response.data.records
                      this.record.to_godown_id = id
                      this.axios.get(`/api/godowns/${id}/details`)
                        .then(response => this.godownDetails = response.data)
                    })
                }
              }

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
      const currentIndex = this.currentIndex

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
              if (payload.apiRoute == 'products') {
                this.inputProducts[currentIndex].id = ''

                this.axios.get('/api/products/autocomplete')
                  .then(response => {
                    this.products = response.data.records
                    this.inputProducts[currentIndex].id = id
                    this.fetchProductDetails(currentIndex)
                  })
              }

              if (payload.apiRoute == 'agents') {
                this.record.agent_id = ''
                this.axios.get('/api/agents/autocomplete')
                  .then(response => {
                    this.agents = response.data.records
                    this.record.agent_id = id
                  })
              }

              if (payload.apiRoute == 'godowns') {

                if (payload.isAccount) {
                  this.accountDetails = {}
                  this.record.from_godown_id = ''
                  this.axios.get('/api/godowns/autocomplete/1')
                    .then(response => {
                      this.accounts = response.data.records
                      this.record.from_godown_id = id
                      this.axios.get(`/api/godowns/${id}/details`)
                        .then(response => this.accountDetails = response.data)
                    })
                } else {
                  this.godownDetails = {}
                  this.record.to_godown_id = ''
                  this.axios.get('/api/godowns/autocomplete/0')
                    .then(response => {
                      this.godowns = response.data.records
                      this.record.to_godown_id = id
                      this.axios.get(`/api/godowns/${id}/details`)
                        .then(response => this.godownDetails = response.data)
                    })
                }
              }
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
