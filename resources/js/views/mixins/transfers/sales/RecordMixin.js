export const RecordMixin = {
  data() {
    return {
      agents: [],
      agentDialog: false,
      agentLoading: false,

      accounts: [],
      fromLoading: false,
      accountDialog: false,

      godowns: [],
      toLoading: false,
      godownDialog: false,

      products: [],
      productDialog: false,
      productLoading: false,
      lotNumberLoading: false,

      inputProducts: [{
        id: '',
        rent: '', rentRaw: '',
        labour: '', labourRaw: '',
        quantity: '', quantityRaw: '',
      }],
    }
  },

  methods: {
    customLoadRecord(id) {
      this.dft_ShowRecord = true

      this.record = {}

      this.axios
        .get(`/api/${this.apiRoute}/${id}/show`)

        .then(response => {
          this.record = response.data.record
          this.godownDetails = { address: this.record.fromAddress, contact_1: this.record.fromContact1, contact_2: this.record.fromContact2 }
          this.accountDetails = { address: this.record.toAddress, contact_1: this.record.toContact1, contact_2: this.record.toContact2 }
          this.products = response.data.record.autofillProducts

          for (let i = 0; i < response.data.record.inputProducts.length; i++) {
            this.inputProducts[i] = response.data.record.inputProducts[i]
            this.productDetails[i] = response.data.record.inputProducts[i].details
            this.inputProducts[i].rent = response.data.record.inputProducts[i].lotDetails.rent
            this.inputProducts[i].rentRaw = this.formatQuantity(response.data.record.inputProducts[i].lotDetails.rent, 1)
            this.inputProducts[i].loading = response.data.record.inputProducts[i].lotDetails.loading
            this.inputProducts[i].loadingRaw = this.formatQuantity(response.data.record.inputProducts[i].lotDetails.loading, 1)
            this.inputProducts[i].unloading = response.data.record.inputProducts[i].lotDetails.unloading
            this.inputProducts[i].unloadingRaw = this.formatQuantity(response.data.record.inputProducts[i].lotDetails.unloading, 1)
          }

          this.dft_ShowRecord = false
        })
    },

    createTransfer(redirectRoute) {
      this.clearUnusedInputs()
      this.record.products = this.inputProducts
      this.createFromForm({ redirect: redirectRoute })
    },

    updateTransfer(id, redirectRoute, payload) {
      this.clearUnusedInputs()
      this.record.products = this.inputProducts
      this.updateFromForm(id, { redirect: redirectRoute, payload: payload })
    },

    // Get entry no
    fetchNewEntryNo() {
      this.axios.get(`/api/${this.apiRoute}/new`)
        .then(response => this.record = response.data.record)
    },

    // Accounts
    fetchFromAutofill(payload = {}) {
      this.fromLoading = true
      this.axios.get('/api/autofills/godowns/all_godowns')
        .then(response => {
          this.accounts = response.data
          if (payload.hasOwnProperty('id') && payload.id) this.inputProducts[payload.varName].id = payload.id
          this.fromLoading = false
        })
    },

    // Godowns
    fetchToAutofill(payload = {}) {
      this.toLoading = true
      this.axios.get('/api/autofills/godowns/all_accounts')
        .then(response => {
          this.godowns = response.data
          if (payload.hasOwnProperty('id') && payload.id) this.record[payload.varName] = payload.id
          this.toLoading = false
        })
    },

    // Agents
    fetchAgentAutofill(payload = {}) {
      this.agentLoading = true
      this.axios.get('/api/autofills/agents/all')
        .then(response => {
          this.agents = response.data
          if (payload.hasOwnProperty('id') && payload.id) this.record[payload.varName] = payload.id
          this.agentLoading = false
        })
    },
  }
}
