export const DetailsMixin = {
  data() {
    return {
      godownDetails: {},
      accountDetails: {},
      productDetails: [{
        unit: '',
        stock: 0,
        remarks: '',
        packing: '',
        lotNumbers: []
      }],
      stock: []
    }
  },

  methods: {
    // Account details
    fetchAccountDetails() {
      this.accountDetails = {}

      if (!this.record.from_godown_id) return

      this.accountLoading = true
      this.axios.get(`/api/autofills/godowns/details/${this.record.from_godown_id}`)
        .then(response => {
          this.accountDetails = response.data
          this.accountLoading = false
        })
    },

    // Godown details
    fetchGodownDetails() {
      this.godownDetails = {}

      if (!this.record.to_godown_id) return

      this.godownLoading = true
      this.axios.get(`/api/autofills/godowns/details/${this.record.to_godown_id}`)
        .then(response => {
          this.godownDetails = response.data
          this.godownLoading = false
        })
    },

    // Product details
    fetchProductDetails(index) {
      this.productDetails[index] = { unit: '', remarks: '', packing: '', lotNumbers: [] }

      if (!this.inputProducts[index].id) return

      this.productLoading = true
      this.axios.get(`/api/autofills/products/details/${this.inputProducts[index].id}`)
        .then(response => {
          this.productDetails[index] = response.data

          this.lotNumberLoading = true
          this.axios.get(`/api/autofills/products/lot_numbers/${this.record.from_godown_id}/${this.inputProducts[index].id}`)
            .then(response => {
              this.productDetails[index].lotNumbers = response.data
              this.lotNumberLoading = false
              document.getElementById(`lotBox${index}`).focus()
            })

          this.productLoading = false
        })
    },

    fetchLotStock(index) {
      if (!this.record.from_godown_id || !this.inputProducts[index].id || !this.inputProducts[index].lot_number) return

      this.axios.get(`/api/autofills/products/lot_stock/${this.record.from_godown_id}/${this.inputProducts[index].id}/${this.inputProducts[index].lot_number}`)
        .then(response => {
          this.productDetails[index].stock = this.formatQuantity(response.data.current_stock, 2)
          this.inputProducts[index].rent = response.data.details.rent
          this.inputProducts[index].rentRaw = this.formatQuantity(response.data.details.rent, 1)
          this.inputProducts[index].loading = response.data.details.loading
          this.inputProducts[index].loadingRaw = this.formatQuantity(response.data.details.loading, 1)
          this.inputProducts[index].unloading = response.data.details.unloading
          this.inputProducts[index].unloadingRaw = this.formatQuantity(response.data.details.unloading, 1)
        })
    },

    fetchGodownProducts(payload = {}) {
      if (!payload.hasOwnProperty('id')) this.resetProducts()
      if (!this.record.from_godown_id) return
      this.axios.get(`/api/autofills/godowns/products/${this.record.from_godown_id}`)
        .then(response => {
          this.products = response.data
          if (payload.hasOwnProperty('id') && payload.id) {
            this.inputProducts[payload.varName].id = payload.id
            this.fetchProductDetails(payload.varName)
            this.fetchLotStock(payload.varName)
          }
        })
    }
  }
}
