import { CrudMixin } from "./CrudMixin"
import { DialogMixin } from "../DialogMixin"
import { TransferMixin } from "../TransferMixin"

export const InterGodownMixin = {
  mixins: [DialogMixin, CrudMixin, TransferMixin],

  methods: {
    /**
     * Fetch for Inter Godown update mode
     * @param {BigInteger} id Record ID
     */
    customFetchRecord(id) {
      this.showRecordLoading = true

      this.fromLoading = true
      this.toLoading = true
      this.productLoading = true

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
                  this.accountDetails = response.data
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

          this.showRecordLoading = false
        })
    },


    /**
     * Fetch for Inter Godown create mode
     */
    customFetchAll() {
      this.fromLoading = true
      this.toLoading = true

      // From godowns
      this.axios.get('/api/godowns/autocomplete_with_stock')
        .then(response => {
          this.godownsWithStock = response.data.records
          this.fromLoading = false
        })

      // Godowns
      this.axios.get('/api/godowns/autocomplete/0')
        .then(response => {
          this.godowns = response.data.records
          this.toLoading = false
        })
    },


    /**
     * Fetch Godown with products in it
     */
    fetchGodownWithStockDetails() {
      this.productLoading = true
      this.axios.get(`/api/godowns/${this.record.from_godown_id}/details`)
        .then(response => {
          this.accountDetails = response.data

          this.productDetails = [{ unit: '', stock: '', remarks: '' }]
          this.inputProducts = [{ id: '', quantity: '', quantityRaw: '' }]

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
     fetchProductDetails(index) {
      this.axios.get(`/api/products/${this.inputProducts[index].id}/details/${this.record.from_godown_id}`)
        .then(response => {
          this.productDetails[index].unit = response.data.unit
          this.productDetails[index].stock = response.data.stock
          this.productDetails[index].remarks = response.data.remarks
          this.productDetails[index].compoundUnit = response.data.compoundUnit
          this.productDetails[index].packing = response.data.packing
        })
    },


    /**
     * Fetch selected godown's deatils
     */
    fetchGodownDetails() {
      this.axios.get(`/api/godowns/${this.record.to_godown_id}/details`)
        .then(response => this.godownDetails = response.data)
    }
  }
}
