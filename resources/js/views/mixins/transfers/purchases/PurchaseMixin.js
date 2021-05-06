import { CrudMixin } from "./CrudMixin"
import { DialogMixin } from "../DialogMixin"

export const PurchaseMixin = {
  mixins: [DialogMixin, CrudMixin],

  data() {
    return {
      transferTypes: {
        interGodown: 1,
        purchase: 2,
        sales: 3
      },

      currentTransferType: '',

      dialogRecord: {},
      dialogErrors: {},

      agentDialog: false,
      godownDialog: false,
      accountDialog: false,
      productDialog: false,

      agents: [],
      godowns: [],
      accounts: [],
      products: [],

      godownDetails: {},
      accountDetails: {},
      productDetails: {},

      autocompleteLoading: false
    }
  },

  methods: {
    /**
     * Fetch for Purchase update mode
     * @param {BigInteger} id Record ID
     */
    customFetchRecord(id) {
      this.showRecordLoading = true

      this.record = {}

      this.axios
        .get(`/api/${this.apiRoute}/${id}/show`)

        .then(response => {
          this.record = response.data.record

          // Accounts
          this.axios.get('/api/godowns/autocomplete/1')
            .then(response => {
              this.accounts = response.data.records

              // Account details
              this.axios.get(`/api/godowns/${this.record.from_godown_id}/details`)
                .then(response => this.accountDetails = response.data)
            })

          // Products
          this.axios.get('/api/products/autocomplete')
            .then(response => {
              this.products = response.data.records

              // Product details
              this.axios.get(`/api/products/${this.record.from_godown_id}/details`)
                .then(response => this.productDetails = response.data)
            })

          // Godowns
          this.axios.get('/api/godowns/autocomplete/0')
            .then(response => {
              this.godowns = response.data.records

              // Godown Details
              this.axios.get(`/api/godowns/${this.record.to_godown_id}/details`)
                .then(response => this.godownDetails = response.data)
            })

          // Godowns
          this.axios.get('/api/agents/autocomplete')
            .then(response => this.agents = response.data.records)
        })

        this.showRecordLoading = false
    },


    /**
     * Fetch for Purchase create mode
     */
    customFetchAll() {
      this.showRecordLoading = true

      // Accounts
      this.axios.get('/api/godowns/autocomplete/1')
        .then(response => this.accounts = response.data.records)

      // Godowns
      this.axios.get('/api/godowns/autocomplete/0')
        .then(response => this.godowns = response.data.records)

      // Products
      this.axios.get('/api/products/autocomplete')
        .then(response => this.products = response.data.records)

      // Agents
      this.axios.get('/api/agents/autocomplete')
        .then(response => this.agents = response.data.records)

      this.showRecordLoading = false
    },


    /**
     * Fetch selected product's details
     */
    fetchProductDetails() {
      this.axios.get(`/api/products/${this.record.product_id}/details`)
        .then(response => this.productDetails = response.data)
    },


    /**
     * Fetch selected godown's details
     */
    fetchGodownDetails() {
      this.axios.get(`/api/godowns/${this.record.to_godown_id}/details`)
        .then(response => this.godownDetails = response.data)
    },


    /**
     * Fetch selected account's details
     */
    fetchAccountDetails() {
      this.axios.get(`/api/godowns/${this.record.from_godown_id}/details`)
        .then(response => this.accountDetails = response.data)
    }
  }
}
