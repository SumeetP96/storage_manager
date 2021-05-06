export const DialogMixin = {
  methods: {
    closeDialog(dialog) {
      this[dialog] = false
      this.dialogRecord = {}
    },

    openDialog(dialog, apiRoute, id) {
      this[dialog] = true;
      this.dialogRecord = {}

      if (apiRoute && id) {
        this.axios
          .get(`/api/${apiRoute}/${id}/show`)
          .then(response => this.dialogRecord = response.data.record)
      }
    },
  }
}
