export const FilterMixin = {
  data() {
    return {
      filters: {},
      activeFilters: [],

      dateFilter: false,
      fromFilter: false,
      toFilter: false,
      productFilter: false,
      lotFilter: false,
      unitFilter: false,
      quantityFilter: false,

      fromAutofill: [],
      fromSelectOnly: [],
      fromSelectExcept: [],

      toAutofill: [],
      toSelectOnly: [],
      toSelectExcept: [],

      productAutofill: [],
      productSelectOnly: [],
      productSelectExcept: [],

      lotAutofill: [],
      lotSelectOnly: [],
      lotSelectExcept: [],
      lotNull: false,

      unitAutofill: [],
      unitSelectOnly: [],
      unitSelectExcept: [],

      quantityLt: '',
      quantityGt: '',

      // Old
      lastFrom: '',
      lastTo: '',

      dbFromDate: '',
      dbToDate: '',
    }
  },

  methods: {
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

    clearFilters() {
      this.activeFilters = []
      this.filters = {}
      this.resetCustomQuery()
      this.clearFilterVars()
    },

    clearFilterVars() {
      this.dateFilter = false
      this.fromFilter = false
      this.toFilter = false
      this.productFilter = false
      this.lotFilter = false
      this.unitFilter = false
      this.quantityFilter = false
    },

    resetCustomQuery() {
      this.customQuery = ''
      let isAccount = this.customQuery.indexOf('is_account')
      if (isAccount >= 0) this.customQuery = 'is_account=' + this.customQuery[this.customQuery.indexOf('is_account') + 11]
    },

    addFilter(name) {
      if (name == 'date') {
        if (this.dbFromDate == '' || this.dbToDate == '') return
        this.filters.date = `${this.getSeperator()}from=${this.dbFromDate}&to=${this.dbToDate}`
        if (this.activeFilters.indexOf('date') < 0) this.activeFilters.push('date')
        this[`${name}Filter`] = false
      }

      if (name == 'from') {
        if (this.fromSelectOnly.length == 0 && this.fromSelectExcept.length == 0) return

        let ids = []
        if (this.fromSelectOnly.length > 0) {
          this.fromSelectOnly.forEach(record => ids.push(record.id))
          this.filters.fromOnlyIds = `${this.getSeperator()}fromOnlyIds=${ids.toString()}`
        }
        if (this.fromSelectExcept.length > 0) {
          this.fromSelectExcept.forEach(record => ids.push(record.id))
          this.filters.fromExceptIds = `${this.getSeperator()}fromExceptIds=${ids.toString()}`
        }
        if (this.activeFilters.indexOf('from') < 0) this.activeFilters.push('from')
        this[`${name}Filter`] = false
      }

      if (name == 'to') {
        if (this.toSelectOnly.length == 0 && this.toSelectExcept.length == 0) return

        let ids = []
        if (this.toSelectOnly.length > 0) {
          this.toSelectOnly.forEach(record => ids.push(record.id))
          this.filters.toOnlyIds = `${this.getSeperator()}toOnlyIds=${ids.toString()}`
        }
        if (this.toSelectExcept.length > 0) {
          this.toSelectExcept.forEach(record => ids.push(record.id))
          this.filters.toExceptIds = `${this.getSeperator()}toExceptIds=${ids.toString()}`
        }
        if (this.activeFilters.indexOf('to') < 0) this.activeFilters.push('to')
        this[`${name}Filter`] = false
      }

      if (name == 'product') {
        if (this.productSelectOnly.length == 0 && this.productSelectExcept.length == 0) return

        let ids = []
        if (this.productSelectOnly.length > 0) {
          this.productSelectOnly.forEach(record => ids.push(record.id))
          this.filters.productOnlyIds = `${this.getSeperator()}productOnlyIds=${ids.toString()}`
        }
        if (this.productSelectExcept.length > 0) {
          this.productSelectExcept.forEach(record => ids.push(record.id))
          this.filters.productExceptIds = `${this.getSeperator()}productExceptIds=${ids.toString()}`
        }
        if (this.activeFilters.indexOf('product') < 0) this.activeFilters.push('product')
        this[`${name}Filter`] = false
      }

      if (name == 'lot') {
        if (this.lotSelectOnly.length == 0 && this.lotSelectExcept.length == 0 && !this.lotNull) return

        if (this.lotNull) {
          this.filters.lotNull = `${this.getSeperator()}lotNull=${true}`
        } else {
          let lots = []
          if (this.lotSelectOnly.length > 0) {
            this.lotSelectOnly.forEach(record => lots.push(record.lot_number))
            this.filters.lotSelect = `${this.getSeperator()}lotOnly=${lots.toString()}`
          }
          if (this.lotSelectExcept.length > 0) {
            this.lotSelectExcept.forEach(record => lots.push(record.lot_number))
            this.filters.lotExcept = `${this.getSeperator()}lotExcept=${lots.toString()}`
          }
        }

        if (this.activeFilters.indexOf('lot') < 0) this.activeFilters.push('lot')
        this[`${name}Filter`] = false
      }

      if (name == 'unit') {
        if (this.unitSelectOnly.length == 0 && this.unitSelectExcept.length == 0) return

        let units = []
        if (this.unitSelectOnly.length > 0) {
          this.unitSelectOnly.forEach(record => units.push(record.unit))
          this.filters.unitOnly = `${this.getSeperator()}unitOnly=${units.toString()}`
        }
        if (this.unitSelectExcept.length > 0) {
          this.unitSelectExcept.forEach(record => units.push(record.unit))
          this.filters.unitExcept = `${this.getSeperator()}unitExcept=${units.toString()}`
        }
        if (this.activeFilters.indexOf('unit') < 0) this.activeFilters.push('unit')
        this[`${name}Filter`] = false
      }

      if (name == 'quantity') {
        if (isNaN(this.quantityGt) || isNaN(this.quantityLt)) return
        if (this.quantityGt <= 0 && this.quantityLt <= 0) return

        if (this.quantityGt > 0) {
          this.filters.quantityGt = `${this.getSeperator()}quantityGt=${this.quantityGt}`
        }
        if (this.quantityLt > 0) {
          this.filters.quantityLt = `${this.getSeperator()}quantityLt=${this.quantityLt}`
        }

        if (this.activeFilters.indexOf('quantity') < 0) this.activeFilters.push('quantity')
        this[`${name}Filter`] = false
      }

      this.filterRecords()
    },

    removeFilter(name) {
      this.activeFilters = this.activeFilters.filter(filter => { return filter != name })

      if (name == 'date') {
        this.record.fromDate = ''
        this.record.toDate = ''
        this.dbFromDate = ''
        this.dbToDate = ''
        this.filters.date = ''
        this[`${name}Filter`] = false
      }

      if (name == 'from') {
        this.fromSelectOnly = []
        this.fromSelectExcept = []
        this.filters.fromOnlyIds = ''
        this.filters.fromExceptIds = ''
        this[`${name}Filter`] = false
      }

      if (name == 'to') {
        this.toSelectOnly = []
        this.toSelectExcept = []
        this.filters.toOnlyIds = ''
        this.filters.toExceptIds = ''
        this[`${name}Filter`] = false
      }

      if (name == 'product') {
        this.productSelectOnly = []
        this.productSelectExcept = []
        this.filters.productOnlyIds = ''
        this.filters.productExceptIds = ''
        this[`${name}Filter`] = false
      }

      if (name == 'lot') {
        this.lotNull = false
        this.lotSelectOnly = []
        this.lotSelectExcept = []
        this.filters.lotNull = false
        this.filters.lotOnly = ''
        this.filters.lotExcept = ''
        this[`${name}Filter`] = false
      }

      if (name == 'unit') {
        this.unitSelectOnly = []
        this.unitSelectExcept = []
        this.filters.unitNull = false
        this.filters.unitOnly = ''
        this.filters.unitExcept = ''
        this[`${name}Filter`] = false
      }

      if (name == 'quantity') {
        this.quantityGt = ''
        this.quantityLt = ''
        this.filters.quantityGt = ''
        this.filters.quantityLt = ''
        this[`${name}Filter`] = false
      }

      this.filterRecords()
    },

    getSeperator() {
      let seperator = ''
      if (this.customQuery[this.customQuery.length -1] != '&') seperator = '&'
      return seperator
    },


    // Old
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
