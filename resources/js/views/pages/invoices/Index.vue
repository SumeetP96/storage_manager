<template>
  <div>
    <AppBar backRoute="home" />

    <v-col cols="6" class="mx-auto">

      <div class="rounded elevation-1 pa-5 mt-8"
        :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'" style="width: 100%">

        <div class="text-h5">Generate storage invoice</div>

        <div class="mt-6 subtitle-1">Godown</div>
        <v-autocomplete
          v-model="godownId"
          hide-details="auto"
          clearable
          solo
          :disabled="godownLoading"
          :loading="godownLoading"
          placeholder="Select godown"
          auto-select-first
          :items="godowns"
          item-text="name"
          item-value="id"
          :class="$vuetify.theme.dark ? 'grey darken-4' : 'white'"
          dense>
        </v-autocomplete>

        <div class="mt-6 subtitle-1">Month</div>
        <v-combobox v-model="month"
          :items="months"
          placeholder="Select month of invoice"
          item-value="id"
          item-text="name"
          clearable
          solo
          dense
        ></v-combobox>

        <div class="overline mt-5 pb-1" :class="$vuetify.theme.dark ? '' : 'error--text text--darken-2'"
          style="font-size: 0.9rem !important">
          only fill if changed
        </div>

        <v-row>
          <v-col cols="4">
            <div class="subtitle-1">Loading</div>
            <v-text-field v-model="customLoading"
            placeholder="0.0"
            type="number"
            solo
            dense>
            </v-text-field>
          </v-col>

          <v-col cols="4">
            <div class="subtitle-1">Unloading</div>
            <v-text-field v-model="customUnloading"
            placeholder="0.0"
            solo
            type="number"
            dense>
            </v-text-field>
          </v-col>

          <v-col cols="4">
            <div class="subtitle-1">Rent</div>
            <v-text-field v-model="customRent"
            placeholder="0.0"
            solo
            type="number"
            dense>
            </v-text-field>
          </v-col>
        </v-row>

        <div class="d-flex justify-space-between mt-5">
          <!-- PDF -->
          <v-btn :dark="$vuetify.theme.dark"
            :disabled="disableExport || !month.id || !godownId"
            @click="disableExportButtons()" color="error"
            :href="`/exports/pdf/invoices/${month.id}/${godownId}?
              cst_loading=${customLoading}&cst_unloading=${customUnloading}&cst_rent=${customRent}`"
            download="storage_invoice.pdf"
            style="width: 125px">
              <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
          </v-btn>

          <!-- Print -->
          <v-btn :dark="$vuetify.theme.dark"
            tabindex="-1" style="width: 125px" color="indigo white--text"
            :disabled="disableExport || !month.id || !godownId"
            @click="disableExportButtons();
            printPage('all-print', `/exports/print/invoices/${month.id}/${godownId}?
              cst_loading=${customLoading}&cst_unloading=${customUnloading}&cst_rent=${customRent}`)">
              <v-icon class="mr-2">mdi-printer</v-icon> Print
          </v-btn>
          <iframe id="all-print" style="display: none"></iframe>
        </div>
      </div>

    </v-col>

  </div>
</template>

<script>
import { CommonMixin } from '../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  components: {
    AppBar: require('../../components/AppBar').default
  },

  data() {
    return {
      customRent: '',
      customLoading: '',
      customUnloading: '',

      godownId: '',
      godowns: [],
      godownLoading: false,

      month: '',
      months: [
        { id: 1, name: 'January' },
        { id: 2, name: 'February' },
        { id: 3, name: 'March' },
        { id: 4, name: 'April' },
        { id: 5, name: 'May' },
        { id: 6, name: 'June' },
        { id: 7, name: 'July' },
        { id: 8, name: 'August' },
        { id: 9, name: 'September' },
        { id: 10, name: 'October' },
        { id: 11, name: 'November' },
        { id: 12, name: 'December' }
      ]
    }
  },

  mounted() {
    this.godownLoading = true
    this.axios.get('/api/autofills/godowns/all_godowns')
      .then(response => {
        this.godowns = response.data
        this.godownLoading = false
      })
  }
}
</script>
