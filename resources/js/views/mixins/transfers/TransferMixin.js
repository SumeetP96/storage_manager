export const TransferMixin = {
  data() {
    return {
      transferTypes: { interGodown: 1, purchase: 2, sales: 3 },

      currentTransferType: '',

      agentDialog: false,
      godownDialog: false,
      accountDialog: false,
      productDialog: false,

      fromLoading: false,
      toLoading: false,
      agentLoading: false,
      productLoading: false,

      dialogRecord: {},
      dialogErrors: {},
      inputProducts: [{ id: '', quantity: '', quantityRaw: '', compoundQuantity: '', compoundQuantityRaw: '' }],

      agents: [],
      godowns: [],
      accounts: [],
      products: [],
      godownsWithStock: [],

      godownDetails: {},
      accountDetails: {},
      productDetails: [{ unit: '', remarks: '', packing: '', compoundUnit: '' }],
    }
  },

  methods: {
    createTransfer(redirectRoute) {
      this.clearUnusedInputs()
      this.record.products = this.inputProducts
      this.createFromForm({ redirect: redirectRoute })
    },

    updateTransfer(id, redirectRoute) {
      this.clearUnusedInputs()
      this.record.products = this.inputProducts
      this.updateFromForm(id, { redirect: redirectRoute })
    },

    addProductInputRow() {
      const last = this.inputProducts.length
      this.productDetails.push({ unit: '', stock: '', remarks: '' })
      this.inputProducts.push({ id: '', quantity: '', quantityRaw: '', compoundQuantity: '', compoundQuantityRaw: '' })
      setTimeout(() => document.getElementById(`productBox${last}`).focus(), 100);
    },

    invalidAddition() {
      const last = this.inputProducts[this.inputProducts.length - 1]
      if (last.id) return false
      else return true
    },

    removeProductInputRow(index) {
      this.inputProducts = this.inputProducts.filter((product, ind) => ind != index)
      this.clearUnusedInputs()
    },

    clearUnusedInputs() {
      this.inputProducts = this.inputProducts.filter((product) => product.id || product.quantity)
      if (this.inputProducts.length == 0) this.addProductInputRow()
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
