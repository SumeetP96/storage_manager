export const DialogMixin = {
  data() {
    return {
      currentIndex: '',
      updateMode: false,

      dialogRecord: {},
      dialogErrors: {},

      agentDialog: false,
      godownDialog: false,
      productDialog: false,
      accountDialog: false,
    }
  },

  methods: {
    closeDialog(dialog) {
      this[dialog] = false
      this.dialogRecord = {}
      this.updateMode = false
      this.currentIndex = ''
    },

    openDialog(dialog, apiRoute, id, updateProduct = false, index = '') {
      this[dialog] = true
      this.dialogRecord = {}

      this.currentIndex = index
      if (updateProduct) {
        this.updateMode = true
        this.record.product_id = id
      }

      if (apiRoute && id) {
        this.axios
          .get(`/api/${apiRoute}/${id}/show`)
          .then(response => this.dialogRecord = response.data.record)
      }
    },
  }
}
