<template>
  <div>
    <AppBar backRoute="home" />

    <v-col cols="4" class="mx-auto">

      <v-skeleton-loader v-if="showRecordLoading" v-bind="attrs" class="mt-5"
        type="list-item-three-line, card-heading, list-item-three-line, card-heading, image, actions">
      </v-skeleton-loader>

      <div v-else class="rounded elevation-1 pa-5 mt-8"
        :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">

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

        <div class="d-flex justify-space-between mt-2">
          <!-- PDF -->
          <v-btn :dark="$vuetify.theme.dark"
            :disabled="disableExport || !month.id || !godownId"
            @click="disableExportButtons()" color="error"
            :href="`/exports/pdf/invoices/${month.id}/${godownId}`"
            download="storage_invoice.pdf"
            style="width: 125px">
              <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
          </v-btn>

          <!-- Print -->
          <v-btn :dark="$vuetify.theme.dark"
            tabindex="-1" style="width: 125px" color="indigo white--text"
            :disabled="disableExport || !month.id || !godownId"
            @click="disableExportButtons();
            printPage('all-print', `/exports/print/invoices/${month.id}/${godownId}`)">
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
