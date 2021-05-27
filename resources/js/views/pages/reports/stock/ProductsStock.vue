<template>
  <div>
    <AppBar backRoute="reports.index" />

    <v-col cols="12" lg="8" class="mx-auto">

      <div class="text-h5 py-4">Products Stock</div>

      <v-row align="end">
        <v-col cols="12" sm="12" md="6" class="text-h5 d-flex">
          <v-btn :color="$vuetify.theme.dark ? '' : 'white purple--text'" @click="refreshTable('name', sortBy)"
            :loading="refreshLoading" :disabled="records.length == 0">
              <v-icon class="mr-2">mdi-table-refresh</v-icon>
              refresh
          </v-btn>

          <div class="grey--text text--lighten-1 mx-4 font-weight-thin" style="font-size: 1.5rem">|</div>

          <!-- PDF -->
          <v-btn tabindex="-1" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'error--text' : 'white error--text'"
            :href="`/exports/pdf/reports/product_stock?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.pdf`">
              <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
          </v-btn>

          <!-- Excel -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'success--text' : 'white success--text'"
            :href="`/exports/excel/reports/product_stock?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.xlsx`">
              <v-icon class="text-h6 mr-2">mdi-file-excel</v-icon> excel
          </v-btn>

          <!-- Print -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px"
          :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'primary--text' : 'white indigo--text'"
            :disabled="disableExport || records.length == 0" @click="disableExportButtons();
              printPage('all-print', `/exports/print/reports/product_stock?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`)">
              <v-icon class="mr-2">mdi-printer</v-icon> Print
          </v-btn>
          <iframe id="all-print" style="display: none"></iframe>
        </v-col>

        <!-- Search -->
        <v-col cols="12" sm="12" md="4" offset-md="2"
          class="d-flex justify-end">
            <v-text-field
              id="searchInput"
              solo
              dense
              clearable
              hide-details
              v-model="query"
              label="Search records"
              :loading="searchLoading"
              :disabled="searchLoading"
              @click:clear="clearSearch()"
              @keypress.enter="searchRecords()"
              prepend-inner-icon="mdi-magnify">
            </v-text-field>
        </v-col>
      </v-row>

      <div class="mt-3">
        <v-skeleton-loader v-bind="attrs"
          v-if="recordsTableLoading"
          type="table-row-divider@6"
          :class="$vuetify.theme.dark ? '' : 'white'" class="px-4">
        </v-skeleton-loader>

        <v-simple-table v-else class="elevation-1">
          <template v-slot:default>
            <thead>
              <tr>
                <th class="subtitle-2" :class="sortBy == 'name' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'name' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('name', 'name')">Product</span>
                    <span v-if="sortBy == 'name'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-right" :class="sortBy == 'compoundStock' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'compoundStock' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('compoundStock', 'name')">Compound stock</span>
                    <span v-if="sortBy == 'compoundStock'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-right" :class="sortBy == 'stock' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'stock' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('stock', 'name')">Total stock</span>
                    <span v-if="sortBy == 'stock'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

              </tr>
            </thead>
            <tbody v-if="records.length > 0">
              <tr v-for="record in records" :key="record.name">
                  <td class="subtitle-1">{{ record.name }}</td>

                  <td class="subtitle-1 text-right font-weight-bold">
                    <span v-if="record.compoundStock < 0" class="error--text">{{ formatQuantity(record.compoundStock) }}</span>
                    <span v-else>{{ record.compoundStock }}</span>
                    <span class="subtitle-2 grey--text pl-1" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">
                      {{ record.compoundUnit }}
                    </span>
                  </td>

                  <td class="subtitle-1 text-right font-weight-bold">
                    <span v-if="record.stock < 0" class="error--text">{{ formatQuantity(record.stock) }}</span>
                    <span v-else>{{ formatQuantity(record.stock) }}</span>
                    <span class="subtitle-2 grey--text pl-1" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">
                      {{ record.unit }}
                    </span>
                  </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td :colspan="3" class="text-center subtitle-1">No records found.</td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
        <v-skeleton-loader v-bind="attrs" v-if="addRecordsTableLoading" type="table-row-divider@3"
          :class="$vuetify.theme.dark ? '' : 'white'" class="px-4">
        </v-skeleton-loader>

        <div class="d-flex justify-space-between mt-3 ml-3">
          <div v-if="records">
            ( {{ records.length }} of {{ totalRecords }} )
          </div>
          <div class="d-flex justify-end align-center">
            Records per page
            <v-select
              @change="loadRecords()"
              v-model="recordsPerPage"
              :items="perPageItems"
              hide-details
              dense solo
              style="width: 85px"
              class="ml-3 elevation-0 center-input">
            </v-select>

            <v-btn color="indigo white--text" class="ml-5"
              :loading="loadMoreLoading"
              :disabled="totalRecords == records.length"
              @click="loadRecords({ loader: 'addRecordsTableLoading', loadMore: true, reset: false })">
              load more
              <v-icon>mdi-chevron-down</v-icon>
            </v-btn>
          </div>
        </div>
      </div>

    </v-col>

  </div>
</template>

<script>
import { CommonMixin } from '../../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  mounted() {
    this.apiRoute = 'reports/products_stock'

    this.sortBy = 'name'

    this.loadRecords()
  },

  components: {
    AppBar: require('../../../components/AppBar').default
  },
}
</script>
