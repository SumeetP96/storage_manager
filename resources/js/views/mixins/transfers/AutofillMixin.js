export const AutofillMixin = {
  data() {
    return {
      agents: [],
      agentLoading: false,

      accounts: [],
      fromLoading: false,

      godowns: [],
      toLoading: false,

      products: [],
      productLoading: false,
    }
  },

  methods: {
    // Get entry no
    fetchNewEntryNo() {
      this.axios.get(`/api/${this.apiRoute}/new`)
        .then(response => this.record = response.data.record)
    },

    // Accounts
    fetchFromAutofill(payload = {}) {
      this.fromLoading = true
      this.axios.get('/api/autofills/godowns/all_accounts')
        .then(response => {
          this.accounts = response.data
          this.fromLoading = false
        })
    },

    // Godowns
    fetchToAutofill(payload = {}) {
      this.toLoading = true
      this.axios.get('/api/autofills/godowns/all_godowns')
        .then(response => {
          this.godowns = response.data
          this.toLoading = false
        })
    },

    // Products
    fetchProductAutofill(payload = {}) {
      this.productLoading = true
      this.axios.get('/api/autofills/products/all')
        .then(response => {
          this.products = response.data
          this.productLoading = false
        })
    },

    // Agents
    fetchAgentAutofill(payload = {}) {
      this.agentLoading = true
      this.axios.get('/api/autofills/agents/all')
        .then(response => {
          this.agents = response.data
          this.agentLoading = false
        })
    },
  }
}
