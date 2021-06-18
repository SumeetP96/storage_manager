import { CrudMixin } from "./CrudMixin"
import { DialogMixin } from "../DialogMixin"
import { TransferMixin } from "../TransferMixin"

export const SalesMixin = {
  mixins: [DialogMixin, CrudMixin, TransferMixin],

  methods: {
    /**
     * Fetch for Sales update mode
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

          // From godowns
          this.axios.get('/api/godowns/autocomplete_with_stock')
            .then(response => {
              this.godownsWithStock = response.data.records

              this.axios.get(`/api/godowns/${this.record.from_godown_id}/details`)
                .then(response => {
                  this.godownDetails = response.data
                  this.fromLoading = false
                })
            })

          // Products with stock
          this.axios.get(`/api/products/autocomplete_with_stock/${this.record.from_godown_id}`)
            .then(response => {
              this.products = response.data.records

              this.axios.get(`/api/purchases/transfer_products/${id}`)
                .then(response => {
                  response.data.record.forEach((product, index) => {
                    this.inputProducts.push({ id: '', quantity: '', quantityRaw: '', compoundQuantity: '', compoundQuantityRaw: '' })
                    this.productDetails.push({ unit: '', remarks: '', packing: '', compoundUnit: '' })

                    this.inputProducts[index].id = product.productId
                    this.inputProducts[index].quantity = product.quantity
                    this.inputProducts[index].quantityRaw = product.quantityRaw
                    this.inputProducts[index].compoundQuantity = product.compoundQuantity
                    this.inputProducts[index].compoundQuantityRaw = product.compoundQuantityRaw

                    this.fetchProductDetails(index)
                  })

                  this.clearUnusedInputs()

                  this.productLoading = false
                })
            })

          // Accounts
          this.axios.get('/api/godowns/autocomplete/1')
            .then(response => {
              this.accounts = response.data.records

              // Account Details
              this.axios.get(`/api/godowns/${this.record.to_godown_id}/details`)
                .then(response => {
                  this.accountDetails = response.data
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
     * Fetch for Sales create mode
     */
    customFetchAll() {
      this.fromLoading = true
      this.toLoading = true
      this.agentLoading = true

      // Godowns
      this.axios.get('/api/godowns/autocomplete_with_stock')
        .then(response => {
          this.godownsWithStock = response.data.records
          this.fromLoading = false
        })

      // Accounts
      this.axios.get('/api/godowns/autocomplete/1')
        .then(response => {
          this.accounts = response.data.records
          this.toLoading = false
        })

      // Agents
      this.axios.get('/api/agents/autocomplete')
        .then(response => {
          this.agents = response.data.records
          this.agentLoading = false
        })
    },


    /**
     * Fetch Godown with Products in it
     */
    fetchGodownWithStockDetails() {
      this.productLoading = true
      this.axios.get(`/api/godowns/${this.record.from_godown_id}/details`)
        .then(response => {
          this.godownDetails = response.data

          this.inputProducts = [{ id: '', quantity: '', quantityRaw: '' }]
          this.productDetails = [{ unit: '', stock: '', remarks: '' }]

          // Products with stock
          this.axios.get(`/api/products/autocomplete_with_stock/${this.record.from_godown_id}`)
            .then(response => {
              this.products = response.data.records
              this.productLoading = false
            })
        })
    },


    /**
     * Fetch selected product's details
     */
     fetchProductDetails(index, clear) {
      this.productDetails[index].remarks = ''
      if (!this.inputProducts[index].id || clear) return
      this.axios.get(`/api/products/${this.inputProducts[index].id}/details/${this.record.from_godown_id}`)
        .then(response => {
          this.productDetails[index].unit = response.data.unit
          this.productDetails[index].remarks = response.data.remarks
          this.productDetails[index].compoundUnit = response.data.compoundUnit
          this.productDetails[index].packing = response.data.packing
          this.productDetails[index].lotNumbers = response.data.lotNumbers
        })
    },

    fetchStockDetails(index, clear) {
      this.productDetails[index].stock = ''
      if (!this.inputProducts[index].id || clear) return
      this.axios.get(`/api/products/${this.inputProducts[index].id}/stock_details/${this.record.from_godown_id}/${this.inputProducts[index].lot_number}`)
        .then(response => {
          this.productDetails[index].stock = response.data.current_stock
          this.inputProducts[index].rentRaw = this.formatQuantity(response.data.details.rent, 2)
          this.inputProducts[index].labourRaw = this.formatQuantity(response.data.details.labour, 2)
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
