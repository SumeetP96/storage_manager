import { CrudMixin } from './CrudMixin'
import { PageMixin } from './PageMixin'
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
    PageMixin,
    ThemeMixin,
    TableMixin,
    SearchMixin,
    LoaderMixin,
    FilterMixin,
    CrudHelperMixin,
  ],

  data() {
    return {
      disableExport: false
    }
  },

  methods: {
    disableExportButtons(duration = 5) {
      this.disableExport = true
      setTimeout(() => {
        this.disableExport = false
      }, duration * 1000);
    },

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

    formatEwayNo(number) {
      return (number.toString()).replace(/^(.{4})(.{4})(.*)$/, "$1 $2 $3");
    }
  },
}
