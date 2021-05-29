<template>
  <div>
    <AppBar backRoute="reports.index" />

    <v-col cols="12" lg="11" class="mx-auto">

      <div class="py-4 text-h5">Account Movement</div>

      <v-row align="start">
        <v-col cols="12" md="8" class="text-h5 d-flex align-start">
          <div style="width: 100%">
            <v-autocomplete
              autofocus
              v-model="accountId"
              hide-details
              clearable
              outlined
              placeholder="Select account"
              auto-select-first
              @input="fetchItemRecords()"
              :items="accounts"
              item-text="name"
              item-value="id"
              prepend-inner-icon="mdi-briefcase-variant-outline"
              class="left-input"
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-autocomplete>
            <div v-if="accountDetails.remarks"
              :class="$vuetify.theme.dark ? '' : 'white'" class="subtitle-2 px-3 py-1 rounded">
                {{ accountDetails.remarks }}
            </div>
          </div>

          <div class="grey--text text--lighten-1 mx-4 font-weight-thin" style="font-size: 1.5rem">|</div>

          <!-- PDF -->
          <v-btn tabindex="-1" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'error--text' : 'white error--text'"
            :href="`/exports/pdf/reports/godown_movement/Account?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            download="account_movements.pdf">
              <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
          </v-btn>

          <!-- Excel -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'success--text' : 'white success--text'"
            :href="`/exports/excel/reports/godown_movement/Account?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            download="account_movements.xlsx">
              <v-icon class="text-h6 mr-2">mdi-file-excel</v-icon> excel
          </v-btn>

          <!-- Print -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px"
            :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'primary--text' : 'white indigo--text'"
            :disabled="disableExport || records.length == 0" @click="disableExportButtons();
              printPage('all-print', `/exports/print/reports/godown_movement/Account?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`)">
              <v-icon class="mr-2">mdi-printer</v-icon> Print
          </v-btn>
          <iframe id="all-print" style="display: none"></iframe>

        </v-col>

        <!-- Search -->
        <v-col cols="12" md="4" class="d-flex justify-end align-center">
            <v-btn class="mr-2" :color="$vuetify.theme.dark ? '' : 'white purple--text'"
               @click="resetDates(); refreshTable('date', `account_id=${accountId}`);"
                :loading="refreshLoading" :disabled="records.length == 0">
                  <v-icon class="mr-2">mdi-table-refresh</v-icon>
                  refresh
            </v-btn>

            <v-text-field
              id="searchInput"
              solo
              dense
              clearable
              hide-details
              v-model="query"
              label="Search records"
              :loading="searchLoading"
              :disabled="searchLoading || !accountId"
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
                <th class="subtitle-2 text-center" :class="sortBy == 'date' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'date' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('date', sortBy)">Date</span>
                    <span v-if="sortBy == 'date'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Date filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="250px" :value="date_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('date') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="date_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Date range</div>

                            <v-text-field
                              v-model="record.fromDate"
                              hide-details
                              solo
                              :loading="filterLoading"
                              :disabled="filterLoading"
                              placeholder="From date"
                              @blur="formatDate('fromDate'); dbFromDate = flipToYMD(record.fromDate);"
                              prepend-inner-icon="mdi-calendar"
                              :class="$vuetify.theme.dark ? '' : 'white'"
                              class="center-input mt-3"
                              dense>
                            </v-text-field>

                            <v-text-field
                              v-model="record.toDate"
                              hide-details
                              solo
                              :disabled="filterLoading"
                              :loading="filterLoading"
                              placeholder="To date"
                              @blur="formatDate('toDate'); dbToDate = flipToYMD(record.toDate);"
                              prepend-inner-icon="mdi-calendar"
                              :class="$vuetify.theme.dark ? '' : 'white'"
                              class="center-input mt-2"
                              dense>
                            </v-text-field>

                            <div class="d-flex justify-space-between align-center mt-5 mb-1">
                              <v-btn dark small @click="removeFilter('date', 'dateRange')" tabindex="-1" :loading="filterLoading">
                                <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('date', 'dateRange')" :loading="filterLoading">
                                <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Date filter end -->
                </th>

                <th class="subtitle-2 text-center" :class="sortBy == 'transferType' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'transferType' ? 'font-size: 1rem !important' : ''" style="width: 12%">
                    <span class="sort-link" @click="sortRecords('transferType', sortBy)">Transfer type</span>
                    <span v-if="sortBy == 'transferType'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Transfer type filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="transferType_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('transferType') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="transferType_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select only</div>

                            <v-combobox v-model="transferTypeSelectOnlyId"
                              :items="transferTypes"
                              label="Accounts to show"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              :loading="filterLoading"
                              :disabled="transferTypeSelectExceptId.length > 0 || filterLoading"
                              solo
                              dense
                              class="mt-2"
                            ></v-combobox>

                            <div class="subtitle-2 my-1">Select except</div>

                            <v-combobox v-model="transferTypeSelectExceptId"
                              :items="transferTypes"
                              :disabled="transferTypeSelectOnlyId.length > 0 || filterLoading"
                              :loading="filterLoading"
                              label="Accounts to hide"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              solo
                              dense
                            ></v-combobox>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('transferType', 'onlyExceptId')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('transferType', 'onlyExceptId')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Transfer type filter end -->
                </th>

                <th class="subtitle-2" :class="sortBy == 'name' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'name' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('name', sortBy)">Godown</span>
                    <span v-if="sortBy == 'name'">
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
                              label="Godown to show"
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
                              label="Godown to hide"
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

                <th class="subtitle-2 text-center" :class="sortBy == 'lotNumber' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'lotNumber' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('lotNumber', sortBy)">Lot number</span>
                    <span v-if="sortBy == 'lotNumber'">
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
                    <span class="sort-link" @click="sortRecords('productName', sortBy)">Product</span>
                    <span v-if="sortBy == 'productName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Product filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="filterProduct_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('filterProduct') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="filterProduct_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select only</div>

                            <v-combobox v-model="filterProductSelectOnlyId"
                              :items="filterProducts"
                              label="Products to show"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              :loading="filterLoading"
                              :disabled="filterProductSelectExceptId.length > 0 || filterLoading"
                              solo
                              dense
                              class="mt-2"
                            ></v-combobox>

                            <div class="subtitle-2 my-1">Select except</div>

                            <v-combobox v-model="filterProductSelectExceptId"
                              :items="filterProducts"
                              :disabled="filterProductSelectOnlyId.length > 0 || filterLoading"
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
                              <v-btn dark small @click="removeFilter('filterProduct', 'onlyExceptId')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('filterProduct', 'onlyExceptId')"
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

                <th class="subtitle-2 text-right" :class="sortBy == 'compoundQuantity' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'compoundQuantity' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('compoundQuantity', sortBy)">Compound</span>
                    <span v-if="sortBy == 'compoundQuantity'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-right" :class="sortBy == 'quantity' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'quantity' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('quantity', sortBy)">Quantity</span>
                    <span v-if="sortBy == 'quantity'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

              </tr>
            </thead>
            <tbody v-if="records.length > 0">
              <tr v-for="record in records" :key="record.id">
                  <td class="subtitle-1 text-center font-weight-bold">{{ record.date | moment('DD/MM/YYYY') }}</td>

                  <td class="subtitle-1 text-center">
                    <v-btn @click="viewRecordDialog = true; loadRecordDialog(record.id, record.ttid, record.productId)"
                      v-if="record.ttid == 1" text width="100%"
                      :color="$vuetify.theme.dark ? 'primary' : 'indigo lighten-1'">
                        {{ record.transferType }}
                    </v-btn>

                    <v-btn @click="viewRecordDialog = true; loadRecordDialog(record.id, record.ttid, record.productId)"
                      v-if="record.ttid == 2" text width="100%"
                      :color="$vuetify.theme.dark ? 'orange' : 'orange darken-2'">
                        {{ record.transferType }}
                    </v-btn>

                    <v-btn @click="viewRecordDialog = true; loadRecordDialog(record.id, record.ttid, record.productId)"
                      v-if="record.ttid == 3" text width="100%" color="success">
                      {{ record.transferType }}
                    </v-btn>
                  </td>

                  <td class="subtitle-1">{{ record.name }}</td>

                  <td class="subtitle-1 text-center">{{ record.lotNumber }}</td>
                  <td class="subtitle-1">{{ record.productName }}</td>

                  <td v-if="record.compoundQuantity" class="subtitle-1 text-right font-weight-bold">
                    <span v-if="record.ttid == 2" class="success--text">
                      + {{ formatQuantity(record.compoundQuantity) }}
                    </span>
                    <span v-if="record.ttid == 3" class="error--text">
                      - {{ formatQuantity(record.compoundQuantity) }}
                    </span>

                    <span class="subtitle-2 grey--text pl-1" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">
                      {{ record.compoundUnit }} ({{ formatQuantity(record.packing, 0) }})
                    </span>
                  </td>
                  <td v-else class="text-right pr-5">-</td>

                  <td class="subtitle-1 text-right font-weight-bold">
                    <span v-if="record.ttid == 2" class="success--text">
                      + {{ formatQuantity(record.quantity) }}
                    </span>
                    <span v-if="record.ttid == 3" class="error--text">
                      - {{ formatQuantity(record.quantity) }}
                    </span>

                    <span class="subtitle-2 grey--text pl-1" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">
                      {{ record.unit }}
                    </span>
                  </td>

              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td :colspan="7" class="text-center subtitle-1">No records found.</td>
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

    <v-dialog v-model="viewRecordDialog" max-width="1200">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>{{ recordType }} Details</div>
          <v-btn icon @click="viewRecordDialog = false"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <v-card-text class="pt-6 pb-10">
          <v-row>
            <v-col cols="12" md="7" class="pr-10">
              <div class="overline pl-2 grey--text mb-2" style="font-size: 0.9rem !important">Transfer details</div>
              <table class="view-record-table">
                <tr>
                  <td class="subtitle-1 font-weight-bold text-left" style="width: 25%">Date</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.date | moment('DD/MM/YYYY dddd') }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">From</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.fromName }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">To</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.toName }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Products</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">

                    <table class="inter-view-table">
                      <tr v-for="(product, index) in recordProducts" :key="index">
                        <td style="border-top-left-radius: 5px; border-bottom-left-radius: 5px;"
                          :class="product.productId == productId ? 'yellow lighten-3 black--text' : ''">
                            <span>{{ product.name }}</span>
                        </td>
                        <td class="text-center"
                          :class="product.productId == productId ? 'yellow lighten-3 black--text' : ''">
                          <span v-if="product.lotNumber" class="subtitle-2" :class="$vuetify.theme.dark ? '' : 'text--darken-2'">
                            ( {{ product.lotNumber }} )
                          </span>
                          <span v-else>-</span>
                        </td>
                        <td class="text-right" style="border-top-right-radius: 5px; border-bottom-right-radius: 5px;"
                          :class="product.productId == productId ? 'yellow lighten-3 black--text' : ''">
                          <span class="font-weight-bold">{{ formatQuantity(product.quantity) }}</span>
                          <span class="ml-1 subtitle-2" :class="$vuetify.theme.dark ? '' : 'text--darken-2'">
                            {{ product.unit }}
                          </span>
                        </td>
                      </tr>
                    </table>

                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Modified on</td>
                  <td class="subtitle-1">{{ dbRecord.updated_at | moment('dddd, DD/MM/YYYY - LTS') }}</td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Created on</td>
                  <td class="subtitle-1">{{ dbRecord.created_at | moment('dddd, DD/MM/YYYY - LTS') }}</td>
                </tr>
              </table>
            </v-col>

            <v-col cols="12" md="5">
              <div class="overline pl-2 grey--text mb-2" style="font-size: 0.9rem !important">Additional details</div>
              <table class="view-record-table">
                <tr>
                  <td class="subtitle-1 font-weight-bold text-left" style="width: 35%">Agent</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.agentName ? dbRecord.agentName : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Order no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.order_no ? dbRecord.order_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Invoice no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.invoice_no ? dbRecord.invoice_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Eway bill no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.eway_bill_no ? dbRecord.eway_bill_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Delivery slip no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.delivery_slip_no ? dbRecord.delivery_slip_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Transport details</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.transport_details ? dbRecord.transport_details : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Additional remarks</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.remarks ? dbRecord.remarks : '-' }}
                  </td>
                </tr>
              </table>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

  </div>
</template>

<script>
import { CommonMixin } from '../../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  data() {
    return {
      dbRecord: {},

      recordType: '',

      productId: '',
      accountId: 0,
      accounts: [],
      accountDetails: {},

      lotNumber: '',
      lotNumber_FILTER: false,

      transferType_FILTER: false,
      transferTypes: [],
      transferTypeSelectOnlyId: [],
      transferTypeSelectExceptId: [],

      filterProduct_FILTER: false,
      filterProducts: [],
      filterProductSelectOnlyId: [],
      filterProductSelectExceptId: [],

      godown_FILTER: false,
      godowns: [],
      godownSelectOnlyId: [],
      godownSelectExceptId: [],
    }
  },

  mounted() {
    this.apiRoute = 'reports/godown_movements'
    this.customQuery = `account_id=${this.accountId}`
    this.sortBy = 'date'

    this.axios.get('/api/godowns/autocomplete_with_transfers')
      .then(response => this.accounts = response.data.records)

    if (this.accounts) this.loadRecords()
  },

  components: {
    AppBar: require('../../../components/AppBar').default
  },

  methods: {
    fetchItemRecords() {
      this.axios.get(`/api/godowns/${this.accountId}/details`)
        .then(response => this.accountDetails = response.data)

      this.customQuery = `account_id=${this.accountId}`
      this.loadRecords()
      this.fetchGodownAutofill()
      this.fetchProductAutofill()
      this.fetchTransferTypesGodown()
    },

    loadRecordDialog(accountId, transferTypeId, productId) {
      let apiRoute = ''

      this.dbRecord = {}
      this.productId = productId

      if (transferTypeId == 1) {
        this.recordType = 'Inter Godown'
        apiRoute = 'inter_godowns'
      }
      if (transferTypeId == 2) {
        this.recordType = 'Purchase'
        apiRoute = 'purchases'
      }
      if (transferTypeId == 3) {
        this.recordType = 'Sale'
        apiRoute = 'sales'
      }

      this.axios
        .get(`/api/${apiRoute}/${accountId}/show`)
        .then(response => {
          this.dbRecord = response.data.record

          this.axios.get(`/api/${apiRoute}/transfer_products/${accountId}`)
            .then(response => this.recordProducts = response.data.record)
        })
    },

    fetchTransferTypesGodown() {
      this.axios
        .get(`/api/autofills/transfer_types/used_by_godowns/${this.accountId}`)
        .then(response => this.transferTypes = response.data)
    },

    fetchGodownAutofill() {
      this.filterLoading = true
      this.axios.get(`/api/autofills/godowns/used_by_godowns/${this.accountId}`)
        .then(response => {
          this.godowns = response.data
          this.filterLoading = false
        })
    },

    fetchProductAutofill() {
      this.filterLoading = true
      this.axios.get(`/api/autofills/products/used_by_godowns/${this.accountId}`)
        .then(response => {
          this.filterProducts = response.data
          this.filterLoading = false
        })
    },
  }
}
</script>

<style scoped>
  .right-input >>> input {
    text-align: right
  }

  .center-input >>> input {
    text-align: center;
    padding-left: 2px;
  }

  .left-input >>> input {
    padding-left: 10px;
  }
</style>
