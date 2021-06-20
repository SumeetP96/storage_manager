export const EntryMixin = {
  methods: {
    resetProducts() {
      this.inputProducts = [{
        id: '',
        rent: '', rentRaw: '',
        labour: '', labourRaw: '',
        quantity: '', quantityRaw: ''
      }]

      this.productDetails = [{
        unit: '',
        stock: '',
        remarks: '',
        packing: '',
        lotNumbers: []
      }]
    },

    addProductInputRow() {
      const last = this.inputProducts.length
      this.productDetails.push({ unit: '', stock: '', remarks: '', packing: '', lotNumbers: [] })
      this.inputProducts.push({
        id: '',
        rent: '', rentRaw: '',
        labour: '', labourRaw: '',
        quantity: '', quantityRaw: ''
      })
      setTimeout(() => document.getElementById(`productBox${last}`).focus(), 100);
    },

    invalidAddition() {
      const last = this.inputProducts[this.inputProducts.length - 1]
      if (last.id) return false
      else return true
    },

    removeProductInputRow(index) {
      this.inputProducts = this.inputProducts.filter((product, ind) => ind != index)
      this.productDetails[index] = {}
      this.clearProductErrors(index)
      this.clearUnusedInputs()
    },

    clearProductErrors(index) {
      this.errors[`product_${index}_rent`] = ''
      this.errors[`product_${index}_loading`] = ''
      this.errors[`product_${index}_unloading`] = ''
      this.errors[`product_${index}_quantity`] = ''
    },

    clearUnusedInputs() {
      this.inputProducts = this.inputProducts.filter((product) => product.id || product.quantity)
      if (this.inputProducts.length == 0) this.addProductInputRow()
    },

    calculateItemQuantity(index) {
      let amount = 0

      if (this.inputProducts[index].quantityRaw) {
        let packing = this.formatQuantity(this.productDetails[index].packing, 2)
        let quantity = parseFloat(this.inputProducts[index].quantityRaw)

        amount = packing * quantity
      }

      return amount.toFixed(2)
    },

    calculateTotalQuantityUnits() {
      let quantity = 0

      this.inputProducts.forEach(product => {
        if (product.quantityRaw) {
          quantity += parseFloat(product.quantityRaw)
        }
      })

      return quantity.toFixed(2)
    },

    calculateTotalQuantityKgs() {
      let quantity = 0

      this.inputProducts.forEach((product, index) => {
        quantity += parseFloat(this.calculateItemQuantity(index))
      })

      return quantity.toFixed(2)
    },
  }
}
