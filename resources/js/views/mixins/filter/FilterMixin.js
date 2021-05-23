import { DateFilterMixin } from "./DateFilterMixin"

export const FilterMixin = {
  mixin: [DateFilterMixin],

  data() {
    return {
      filters: {},
      activeFilters: [],
    }
  },

  methods: {
    /**
     * Fetch records with applied filters
     * Retain main custom query
     */
    filterRecords() {
      this.resetCustomQuery()

      for (const filter in this.filters) {
        if (Object.hasOwnProperty.call(this.filters, filter)) {
          const query = this.filters[filter]
          if (query) this.customQuery += query
        }
      }

      this.loadRecords()
    },


    /**
     * Add filter to list
     * @param {String} name Column name
     */
    addFilter(name, type, propName) {

      // Date range filter
      if (type == 'dateRange') {
        if (this.dbFromDate == '' || this.dbToDate == '') return
        this.filters.date = `${this.getSeperator()}from=${this.dbFromDate}&to=${this.dbToDate}`
      }

      // Only / Except filter
      if (type == 'onlyExcept') {
        if (this[`${name}SelectOnly`].length == 0 && this[`${name}SelectExcept`].length == 0) return

        let ids = []
        if (this[`${name}SelectOnly`].length > 0) {
          this[`${name}SelectOnly`].forEach(record => ids.push(record.id))
          this.filters[name] = `${this.getSeperator()}${name}Only=${ids}`
        }

        if (this[`${name}SelectExcept`].length > 0) {
          this[`${name}SelectExcept`].forEach(record => ids.push(record.id))
          this.filters[name] = `${this.getSeperator()}${name}Except=${ids}`
        }
      }

      if (type == 'withWithout') {
        if (!this[name]) return
        this.filters[name] = `${this.getSeperator()}${name}=${this[name]}`
      }

      if (this.activeFilters.indexOf(name) < 0) this.activeFilters.push(name)
      this[`${name}_FILTER`] = false
      this.filterRecords()
    },


    /**
     * Remove filter from list
     * @param {String} name
     */
    removeFilter(name, type) {
      this.activeFilters = this.activeFilters.filter(filter => { return filter != name })

      if (type == 'dateRange') {
        this.record.fromDate = ''
        this.record.toDate = ''
        this.dbFromDate = ''
        this.dbToDate = ''
      }

      this.filters[name] = ''
      this[`${name}_FILTER`] = false
      this.filterRecords()
    },


    /**
     * Clear all filters
     */
    clearFilters() {
      this.activeFilters = []
      this.filters = {}
      this.resetCustomQuery()
      this.clearFilterVars()
    },


    /**
     * Clear variables used for filtering
     */
    clearFilterVars() {
      this.activeFilters.forEach(name => {
        this[`${name}_FILTER`] = false
      })
    },

    resetCustomQuery() {
      this.customQuery = ''
    },

    getSeperator() {
      let seperator = ''
      if (this.customQuery[this.customQuery.length -1] != '&') seperator = '&'
      return seperator
    },
  }
}
