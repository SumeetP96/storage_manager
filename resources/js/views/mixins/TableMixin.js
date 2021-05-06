export const TableMixin = {
  data() {
    return {
      selectedColumns: [],
      extraColumns: [],

      viewRecordDialog: false,

      defaultRecordsPerPage: 10,
      recordsPerPage: 10,
      perPageItems: [10, 25, 50, 100],

      totalRecords: 0,
      currentCount: this.record ? this.records.length : 0,

      sortBy: 'created_at',
      flow: 'desc',
    }
  },

  methods: {
    setupExtraColumns(columns) {
      this.extraColumns = columns
    },

    refreshTable(sortBy) {
      this.resetSort(sortBy)
      this.query = ''
      this.recordsPerPage = this.defaultRecordsPerPage
      this.loadRecords()
    },

    resetSort(sortBy = undefined) {
      this.sortBy = sortBy ? sortBy : 'created_at'
      this.flow = 'desc'
    },

    sortRecords(field, sortBy) {
      if (field == this.sortBy) {
        if (this.flow == 'desc') this.resetSort(sortBy)
        else {
          this.sortBy = field
          this.flow = 'desc'
        }
      } else {
        this.sortBy = field
        this.flow = 'asc'
      }

      this.loadRecords()
    }
  }
}
