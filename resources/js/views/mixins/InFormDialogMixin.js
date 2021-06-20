export const InFormDialogMixin = {
  data() {
    return {
      dialogRecord: {},
      dialogErrors: {},

      dialogUpdateButton: false,
      dialogCreateButton: false,

      // For invoice products
      currentIndex: '',
      currentIndexId: '',
    }
  },

  methods: {
    closeDialog(dialog) {
      this[dialog] = false
      this.dialogRecord = {}
      this.dialogErrors = {}
      this.currentIndex = ''
      this.currentIndexId = ''
    },

    openDialog(dialog, apiRoute, id, index = '') {
      this[dialog] = true
      this.dialogRecord = {}

      this.currentIndex = index
      this.currentIndexId = id

      if (apiRoute && id) {
        this.axios
          .get(`/api/${apiRoute}/${id}/show`)
          .then(response => this.dialogRecord = response.data.record)
      }
    },

    createDialogRecord(payload) {
      this.dialogCreateButton = true
      this.dialogErrors = {}

      this.$swal({
        title: 'Are you sure you want to save?',
        text: "Save record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
      }).then((result) => {
        if (result.isConfirmed) {
          this.axios
            .post(`/api/${payload.apiRoute}/store`, this.dialogRecord)
            .then(response => {
              if (response.data.success) {
                this[payload.afMethod]({ id: response.data.id, varName: payload.varName })
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
            })

            .catch(({response}) => {
              if (response.status == 422) {
                this.dialogErrors = response.data.errors
                this.dialogCreateButton = false
              }
            })
          }
        })
    },

    updateDialogRecord(id, payload) {
      this.dialogUpdateButton = true
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
              this[payload.afMethod]({ id: id, varName: payload.varName })
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
          })

          .catch(({response}) => {
            if (response.status == 422) {
              this.dialogErrors = response.data.errors
              this.dialogUpdateButton = false
            }
          })
        }
      })
    },
  }
}
