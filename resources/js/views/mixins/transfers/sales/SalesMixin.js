import { CrudMixin } from "./CrudMixin"
import { DialogMixin } from "../DialogMixin"

export const SalesMixin = {
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
      accounts: [],
      products: [],
      godownsWithStock: [],

      godownDetails: {},
      accountDetails: {},
      productDetails: {},

      autocompleteLoading: false
    }
  },

  methods: {
    /**
     * Fetch for Sales update mode
     * @param {BigInteger} id Record ID
     */
    customFetchRecord(id) {
      this.showRecordLoading = true

      this.record = {}

      this.axios
        .get(`/api/${this.apiRoute}/${id}/show`)

        .then(response => {
          this.record = response.data.record

          // From godowns
          this.axios.get('/api/godowns/autocomplete_with_stock')
            .then(response => {
              this.godownsWithStock = response.data.records

              // Godown details
              this.axios.get(`/api/godowns/${this.record.from_godown_id}/details`)
                .then(response => {
                  this.godownDetails = response.data

                  // Products with stock
                  this.axios.get(`/api/products/autocomplete_with_stock/${this.record.from_godown_id}`)
                    .then(response => {
                      this.products = response.data.records

                      // Product details
                      this.axios.get(`/api/products/${this.record.product_id}/details/${this.record.from_godown_id}`)
                        .then(response => this.productDetails = response.data)
                    })
                })
            })

          // Accounts
          this.axios.get('/api/godowns/autocomplete/1')
            .then(response => {
              this.accounts = response.data.records

              // Account Details
              this.axios.get(`/api/godowns/${this.record.to_godown_id}/details`)
                .then(response => this.accountDetails = response.data)
            })

          // Agents
          this.axios.get('/api/agents/autocomplete')
            .then(response => this.agents = response.data.records)

        })

        this.showRecordLoading = false
    },


    /**
     * Fetch for Sales create mode
     */
    customFetchAll() {
      this.showRecordLoading = true

      // Godowns
      this.axios.get('/api/godowns/autocomplete_with_stock')
        .then(response => this.godownsWithStock = response.data.records)

      // Accounts
      this.axios.get('/api/godowns/autocomplete/1')
        .then(response => this.accounts = response.data.records)

      // Agents
      this.axios.get('/api/agents/autocomplete')
        .then(response => this.agents = response.data.records)

      this.showRecordLoading = false
    },


    /**
     * Fetch Godown with Products in it
     */
    fetchGodownWithStockDetails() {
      this.axios.get(`/api/godowns/${this.record.from_godown_id}/details`)
        .then(response => {
          this.godownDetails = response.data

          this.productDetails = {}
          this.record.product_id = undefined

          // Products with stock
          this.axios.get(`/api/products/autocomplete_with_stock/${this.record.from_godown_id}`)
            .then(response => {
              this.products = response.data.records

              // Product details
              this.axios.get(`/api/products/${this.record.product_id}/details/${this.record.from_godown_id}`)
                .then(response => this.productDetails = response.data)
            })
        })
    },


    /**
     * Fetch selected product's details
     */
    fetchProductDetails() {
      this.productDetails = {}

      // Products with stock
      this.axios.get(`/api/products/autocomplete_with_stock/${this.record.from_godown_id}`)
      .then(response => {
        this.products = response.data.records

        // Product details
        this.axios.get(`/api/products/${this.record.product_id}/details/${this.record.from_godown_id}`)
          .then(response => this.productDetails = response.data)
      })
    },


    /**
     * Fetch selected account's details
     */
    fetchAccountDetails() {
      this.axios.get(`/api/godowns/${this.record.to_godown_id}/details`)
        .then(response => this.accountDetails = response.data)
    }
  }
}
