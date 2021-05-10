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

      flow: 'desc',
      sortBy: 'created_at',
    }
  },

  methods: {
    setupExtraColumns(columns) {
      this.extraColumns = columns
    },

    refreshTable(sortBy) {
      this.sortBy = sortBy ? sortBy : 'created_at'
      this.query = ''
      this.recordsPerPage = this.defaultRecordsPerPage
      this.clearFilters()
      this.loadRecords()
    },

    sortRecords(field) {
      if (this.flow == 'desc') {
        this.sortBy = field
        this.flow = 'asc'
      } else {
        this.sortBy = field
        this.flow = 'desc'
      }

      this.loadRecords()
    },
  }
}
