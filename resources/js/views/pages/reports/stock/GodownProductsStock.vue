<template>
  <div>
    <AppBar backRoute="reports.index" />

    <v-col cols="12" lg="10" class="mx-auto">

      <div class="text-h5 py-4">Godown Product Stock</div>

      <v-row align="end">
        <v-col cols="12" sm="12" md="6" class="text-h5 d-flex">
          <v-btn :color="$vuetify.theme.dark ? '' : 'white purple--text'" @click="refreshTable('godownName', sortBy)"
            :loading="refreshLoading" :disabled="records.length == 0">
              <v-icon class="mr-2">mdi-table-refresh</v-icon>
              refresh
          </v-btn>

          <div class="grey--text text--lighten-1 mx-4 font-weight-thin" style="font-size: 1.5rem">|</div>

          <!-- PDF -->
          <v-btn tabindex="-1" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'error--text' : 'white error--text'"
            :href="`/exports/pdf/reports/godown_product_stock?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.pdf`">
              <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
          </v-btn>

          <!-- Excel -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'success--text' : 'white success--text'"
            :href="`/exports/excel/reports/godown_product_stock?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.xlsx`">
              <v-icon class="text-h6 mr-2">mdi-file-excel</v-icon> excel
          </v-btn>

          <!-- Print -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px"
          :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'primary--text' : 'white indigo--text'"
            :disabled="disableExport || records.length == 0" @click="disableExportButtons();
              printPage('all-print', `/exports/print/reports/godown_product_stock?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`)">
              <v-icon class="mr-2">mdi-printer</v-icon> Print
          </v-btn>
          <iframe id="all-print" style="display: none"></iframe>
        </v-col>

        <!-- Search -->
        <v-col cols="12" sm="12" md="3" offset-md="3"
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
                <th class="subtitle-2" :class="sortBy == 'godownName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'godownName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('godownName', 'godownName')">Godown</span>
                    <span v-if="sortBy == 'godownName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Godown filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="godown_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('godown') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="godown_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select only</div>

                            <v-combobox v-model="godownSelectOnlyId"
                              :items="godowns"
                              label="Godowns to show"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              :loading="filterLoading"
                              :disabled="godownSelectExceptId.length > 0 || filterLoading"
                              solo
                              dense
                              class="mt-2"
                            ></v-combobox>

                            <div class="subtitle-2 my-1">Select except</div>

                            <v-combobox v-model="godownSelectExceptId"
                              :items="godowns"
                              :disabled="godownSelectOnlyId.length > 0 || filterLoading"
                              :loading="filterLoading"
                              label="Godowns to hide"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              solo
                              dense
                            ></v-combobox>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('godown', 'onlyExceptId')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('godown', 'onlyExceptId')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Godown filter end -->
                </th>

                <th class="subtitle-2 text-center" :class="sortBy == 'productLotNumber' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'productLotNumber' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('productLotNumber', 'godownName')">Lot number</span>
                    <span v-if="sortBy == 'productLotNumber'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Lot Number filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="800px" :value="lotNumber_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('lotNumber') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="lotNumber_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>

                            <div class="rounded px-4 pb-4 mt-3"
                              :class="$vuetify.theme.dark ? 'grey darken-4' : 'white'">
                              <div class="subtitle-2 pt-3">Select records</div>
                              <v-radio-group v-model="lotNumber" column hide-details>
                                <v-radio label="With lot number" value="with"></v-radio>
                                <v-radio label="Without lot number" value="without"></v-radio>
                              </v-radio-group>
                            </div>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('lotNumber', 'withWithout')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('lotNumber', 'withWithout')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Lot Number filter end -->
                </th>

                <th class="subtitle-2" :class="sortBy == 'productName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'productName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('productName', 'godownName')">Product</span>
                    <span v-if="sortBy == 'productName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Product filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="product_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('product') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="product_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select only</div>

                            <v-combobox v-model="productSelectOnlyId"
                              :items="products"
                              label="Products to show"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              :loading="filterLoading"
                              :disabled="productSelectExceptId.length > 0 || filterLoading"
                              solo
                              dense
                              class="mt-2"
                            ></v-combobox>

                            <div class="subtitle-2 my-1">Select except</div>

                            <v-combobox v-model="productSelectExceptId"
                              :items="products"
                              :disabled="productSelectOnlyId.length > 0 || filterLoading"
                              :loading="filterLoading"
                              label="Products to hide"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              solo
                              dense
                            ></v-combobox>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('product', 'onlyExceptId')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('product', 'onlyExceptId')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Product filter end -->
                </th>

                <th class="subtitle-2 text-right" :class="sortBy == 'compoundStock' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'compoundStock' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('compoundStock', 'godownName')">Compound stock</span>
                    <span v-if="sortBy == 'compoundStock'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-right" :class="sortBy == 'currentStock' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'currentStock' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('currentStock', 'godownName')">Current Stock</span>
                    <span v-if="sortBy == 'currentStock'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

              </tr>
            </thead>
            <tbody v-if="records.length > 0">
              <tr v-for="record in records" :key="record.name">
                  <td class="subtitle-1">{{ record.godownName }}</td>

                  <td class="subtitle-1 text-center font-weight-bold">
                      {{ record.productLotNumber ? record.productLotNumber : '-' }}
                  </td>

                  <td class="subtitle-1">{{ record.productName }}</td>

                  <td class="subtitle-1 text-right font-weight-bold">
                    <span v-if="record.compoundStock < 0" class="error--text">{{ formatQuantity(record.compoundStock) }}</span>
                    <span v-else>{{ record.compoundStock }}</span>
                    <span class="subtitle-2 grey--text pl-1" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">
                        {{ record.compoundUnit }} ({{ formatQuantity(record.packing, 0) }})
                    </span>
                  </td>

                  <td class="subtitle-1 text-right font-weight-bold">
                    <span v-if="record.currentStock < 0" class="error--text">{{ formatQuantity(record.currentStock) }}</span>
                    <span v-else>{{ formatQuantity(record.currentStock) }}</span>
                    <span class="subtitle-2 grey--text pl-1" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">
                        {{ record.productUnit }}
                    </span>
                  </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td :colspan="5" class="text-center subtitle-1">No records found.</td>
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
    this.apiRoute = 'reports/godown_products_stock'

    this.sortBy = 'godownName'

    this.loadRecords()

    this.fetchGodownsWithStock()
    this.fetchGpsProductsWithStock()
  },

  components: {
    AppBar: require('../../../components/AppBar').default
  },

  data() {
    return {
      lotNumber: '',
      lotNumber_FILTER: false,

      godown_FILTER: false,
      godowns: [],
      godownSelectOnlyId: [],
      godownSelectExceptId: [],

      product_FILTER: false,
      products: [],
      productSelectOnlyId: [],
      productSelectExceptId: [],
    }
  },

  methods: {
    fetchGodownsWithStock() {
      this.filterLoading = true
      this.axios.get('/api/autofills/godowns/with_stock')
        .then(response => {
          this.godowns = response.data
          this.filterLoading = false
        })
    },

    fetchGpsProductsWithStock() {
      this.filterLoading = true
      this.axios.get('/api/autofills/godowns/with_stock_products')
        .then(response => {
          this.products = response.data
          this.filterLoading = false
        })
    },
  }
}
</script>
