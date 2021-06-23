<template>
  <div>
    <AppBar backRoute="home" />

    <v-col cols="4" class="mx-auto">

      <v-skeleton-loader v-if="showRecordLoading" v-bind="attrs" class="mt-5"
        type="list-item-three-line, card-heading, list-item-three-line, card-heading, image, actions">
      </v-skeleton-loader>

      <div v-else class="rounded elevation-1 pa-5 mt-8"
        :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">

        <div class="text-h5">Storage Invoice</div>

        <div class="mt-6 subtitle-1">Select month</div>
        <v-combobox v-model="month"
          :items="months"
          label="Month of invoice"
          item-value="id"
          item-text="name"
          clearable
          solo
          dense
        ></v-combobox>

        <!-- PDF -->
        <v-btn dark :disabled="disableExport" class="mt-2"
          @click="disableExportButtons()" color="indigo"
          :href="`/exports/pdf/invoices/${month.id}`"
          download="storage_invoice.pdf">
            <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> Generate Invoice
        </v-btn>
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
  }
}
</script>
