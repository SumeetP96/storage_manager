export const TransactionDateMixin = {
  data() {
    return {
      transactionDate: localStorage.getItem('transaction_date')
    }
  },

  methods: {
    fetchTransactionDate() {
      if (this.transactionDate) return

      this.axios
        .get('/api/settings/get_lock_date')
        .then(response => {
          localStorage.setItem('transaction_date', response.data.lockDate)
          this.transactionDate = localStorage.getItem('transaction_date')
        })
    }
  }
}
