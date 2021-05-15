export const CrudHelperMixin = {
  methods: {
    /**
     * Load all records
     * @param {Object} payload Options
     */
    loadRecords(payload = {}) {
      this.fetchAllRecords({
        search: payload.hasOwnProperty('search') ? payload.search : false,
        reset: payload.hasOwnProperty('reset') ? payload.reset : true,
        loader: payload.hasOwnProperty('loader') ? payload.loader : undefined,
        loadMore: payload.hasOwnProperty('loadMore') ? payload.loadMore : false,
        customQuery: payload.hasOwnProperty('customQuery') ? payload.customQuery : false,
      })
    },


    /**
     * Show record from id
     * @param {BigInteger} id Record ID
     * @param {Object} payload Options
     */
    loadRecord(id, payload = {}) {
      this.fetchRecord(id, {
        loader: payload.hasOwnProperty('loaders') ? payload.loader : undefined
      })
    },


    /**
     * Goto edit page from dialog
     * @param {Object} payload Options
     */
    editFromTable(payload = {}) {
      this.$router.push({
        name: payload.name,
        params: payload.params
      })
    },


    /**
     * Create record from form
     * @param {Object} payload Options
     */
    createFromForm(payload = {}) {
      this.storeRecord({
        loader: payload.hasOwnProperty('loader') ? payload.loader : undefined,
        redirect: payload.hasOwnProperty('redirect') ? payload.redirect : undefined
      })
    },


    /**
     * Update record from form
     * @param {BigInteger} id Record ID
     * @param {Object} payload Options
     */
    updateFromForm(id, payload = {}) {
      this.updateRecord(id, {
        loader: payload.hasOwnProperty('loader') ? payload.loader : undefined,
        redirect: payload.hasOwnProperty('redirect') ? payload.redirect : undefined
      })
    },


    /**
     * Delete record from table dialog
     * @param {BigInteger} id Record ID
     * @param {Object} payload Options
     */
     deleteFromTable(id, payload = {}) {
      this.destroyRecord(id, {
        dialog: payload.dialog,
        redirect: payload.hasOwnProperty('redirect') ? payload.redirect : undefined,
        loader: payload.hasOwnProperty('loader') ? payload.loader : undefined,
        refreshTable: payload.hasOwnProperty('refreshTable') ? payload.refreshTable : true
      })
    },
  }
}
