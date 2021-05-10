import { ThemeMixin } from './ThemeMixin'
import { CrudMixin } from './CrudMixin'
import { DateMixin } from './DateMixin'
import { TableMixin } from './TableMixin'
import { LoaderMixin } from './LoaderMixin'
import { SearchMixin } from './SearchMixin'
import { CrudHelperMixin } from './CrudHelperMixin'
import { FilterMixin } from './FilterMixin'

export const CommonMixin = {
  mixins: [
    ThemeMixin,
    CrudMixin,
    LoaderMixin,
    SearchMixin,
    DateMixin,
    TableMixin,
    CrudHelperMixin,
    FilterMixin,
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
    }
  }
}
