import { CrudMixin } from './CrudMixin'
import { DateMixin } from './DateMixin'
import { ThemeMixin } from './ThemeMixin'
import { TableMixin } from './TableMixin'
import { LoaderMixin } from './LoaderMixin'
import { SearchMixin } from './SearchMixin'
import { FilterMixin } from './filter/FilterMixin'
import { CrudHelperMixin } from './CrudHelperMixin'

export const CommonMixin = {
  mixins: [
    DateMixin,
    CrudMixin,
    ThemeMixin,
    TableMixin,
    SearchMixin,
    LoaderMixin,
    FilterMixin,
    CrudHelperMixin,
  ],

  methods: {
    showRecord(routeName, id) {
      this.$router.push({ name: routeName, params: { id: id }})
    },

    formatQuantity(quantity, decimal = 2) {
      let qty = (parseFloat(quantity) / 100).toFixed(decimal)
      return qty
    },

    setFormatQuantity(quantity) {
      if (!quantity) return ''
      let qty = (parseFloat(quantity) * 100).toFixed(0)
      return qty
    },

    hardRedirect(location) {
      window.location.href = location
    }
  }
}
