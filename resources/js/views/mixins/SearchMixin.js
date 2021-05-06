export const SearchMixin = {
  data() {
    return {
      query: ''
    }
  },

  methods: {
    searchRecords() {
      this.query.trim()
      this.loadRecords({ search: true })
    },

    clearSearch() {
      this.query = ''
      this.searchRecords()
    }
  }
}
