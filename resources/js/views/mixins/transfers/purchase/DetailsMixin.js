export const DetailsMixin = {
  data() {
    return {
      godownDetails: {},
      accountDetails: {},
      productDetails: [{
        unit: '',
        stock: '',
        remarks: '',
        packing: ''
      }],
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
      if (!this.inputProducts[index].id) return

      this.productLoading = true
      this.axios.get(`/api/autofills/products/details/${this.inputProducts[index].id}`)
        .then(response => {
          this.productDetails[index].unit = response.data.unit
          this.productDetails[index].remarks = response.data.remarks
          this.productDetails[index].packing = response.data.packing
          this.productLoading = false
        })
    },

    resetProductDetails(index) {
      this.productDetails[index] = { unit: '', stock: '', remarks: '', packing: '' }
    }
  }
}
