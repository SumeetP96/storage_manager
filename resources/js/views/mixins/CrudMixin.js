export const CrudMixin = {
  data() {
    return {
      errors: {},

      record: {
        fromDate: '',
        toDate: '',

        createdFromDate: '',
        createdToDate: '',

        updatedFromDate: '',
        updatedToDate: '',
      },

      recordProducts: [],

      records: [],

      apiRoute: '',
      customQuery: '',

      disablePackingEdit: true,
    }
  },

  methods: {
    editPacking() {
      this.$swal({
        title: 'Warning!',
        text: "Editing product packing will reset all the packing fields entered in the transactions, and you will have to manually enter them again. Are you sure you want to continue?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, continue!',
        focusCancel: true,
      }).then((result) => {
        if (result.isConfirmed) {
          this.disablePackingEdit = false
        } else {
          this.disablePackingEdit = true
        }
      })
    },


    /**
     * Load all records
     * @param {Object} payload Options
     */
    fetchAllRecords(payload) {
      let loader = payload.loader ? payload.loader : this.dft_RecordsTable
      this[loader] = true
      this.refreshLoading = true

      if (payload.reset) this.records = []
      this.searchLoading = true
      if (payload.loadMore) this.loadMoreLoading = true
      if (payload.customQuery) this.customQuery = payload.customQuery

      this.axios
        .get(`/api/${this.apiRoute}?query=${this.query}
          &limit=${this.recordsPerPage}
          &skip=${this.records.length}
          &sortBy=${this.sortBy}
          &flow=${this.flow}
          &${this.customQuery}`)

        .then(response => {
          response.data.records.forEach(record => this.records.push(record))
          this.totalRecords = response.data.total

          this.clearFilterVars()

          this[loader] = false
          this.refreshLoading = false
          if (payload.loadMore) this.loadMoreLoading = false

          this.searchLoading = false
          if (payload.search) {
            setTimeout(() => { document.getElementById('searchInput').focus() }, 100);
          }
        })
    },


    /**
     * Show record from database
     * @param {BigInteger} id Record ID
     * @param {Object} payload Options
     */
    fetchRecord(id, payload) {
      let loader = payload.loader ? payload.loader : this.dft_ShowRecord
      this[loader] = true

      this.record = {}

      this.axios
        .get(`/api/${this.apiRoute}/${id}/show`)

        .then(response => {
          this.record = response.data.record
          this[loader] = false
        })
    },


    /**
     * Create record in database
     * @param {Object} payload Options
     */
    storeRecord(payload) {
      let loader = payload.loader ? payload.loader : this.dft_CreateButton
      this[loader] = true

      this.errors = {}
      this.$swal({
        title: 'Are you sure you want to save?',
        text: "Save record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!',
        focusCancel: true,
      }).then((result) => {
        if (result.isConfirmed) {
          this.axios
            .post(`/api/${this.apiRoute}/store`, this.record)

            .then(response => {
              if (response.data.success) {
                this.$swal(
                  'Saved!',
                  'Record has been saved.',
                  'success'
                )
                this.$router.push({ name: payload.redirect })
              }

              if (!response.data.success) {
                this.$swal({
                  icon: 'error',
                  title: 'Error!',
                  text: response.data.message ? response.data.message : 'Unknown error occured!'
              })
              }

              if (response.data.warning) {
                this.$swal({
                  title: "Warning!",
                  icon: 'warning',
                  text: response.data.message
                })
                this.$router.push({ name: payload.redirect })
              }

              this[loader] = false
            })

            .catch(({response}) => {
              if (response.status == 422) {
                this.errors = response.data.errors
                this[loader] = false
              }
            })
        }
      })

      this[loader] = false
    },


    /**
     * Update record in database
     * @param {BigInteger} id Record ID
     * @param {Object} payload Options
     */
    updateRecord(id, payload) {
      let loader = payload.loader ? payload.loader : this.dft_UpdateButton
      this[loader] = true

      this.errors = {}

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
          .post(`/api/${this.apiRoute}/${id}/update`, this.record)

          .then(response => {
            if (response.data.success) {
              this.$swal(
                'Updated!',
                'Record has been updated.',
                'success'
              )
              this.$router.push({ name: payload.redirect })
            }

            if (!response.data.success) {
              this.$swal({
                icon: 'error',
                title: 'Error!',
                text: response.data.message ? response.data.message : 'Unknown error occured!'
              })
            }

            if (response.data.warning) {
              this.$swal({
                title: "Warning!",
                icon: 'warning',
                text: response.data.message
              })
              this.$router.push({ name: payload.redirect })
            }

            this[loader] = false
          })

          .catch(({response}) => {
            if (response.status == 422) {
              this.errors = response.data.errors
              this[loader] = false
            }
          })
        }
      })

      this[loader] = false
    },


    /**
     * Delete record from database
     * @param {BigInteger} id Record ID
     * @param {Object} payload Options
     */
    destroyRecord(id, payload) {
      let dialog = payload.dialog
      let loader = payload.loader ? payload.loader : this.dft_DeleteButton

      this[loader] = true

      this.$swal({
        title: 'Are you sure you want to delete this record?',
        text: "Delete record!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        focusCancel: true,
      }).then((result) => {
        if (result.isConfirmed) {
          this.axios
          .post(`/api/${this.apiRoute}/${id}/destroy`)

          .then(response => {
            if (response.data.success) {
              this.$swal('Deleted!', 'Record has been deleted.', 'success')

              if (payload.redirect) this.$router.push({ name: payload.redirect })
              if (payload.refreshTable) this.refreshTable()
              if (dialog) this[dialog] = false
            }
          })

          .catch(({response}) => {
            if (response.status == 500) {
              this.$swal({
                icon: 'error',
                title: 'Error!',
                text: 'Transactions exist, cannot delete!'
              })
            }
          })
        }

        this[loader] = false
      })
    },

    customFetchTransfer(id) {
      let loader = this.dft_ShowRecord
      this[loader] = true

      this.record = {}

      this.axios
        .get(`/api/${this.apiRoute}/${id}/show`)

        .then(response => {
          this.record = response.data.record
          this.axios.get(`api/${this.apiRoute}/transfer_products/${id}`)
            .then(response => {
              this.recordProducts = response.data.record
              this[loader] = false
            })
        })
    }
  }
}
