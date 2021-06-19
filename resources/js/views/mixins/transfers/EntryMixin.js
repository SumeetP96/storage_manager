export const EntryMixin = {
  data() {
    return {
      inputProducts: [{
        id: '',
        rent: '', rentRaw: '',
        labour: '', labourRaw: '',
        quantity: '', quantityRaw: '',
      }],
    }
  },

  methods: {
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

    addProductInputRow() {
      const last = this.inputProducts.length
      this.productDetails.push({ unit: '', stock: '', remarks: '' })
      this.inputProducts.push({
        id: '', quantity: '', quantityRaw: '', compoundQuantity: '', compoundQuantityRaw: '',
        rent: '', rentRaw: '', labour: '', labourRaw: ''
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
      this.productDetails[index] = { unit: '', remarks: '', packing: '', compoundUnit: '' }
      this.clearProductErrors()
      this.clearUnusedInputs()
    },

    clearProductErrors() {
      this.errors[`product_${index}_rent`] = ''
      this.errors[`product_${index}_labour`] = ''
      this.errors[`product_${index}_compound_quantity`] = ''
      this.errors[`product_${index}_quantity`] = ''
    },

    clearUnusedInputs() {
      this.inputProducts = this.inputProducts.filter((product) => product.id || product.quantity)
      if (this.inputProducts.length == 0) this.addProductInputRow()
    },

    calculateTotalQuantity() {
      let quantity = 0

      this.inputProducts.forEach(product => {
        if (product.id && product.quantityRaw) {
          quantity += parseFloat(product.quantityRaw)
        }
      })

      return quantity.toFixed(2)
    },

    calculateTotalCompound() {
      let compound = 0

      this.inputProducts.forEach(product => {
        if (product.id && product.compoundQuantityRaw) {
          compound += parseFloat(product.compoundQuantityRaw)
        }
      })

      return compound.toFixed(2)
    },

    calculateQuantity(index) {
      let compoundQuantity = parseFloat(this.inputProducts[index].compoundQuantityRaw)
      let packing = this.formatQuantity(parseFloat(this.productDetails[index].packing), 0)

      if (!compoundQuantity || !packing) return
      if (parseFloat(compoundQuantity) <= 0) return

      if (compoundQuantity && packing) {
        this.inputProducts[index].quantityRaw = (compoundQuantity * packing).toFixed(0)
        this.inputProducts[index].quantity = (compoundQuantity * packing).toFixed(0) * 100
      } else {
        this.inputProducts[index].quantityRaw = undefined
        this.inputProducts[index].quantity = undefined
      }
    },
  }
}
