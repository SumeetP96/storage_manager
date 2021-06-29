import { DateFilterMixin } from "./DateFilterMixin"

export const FilterMixin = {
  mixin: [DateFilterMixin],

  data() {
    return {
      filters: {},
      activeFilters: [],
      createdRange_FILTER: false,
      updatedRange_FILTER: false,
      date_FILTER: false,
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
    addFilter(name, type) {
      // Date range filter
      if (type == 'dateRange') {
        if (this.dbFromDate == '' || this.dbToDate == '') return
        this.filters.date = `${this.getSeperator()}from=${this.dbFromDate}&to=${this.dbToDate}`
      }

      // Created at range filter
      if (type == 'createdRange') {
        if (this.dbCreatedFromDate == '' || this.dbCreatedToDate == '') return
        this.filters.createdAt = `${this.getSeperator()}createdFrom=${this.dbCreatedFromDate}&createdTo=${this.dbCreatedToDate}`
      }

      // Updated at range filter
      if (type == 'updatedRange') {
        if (this.dbUpdatedFromDate == '' || this.dbUpdatedToDate == '') return
        this.filters.updatedAt = `${this.getSeperator()}updatedFrom=${this.dbUpdatedFromDate}&updatedTo=${this.dbUpdatedToDate}`
      }

      // Only / Except filter
      if (type == 'onlyExcept') {
        if (this[`${name}SelectOnly`].length == 0 && this[`${name}SelectExcept`].length == 0) return

        let values = []
        if (this[`${name}SelectOnly`].length > 0) {
          this[`${name}SelectOnly`].forEach(record => values.push(record[name]))
          this.filters[name] = `${this.getSeperator()}${name}Only=${values}`
        }

        if (this[`${name}SelectExcept`].length > 0) {
          this[`${name}SelectExcept`].forEach(record => values.push(record[name]))
          this.filters[name] = `${this.getSeperator()}${name}Except=${values}`
        }
      }

      // Only / Except filter ID
      if (type == 'onlyExceptId') {
        if (this[`${name}SelectOnlyId`].length == 0 && this[`${name}SelectExceptId`].length == 0) return

        let ids = []
        if (this[`${name}SelectOnlyId`].length > 0) {
          this[`${name}SelectOnlyId`].forEach(record => ids.push(record.id))
          this.filters[name] = `${this.getSeperator()}${name}OnlyId=${ids}`
        }

        if (this[`${name}SelectExceptId`].length > 0) {
          this[`${name}SelectExceptId`].forEach(record => ids.push(record.id))
          this.filters[name] = `${this.getSeperator()}${name}ExceptId=${ids}`
        }
      }

      // Less than greater than number
      if (type == 'lessGreat') {
        if (!this[`${name}Lt`] && !this[`${name}Gt`]) return

        if(!isNaN(this[`${name}Lt`])) {
          this.filters[`${name}Lt`] = `${this.getSeperator()}${name}Lt=${this[`${name}Lt`]}`
        }

        if(!isNaN(this[`${name}Gt`])) {
          this.filters[`${name}Gt`] = `${this.getSeperator()}${name}Gt=${this[`${name}Gt`]}`
        }
      }

      // Contains string like
      if (type == 'withWithout') {
        if (!this[name]) return
        this.filters[name] = `${this.getSeperator()}${name}=${this[name]}`
      }

      if (this.activeFilters.indexOf(name) < 0) this.activeFilters.push(name)

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

      if (type == 'updatedRange') {
        this.record.updatedFromDate = ''
        this.record.updatedToDate = ''
        this.dbUpdatedFromDate = ''
        this.dbUpdatedToDate = ''
      }

      if (type == 'createdRange') {
        this.record.createdFromDate = ''
        this.record.createdToDate = ''
        this.dbCreatedFromDate = ''
        this.dbCreatedToDate = ''
      }

      // Only / Except filter
      if (type == 'onlyExcept') {
        this[`${name}SelectOnly`] = []
        this[`${name}SelectExcept`] = []
      }

      // Only / Except Id filter
      if (type == 'onlyExceptId') {
        this[`${name}SelectOnlyId`] = []
        this[`${name}SelectExceptId`] = []
      }

      // Less than greater than number
      if (type == 'lessGreat') {
        this[`${name}Lt`]  = ''
        this[`${name}Gt`]  = ''
      }

      this.filters[name] = ''
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
      for (let prop in this.$data) {
        if (prop.indexOf('_FILTER') >= 0) this[prop] = false
      }
    },

    resetCustomQuery() {
      if (this.apiRoute == 'godowns') {
        const isAccountIndex = this.customQuery.indexOf('is_account')
        if (isAccountIndex >= 0) this.customQuery = 'is_account=' + this.customQuery[isAccountIndex + 11]
      }
      else if (this.apiRoute == 'reports/product_movements') {
        const productIdIndex = this.customQuery.indexOf('product_id')
        const lotNumberIndex = this.customQuery.indexOf('lot_number')
        if (productIdIndex >= 0 && lotNumberIndex >= 0) {
          this.customQuery = 'product_id=' + this.customQuery[productIdIndex + 11] + 'lot_number=' + this.customQuery[productIdIndex + 11]
        }
      }
      else if (this.apiRoute == 'reports/godown_movements') {
        const godownIdIndex = this.customQuery.indexOf('account_id')
        if (godownIdIndex >= 0) this.customQuery = 'account_id=' + this.customQuery[godownIdIndex + 11]
      }
      else if (this.apiRoute == 'reports/agent_transfers') {
        const agentIdIndex = this.customQuery.indexOf('agent_id')
        if (agentIdIndex >= 0) this.customQuery = 'agent_id=' + this.customQuery[agentIdIndex + 9]
      }
      else this.customQuery = ''
    },

    getSeperator() {
      let seperator = ''
      if (this.customQuery[this.customQuery.length -1] != '&') seperator = '&'
      return seperator
    },
  }
}
