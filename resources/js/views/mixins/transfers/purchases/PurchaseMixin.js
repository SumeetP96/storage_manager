import { CrudMixin } from "./CrudMixin"
import { DialogMixin } from "../DialogMixin"
import { TransferMixin } from "../TransferMixin"

export const PurchaseMixin = {
  mixins: [DialogMixin, CrudMixin, TransferMixin],

  methods: {
    /**
     * Fetch for Purchase update mode
     * @param {BigInteger} id Record ID
     */
    customFetchRecord(id) {
      this.showRecordLoading = true
      this.fromLoading = true
      this.toLoading = true
      this.productLoading = true
      this.agentLoading = true

      this.record = {}

      this.axios
        .get(`/api/${this.apiRoute}/${id}/show`)

        .then(response => {
          this.record = response.data.record

          // Accounts
          this.axios.get('/api/godowns/autocomplete/1')
            .then(response => {
              this.accounts = response.data.records

              this.axios.get(`/api/godowns/${this.record.from_godown_id}/details`)
                .then(response => {
                  this.accountDetails = response.data
                  this.fromLoading = false
                })
            })

          // Products
          this.axios.get('/api/products/autocomplete')
            .then(response => {
              this.products = response.data.records

              this.axios.get(`/api/purchases/transfer_products/${id}`)
                .then(response => {
                  response.data.record.forEach((product, index) => {
                    this.inputProducts.push({ id: '', quantity: '', quantityRaw: ''})
                    this.productDetails.push({ unit: '', remarks: '' })

                    this.inputProducts[index].id = product.productId
                    this.inputProducts[index].quantity = product.quantity
                    this.inputProducts[index].quantityRaw = product.quantityRaw

                    this.fetchProductDetails(index)
                  })

                  this.clearUnusedInputs()

                  this.productLoading = false
                })
            })

          // Godowns
          this.axios.get('/api/godowns/autocomplete/0')
            .then(response => {
              this.godowns = response.data.records

              // Godown Details
              this.axios.get(`/api/godowns/${this.record.to_godown_id}/details`)
                .then(response => {
                  this.godownDetails = response.data
                  this.toLoading = false
                })
            })

          // Agents
          this.axios.get('/api/agents/autocomplete')
            .then(response => {
              this.agents = response.data.records
              this.agentLoading = false
            })

          this.showRecordLoading = false
        })

    },


    /**
     * Fetch for Purchase create mode
     */
    customFetchAll() {
      this.fromLoading = true
      this.toLoading = true
      this.productLoading = true
      this.agentLoading = true

      // Accounts
      this.axios.get('/api/godowns/autocomplete/1')
        .then(response => {
          this.accounts = response.data.records
          this.fromLoading = false
        })

      // Godowns
      this.axios.get('/api/godowns/autocomplete/0')
        .then(response => {
          this.godowns = response.data.records
          this.toLoading = false
        })

      // Products
      this.axios.get('/api/products/autocomplete')
        .then(response => {
          this.products = response.data.records
          this.productLoading = false
        })

      // Agents
      this.axios.get('/api/agents/autocomplete')
        .then(response => {
          this.agents = response.data.records
          this.agentLoading = false
        })
    },


    /**
     * Fetch selected product's details
     */
    fetchProductDetails(index) {
      this.axios.get(`/api/products/${this.inputProducts[index].id}/details`)
        .then(response => {
          this.productDetails[index].unit = response.data.unit
          this.productDetails[index].remarks = response.data.remarks
        })
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
