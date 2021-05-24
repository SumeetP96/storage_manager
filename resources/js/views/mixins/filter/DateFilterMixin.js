export const DateFilterMixin = {
  data() {
    return {
      lastFrom: '',
      lastTo: '',
      dbFromDate: '',
      dbToDate: '',

      lastCreatedFrom: '',
      lastCreatedTo: '',
      dbCreatedFromDate: '',
      dbCreatedToDate: '',

      lastUpdatedFrom: '',
      lastUpdatedTo: '',
      dbUpdatedFromDate: '',
      dbUpdatedToDate: '',
    }
  },

  methods: {
    fetchDateRecords() {
      if (this.dbFromDate == '' && this.dbToDate == '') {
        if (this.dbFromDate == this.lastFrom && this.dbToDate == this.lastTo) return
        this.loadRecords()
        this.lastFrom = ''
        this.lastTo = ''
      }

      if (this.dbFromDate != '' && this.dbToDate != '') {
        if (this.dbFromDate == this.lastFrom && this.dbToDate == this.lastTo) return

        this.customQuery = `from=${this.dbFromDate}&to=${this.dbToDate}`
        this.loadRecords()
        this.lastFrom = this.dbFromDate
        this.lastTo = this.dbToDate
      }
    },

    resetDates() {
      this.record.fromDate = ''
      this.record.toDate = ''
      this.dbFromDate = ''
      this.dbToDate = ''
      this.customQuery = ''
    },
  }
}
